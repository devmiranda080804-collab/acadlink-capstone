<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\ProgramHead\AccountManagementController as PHAccountManagementController;
use App\Http\Controllers\Secretary\AccountManagementController as SecAccountManagementController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Admin\RolesPermissionsController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\ProgramHead\AnnouncementController as PHAnnouncementController;
use App\Http\Controllers\Secretary\AnnouncementController as SecAnnouncementController;
use App\Http\Controllers\Faculty\AnnouncementController as FacultyAnnouncementController;
use App\Http\Controllers\ProgramHead\CourseOversightController;
use App\Http\Controllers\Faculty\CourseCoordinationController;
use App\Http\Controllers\Faculty\CollaborationController;
use App\Http\Controllers\Faculty\TemplateController;
use App\Http\Controllers\ProgramHead\TemplateReviewController;
use App\Http\Controllers\Admin\TemplateApprovalController;
use App\Http\Controllers\Secretary\TemplateDistributionController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProgramHead\DashboardController as PHDashboardController;
use App\Http\Controllers\Secretary\DashboardController as SecDashboardController;
use App\Http\Controllers\Faculty\DashboardController as FacultyDashboardController;
use App\Http\Controllers\Secretary\DocumentRepositoryController;
use App\Http\Controllers\Secretary\CourseFilingController;
use App\Http\Controllers\Faculty\SharedLibraryController;
use App\Http\Controllers\ProgramHead\SubmissionController as PHSubmissionController;
use App\Http\Controllers\Faculty\SubmissionController as FacultySubmissionController;
use App\Http\Controllers\Faculty\ExamGeneratorController;
use App\Http\Controllers\Faculty\ContentModuleController;
use App\Http\Controllers\ProgramAssignmentController;
use App\Http\Controllers\Admin\AuditLogController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');

// Reset Password
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');

// Change Password
Route::get('/change-password', [ChangePasswordController::class, 'show']);
Route::post('/change-password', [ChangePasswordController::class, 'update']);

/*
|--------------------------------------------------------------------------
| Faculty
|--------------------------------------------------------------------------
*/

