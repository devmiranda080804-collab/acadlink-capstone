<?php

namespace App\Support;

use App\Models\AnnouncementProgram;
use App\Models\CalendarActivity;
use App\Models\ProgramAssignment;
use App\Models\SharedResource;
use App\Models\Submission;
use App\Models\SubmissionRequirement;
use App\Models\TemplateDocument;
use App\Models\TemplateDocumentProgram;
use App\Models\User;

// What each person hears about in their bell. Raised from the records themselves, so every
// way of making the change (upload, editor, new version, ...) notifies the same people.
class SystemNotifications
{
    public static function register(): void
    {
        // Seeders and console commands make records too; nobody needs to hear about those.
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        // Templates: the Dean hears about new ones to review; the Secretary hears the decision.
        TemplateDocument::created(function (TemplateDocument $t) {
            if ($t->isPending()) {
                self::templateToReview($t);
            }
        });
        TemplateDocument::updated(function (TemplateDocument $t) {
            if (!$t->wasChanged('status')) {
                return;
            }
            if ($t->isPending()) {
                self::templateToReview($t);
            } elseif ($t->isReturned()) {
                Notify::send(User::find($t->created_by), 'Template returned: ' . $t->title,
                    Notify::actor() . ' returned v' . $t->version . ': ' . $t->return_reason,
                    '/secretary/template-distribution', 'return', "tpl-returned:{$t->id}");
            } elseif ($t->isApproved()) {
                Notify::send(User::find($t->created_by), 'Template approved: ' . $t->title,
                    Notify::actor() . ' approved v' . $t->version . '.',
                    '/secretary/template-distribution', 'check', "tpl-approved:{$t->id}");
            }
        });

        // A template sent to a program reaches its Program Head and faculty.
        TemplateDocumentProgram::saved(function (TemplateDocumentProgram $row) {
            if (!$row->distributed_at || !($row->wasRecentlyCreated || $row->wasChanged('distributed_at'))) {
                return;
            }
            $t = $row->document;
            Notify::send(Notify::programHeads($row->program), 'New template for ' . $row->program . ': ' . $t->title,
                'The Dean sent ' . TemplateDocument::typeLabel($t->type) . ' v' . $t->version . ' to your program.',
                '/program-head/template-review', 'template', "tpl-sent:{$t->id}:{$row->program}");
            Notify::send(Notify::faculty($row->program), 'New template: ' . $t->title,
                TemplateDocument::typeLabel($t->type) . ' v' . $t->version . ' is ready in Templates. Make a copy to edit it.',
                '/faculty/my-template', 'template', "tpl-sent:{$t->id}");
        });

        // Submissions and Deadline.
        SubmissionRequirement::created(function (SubmissionRequirement $req) {
            Notify::send(Notify::faculty($req->program), 'New requirement: ' . $req->title,
                'Due ' . $req->deadline->format('M j, Y') . ($req->per_course ? ', one file for each course you teach.' : '.'),
                '/faculty/submissions', 'calendar', "req:{$req->id}");
        });

        Submission::saved(function (Submission $s) {
            $req = $s->requirement;
            if (!$req) {
                return;
            }
            $course = $s->course ? ' for ' . $s->course->code : '';

            // A new file: the first submission, or one that replaces it.
            if ($s->wasRecentlyCreated || $s->wasChanged('file_path')) {
                $who = $s->faculty->name ?? 'A faculty member';
                Notify::send(Notify::programHeads($req->program), $who . ' submitted ' . $req->title . $course,
                    ($s->status === 'late' ? 'Submitted late. ' : '') . $s->file_name,
                    '/program-head/submissions', 'upload', "sub:{$s->id}:" . md5((string) $s->file_path));
                if ($req->per_course && $s->course_id) {
                    Notify::send(Notify::secretaries(), 'To check: ' . $req->title . $course,
                        $who . ' submitted a file for Course Filing.',
                        '/secretary/course-filing', 'upload', "sub-check:{$s->id}:" . md5((string) $s->file_path));
                }
            }

            if ($s->wasChanged('filing_status') && $s->filing_status) {
                $returned = $s->filing_status === Submission::FILING_RETURNED;
                Notify::send(User::find($s->faculty_id),
                    ($returned ? 'Returned: ' : 'Validated: ') . $req->title . $course,
                    $returned ? Notify::actor() . ': ' . $s->filing_return_reason : 'Checked and validated by ' . Notify::actor() . '.',
                    '/faculty/submissions', $returned ? 'return' : 'check', "sub-review:{$s->id}:{$s->filing_status}:" . optional($s->filing_checked_at)->timestamp . ":" . md5((string) $s->file_path));
            }
        });

        // Course assignments.
        ProgramAssignment::created(function (ProgramAssignment $a) {
            $course = $a->course;
            Notify::send(User::find($a->faculty_id), 'Assigned to ' . ($course->code ?? 'a course'),
                ($course->title ?? '') . ' · ' . $a->semester . ', A.Y. ' . $a->school_year,
                '/faculty/course-coordination', 'course', "assign:{$a->id}");
        });

        // Announcements reach the programs they were posted for.
        AnnouncementProgram::created(function (AnnouncementProgram $row) {
            $ann = $row->announcement;
            $poster = $ann?->user;
            if (!$ann) {
                return;
            }
            $people = Notify::faculty($row->program);
            if (in_array($poster?->role, ['admin', 'secretary'], true)) {
                $people = $people->merge(Notify::programHeads($row->program));
            }
            Notify::send($people, 'Announcement: ' . $ann->title, 'Posted by ' . ($poster->name ?? 'the college') . '.',
                Notify::rolePath('/announcements'), 'megaphone', "ann:{$ann->id}");
        });

        // Calendar of Activities: everyone, or only faculty for a faculty activity.
        CalendarActivity::created(function (CalendarActivity $activity) {
            $people = User::whereNull('archived_at')->whereIn('role', $activity->category === 'faculty' ? ['faculty'] : ['admin', 'secretary', 'program_head', 'faculty'])->get();
            Notify::send($people, 'New activity: ' . $activity->title,
                \Illuminate\Support\Carbon::parse($activity->activity_date)->format('M j, Y') . ($activity->location ? ' · ' . $activity->location : ''),
                Notify::rolePath('/calendar'), 'calendar', "cal:{$activity->id}");
        });

        // Shared Library: the course's faculty, or the program's for a whole-program resource.
        SharedResource::created(function (SharedResource $r) {
            $people = $r->course_id ? Notify::courseFaculty($r->course_id) : Notify::faculty($r->program);
            $where = $r->course ? $r->course->code : $r->program;
            Notify::send($people, 'Shared with ' . $where . ': ' . $r->title,
                ($r->sharer->name ?? 'A faculty member') . ' shared it in the Shared Library.' . ($r->course_id && $r->isEditableFile() ? ' You can edit it together.' : ''),
                '/faculty/shared-library', 'library', "shared:{$r->id}");
        });
    }