Route::middleware('role:faculty')->prefix('faculty')->group(function () {
    Route::get('/dashboard', [FacultyDashboardController::class, 'index']);
    Route::get('/my-template', [TemplateController::class, 'index']);
    Route::post('/my-template/{template}/copy', [TemplateController::class, 'makeCopy']);
    Route::get('/exam-generator', [ExamGeneratorController::class, 'index']);
    Route::post('/exam-generator', [ExamGeneratorController::class, 'store']);
    Route::get('/exam-generator/{exam}', [ExamGeneratorController::class, 'show']);
    Route::put('/exam-generator/{exam}', [ExamGeneratorController::class, 'update']);
    Route::get('/exam-generator/{exam}/tos', [ExamGeneratorController::class, 'tos']);
    Route::post('/exam-generator/{exam}/finalize', [ExamGeneratorController::class, 'finalize']);
    Route::delete('/exam-generator/{exam}', [ExamGeneratorController::class, 'destroy']);
    Route::get('/shared-library', [SharedLibraryController::class, 'index']);
    Route::post('/shared-library', [SharedLibraryController::class, 'store']);
    Route::delete('/shared-library/{resource}', [SharedLibraryController::class, 'destroy']);
    Route::get('/course-coordination', [CourseCoordinationController::class, 'index']);
    Route::get('/analytics', fn () => view('faculty.analytics'));
    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::get('/announcements', [FacultyAnnouncementController::class, 'index']);
    Route::get('/submissions', [FacultySubmissionController::class, 'index']);
    Route::post('/submissions/{requirement}', [FacultySubmissionController::class, 'store']);
    Route::get('/cms', [ContentModuleController::class, 'index']);
    Route::post('/cms', [ContentModuleController::class, 'store']);
    Route::put('/cms/{module}', [ContentModuleController::class, 'update']);
    Route::delete('/cms/{module}', [ContentModuleController::class, 'destroy']);

    // Collaboration API (Google Docs-backed)
    Route::get('/collab/courses/{course}/documents', [CollaborationController::class, 'index']);
    Route::post('/collab/courses/{course}/documents', [CollaborationController::class, 'store']);
    Route::get('/collab/documents/{document}', [CollaborationController::class, 'show']);
    Route::post('/collab/documents/{document}/resync', [CollaborationController::class, 'resync']);
    Route::delete('/collab/documents/{document}', [CollaborationController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware('role:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Account Management
    Route::get('/account-management', [AccountManagementController::class, 'index']);
    Route::post('/account-management', [AccountManagementController::class, 'store']);
    Route::put('/account-management/{user}', [AccountManagementController::class, 'update']);
    Route::patch('/account-management/{user}/archive', [AccountManagementController::class, 'archive']);
    Route::patch('/account-management/{user}/unarchive', [AccountManagementController::class, 'unarchive']);

    // Roles & Permissions
    Route::get('/roles-permissions', [RolesPermissionsController::class, 'index']);
    Route::get('/roles-permissions/{role}', [RolesPermissionsController::class, 'show']);
    Route::post('/roles-permissions/{role}', [RolesPermissionsController::class, 'update']);

    // Template Approvals (dating System Approvals)
    Route::get('/template-approvals', [TemplateApprovalController::class, 'index']);
    Route::post('/template-approvals', [TemplateApprovalController::class, 'store']);
    Route::delete('/template-approvals/{template}', [TemplateApprovalController::class, 'destroy']);

    Route::get('/audit-logs', [AuditLogController::class, 'index']);

    // Announcements
    Route::get('/announcements', [AdminAnnouncementController::class, 'index']);
    Route::post('/announcements', [AdminAnnouncementController::class, 'store']);
    Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy']);

    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::post('/calendar', [CalendarController::class, 'store']);
    Route::put('/calendar/{activity}', [CalendarController::class, 'update']);
    Route::delete('/calendar/{activity}', [CalendarController::class, 'destroy']);

    Route::get('/program-assignment', [ProgramAssignmentController::class, 'index']);
    Route::post('/program-assignment', [ProgramAssignmentController::class, 'store']);
    Route::delete('/program-assignment/{assignment}', [ProgramAssignmentController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Program Head
|--------------------------------------------------------------------------
*/

Route::middleware('role:program_head')->prefix('program-head')->group(function () {
    Route::get('/dashboard', [PHDashboardController::class, 'index']);
    Route::get('/template-review', [TemplateReviewController::class, 'index']);
    Route::post('/template-review/{template}/distribute', [TemplateReviewController::class, 'distribute']);
    Route::get('/course-oversight', [CourseOversightController::class, 'index']);
    Route::post('/course-oversight/materials', [CourseOversightController::class, 'store']);
    Route::delete('/course-oversight/materials/{material}', [CourseOversightController::class, 'destroy']);
    Route::post('/course-oversight/program-outcomes', [CourseOversightController::class, 'storeProgramOutcome']);
    Route::delete('/course-oversight/program-outcomes/{programOutcome}', [CourseOversightController::class, 'destroyProgramOutcome']);
    Route::post('/course-oversight/course-outcomes', [CourseOversightController::class, 'storeCourseOutcome']);
    Route::delete('/course-oversight/course-outcomes/{courseOutcome}', [CourseOversightController::class, 'destroyCourseOutcome']);
    Route::post('/course-oversight/course-outcomes/{courseOutcome}/mapping/{programOutcome}', [CourseOversightController::class, 'toggleMapping']);

    // Account Management
    Route::get('/account-management', [PHAccountManagementController::class, 'index']);
    Route::post('/account-management', [PHAccountManagementController::class, 'store']);
    Route::put('/account-management/{user}', [PHAccountManagementController::class, 'update']);
    Route::patch('/account-management/{user}/archive', [PHAccountManagementController::class, 'archive']);
    Route::patch('/account-management/{user}/unarchive', [PHAccountManagementController::class, 'unarchive']);

    // Announcements
    Route::get('/announcements', [PHAnnouncementController::class, 'index']);
    Route::post('/announcements', [PHAnnouncementController::class, 'store']);
    Route::delete('/announcements/{announcement}', [PHAnnouncementController::class, 'destroy']);

    Route::get('/calendar', [CalendarController::class, 'index']);

    Route::get('/submissions', [PHSubmissionController::class, 'index']);
    Route::post('/submissions', [PHSubmissionController::class, 'store']);
    Route::put('/submissions/{requirement}', [PHSubmissionController::class, 'update']);
    Route::delete('/submissions/{requirement}', [PHSubmissionController::class, 'destroy']);

    Route::get('/program-assignment', [ProgramAssignmentController::class, 'index']);
    Route::post('/program-assignment', [ProgramAssignmentController::class, 'store']);
    Route::delete('/program-assignment/{assignment}', [ProgramAssignmentController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Secretary
|--------------------------------------------------------------------------
*/

Route::middleware('role:secretary')->prefix('secretary')->group(function () {
    Route::get('/dashboard', [SecDashboardController::class, 'index']);
    Route::get('/document-repository', [DocumentRepositoryController::class, 'index']);
    Route::post('/document-repository', [DocumentRepositoryController::class, 'store']);
    Route::delete('/document-repository/{document}', [DocumentRepositoryController::class, 'destroy']);
    Route::get('/template-distribution', [TemplateDistributionController::class, 'index']);
    Route::post('/template-distribution/{template}/forward', [TemplateDistributionController::class, 'forward']);
    Route::get('/course-filing', [CourseFilingController::class, 'index']);

    // Account Management
    Route::get('/account-management', [SecAccountManagementController::class, 'index']);
    Route::post('/account-management', [SecAccountManagementController::class, 'store']);
    Route::put('/account-management/{user}', [SecAccountManagementController::class, 'update']);
    Route::patch('/account-management/{user}/archive', [SecAccountManagementController::class, 'archive']);
    Route::patch('/account-management/{user}/unarchive', [SecAccountManagementController::class, 'unarchive']);

    // Announcements
    Route::get('/announcements', [SecAnnouncementController::class, 'index']);
    Route::post('/announcements', [SecAnnouncementController::class, 'store']);
    Route::delete('/announcements/{announcement}', [SecAnnouncementController::class, 'destroy']);

    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::post('/calendar', [CalendarController::class, 'store']);
    Route::put('/calendar/{activity}', [CalendarController::class, 'update']);
    Route::delete('/calendar/{activity}', [CalendarController::class, 'destroy']);

    Route::get('/program-assignment', [ProgramAssignmentController::class, 'index']);
    Route::post('/program-assignment', [ProgramAssignmentController::class, 'store']);
    Route::delete('/program-assignment/{assignment}', [ProgramAssignmentController::class, 'destroy']);
});