    protected static function templateToReview(TemplateDocument $t): void
    {
        Notify::send(Notify::admins(), 'Template to review: ' . $t->title,
            (User::find($t->created_by)?->name ?? 'The Secretary') . ' submitted v' . $t->version . ' for your approval.',
            '/admin/template-approvals/' . $t->id . '/review', 'template', "tpl-pending:{$t->id}");
    }

    // Reminders before a requirement is due and once it is overdue, sent when a faculty
    // member opens a page (at most hourly), so no scheduled job is needed.
    public static function remindDeadlines(User $faculty): void
    {
        if ($faculty->role !== 'faculty' || !\Illuminate\Support\Facades\Cache::add('deadline-check:' . $faculty->id, 1, 3600)) {
            return;
        }

        try {
            foreach (SubmissionRequirement::whereIn('program', $faculty->programCodes())->get() as $req) {
                if (!($req->is_overdue || $req->is_due_soon) || !$req->isMissingFor($faculty)) {
                    continue;
                }
                $overdue = $req->is_overdue;
                Notify::send($faculty,
                    ($overdue ? 'Overdue: ' : 'Due soon: ') . $req->title,
                    $overdue ? 'It was due ' . $req->deadline->format('M j, Y') . '. Submit it as soon as you can.' : 'Due ' . $req->deadline->format('M j, Y') . '.',
                    '/faculty/submissions', 'clock', ($overdue ? 'req-overdue:' : 'req-soon:') . $req->id, true);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
