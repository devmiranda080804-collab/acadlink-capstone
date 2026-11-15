<?php

namespace App\Support;

// The sidebar of every role, in one place (rendered by partials.sidebar). Each item keeps the
// permission key, link and "active" pattern the pages always used; items are grouped into
// labelled sections. Icons are Lucide-style line icons (24×24, 2px stroke).
class SidebarMenu
{
    // section label => items. An item: [permission key|null, label, url, active pattern(s), icon, badge variable|null]
    const MENUS = [
        'faculty' => [
            'Overview' => [
                ['dashboard', 'Dashboard', '/faculty/dashboard', 'faculty/dashboard', 'dashboard', null],
            ],
            'Teaching' => [
                ['my-template', 'Templates', '/faculty/my-template', 'faculty/my-template*', 'files', 'newTemplatesCount'],
                ['exam-generator', 'Assessment Generator', '/faculty/exam-generator', 'faculty/exam-generator*', 'clipboard', null],
                ['course-coordination', 'Course Coordination', '/faculty/course-coordination', 'faculty/course-coordination*', 'cap', null],
                ['shared-library', 'Shared Library', '/faculty/shared-library', 'faculty/shared-library*', 'book', null],
                ['cms', 'CMS', '/faculty/cms', 'faculty/cms*', 'pen', null],
            ],
            'Tracking' => [
                ['submissions', 'Submissions and Deadline', '/faculty/submissions', 'faculty/submissions*', 'deadline', 'urgentSubmissionsCount'],
                ['analytics', 'Analytics', '/faculty/analytics', 'faculty/analytics*', 'chart', null],
            ],
            'Updates' => [
                ['announcements', 'Announcements', '/faculty/announcements', 'faculty/announcements*', 'megaphone', 'unreadAnnouncementsCount'],
                ['calendar', 'Calendar of Activities', '/faculty/calendar', 'faculty/calendar*', 'calendar', null],
            ],
        ],
        'program_head' => [
            'Overview' => [
                ['dashboard', 'Dashboard', '/program-head/dashboard', 'program-head/dashboard', 'dashboard', null],
                ['analytics', 'Analytics', '/program-head/analytics', 'program-head/analytics*', 'chart', null],
            ],
            'Program' => [
                ['template-review', 'Templates', '/program-head/template-review', 'program-head/template-review*', 'files', null],
                ['course-oversight', 'Course Oversight', '/program-head/course-oversight', 'program-head/course-oversight*', 'eye', null],
                ['program-assignment', 'Program Assignment', '/program-head/program-assignment', 'program-head/program-assignment*', 'userCheck', null],
                ['submissions', 'Submissions and Deadline', '/program-head/submissions', 'program-head/submissions*', 'deadline', null],
                ['official-documents', 'Official Documents', '/program-head/official-documents', 'program-head/official-documents*', 'fileCheck', null],
            ],
            'People' => [
                ['account-management', 'Account Management', '/program-head/account-management', 'program-head/account-management*', 'users', null],
            ],
            'Updates' => [
                ['announcements', 'Announcements', '/program-head/announcements', 'program-head/announcements*', 'megaphone', 'unreadAnnouncementsCount'],
                ['calendar', 'Calendar of Activities', '/program-head/calendar', 'program-head/calendar*', 'calendar', null],
            ],
        ],
        'secretary' => [
            'Overview' => [
                ['dashboard', 'Dashboard', '/secretary/dashboard', 'secretary/dashboard', 'dashboard', null],
            ],
            'Documents' => [
                ['template-distribution', 'Template Distribution', '/secretary/template-distribution', 'secretary/template-distribution*', 'send', null],
                ['course-filing', 'Course Filing', '/secretary/course-filing', 'secretary/course-filing*', 'folderCheck', null],
                ['document-repository', 'Document Repository', '/secretary/document-repository', 'secretary/document-repository*', 'archive', null],
                ['system-cms', 'System CMS', '/secretary/system-cms', 'secretary/system-cms*', 'layout', null],
            ],
            'People' => [
                ['account-management', 'Account Management', '/secretary/account-management', 'secretary/account-management*', 'users', null],
            ],
            'Updates' => [
                ['announcements', 'Announcements', '/secretary/announcements', 'secretary/announcements*', 'megaphone', 'unreadAnnouncementsCount'],
                ['calendar', 'Calendar of Activities', '/secretary/calendar', 'secretary/calendar*', 'calendar', null],
            ],
        ],
        'admin' => [
            'Overview' => [
                ['dashboard', 'Dashboard', '/admin/dashboard', 'admin/dashboard', 'dashboard', null],
                ['analytics', 'Analytics', '/admin/analytics', 'admin/analytics*', 'chart', null],
            ],
            'Documents' => [
                ['template-approvals', 'Templates', '/admin/template-approvals', 'admin/template-approvals*', 'files', null],
                ['system-cms', 'System CMS', '/admin/system-cms', 'admin/system-cms*', 'layout', null],
            ],
            'People' => [
                ['account-management', 'Account Management', '/admin/account-management', 'admin/account-management*', 'users', null],
                ['roles-permissions', 'Roles & Permissions', '/admin/roles-permissions', 'admin/roles-permissions*', 'shield', null],
                ['program-assignment', 'Program Assignment', '/admin/program-assignment', 'admin/program-assignment*', 'userCheck', null],
            ],
            'Updates' => [
                ['announcements', 'Announcements', '/admin/announcements', 'admin/announcements*', 'megaphone', 'unreadAnnouncementsCount'],
                ['calendar', 'Calendar of Activities', '/admin/calendar', 'admin/calendar*', 'calendar', null],
            ],
            'System' => [
                ['audit-logs', 'Audit Logs', '/admin/audit-logs', 'admin/audit-logs*', 'scroll', null],
                // Settings never had a permission switch; it stays that way.
                [null, 'Settings', '/admin/settings', 'admin/settings*', 'settings', null],
            ],
        ],
    ];

    const ROLE_LABELS = [
        'admin'        => 'Admin / Dean',
        'program_head' => 'Program Head',
        'secretary'    => 'Secretary',
        'faculty'      => 'Faculty',
    ];

    // Lucide-style icon paths (viewBox 0 0 24 24, stroke currentColor, 2px, round caps).
    const ICONS = [
        'dashboard'   => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
        'files'       => '<path d="M20 7h-3a2 2 0 0 1-2-2V2"/><path d="M9 18a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h7l4 4v10a2 2 0 0 1-2 2Z"/><path d="M3 7.6v12.8A1.6 1.6 0 0 0 4.6 22h9.8"/>',
        'clipboard'   => '<rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>',
        'cap'         => '<path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/>',
        'book'        => '<path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>',
        'pen'         => '<path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/>',
        'deadline'    => '<path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h5"/><path d="M17.5 17.5 16 16.3V14"/><circle cx="16" cy="16" r="6"/>',
        'chart'       => '<path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
        'megaphone'   => '<path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
        'calendar'    => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>',
        'eye'         => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>',
        'userCheck'   => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>',
        'fileCheck'   => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/>',
        'users'       => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'send'        => '<path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/>',
        'folderCheck' => '<path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/><path d="m9 13 2 2 4-4"/>',
        'archive'     => '<rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>',
        'layout'      => '<rect width="18" height="7" x="3" y="3" rx="1"/><rect width="9" height="7" x="3" y="14" rx="1"/><rect width="5" height="7" x="16" y="14" rx="1"/>',
        'shield'      => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
        'scroll'      => '<path d="M15 12h-5"/><path d="M15 8h-5"/><path d="M19 17V5a2 2 0 0 0-2-2H4"/><path d="M8 21h12a2 2 0 0 0 2-2v-1a1 1 0 0 0-1-1H11a1 1 0 0 0-1 1v1a2 2 0 1 1-4 0V5a2 2 0 1 0-4 0v2a1 1 0 0 0 1 1h3"/>',
        'settings'    => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
        'key'         => '<path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"/><circle cx="16.5" cy="7.5" r=".5" fill="currentColor"/>',
        'logout'      => '<path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>',
        'collapse'    => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/><path d="m16 15-3-3 3-3"/>',
        // Used by the dashboards.
        'arrow'       => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'pin'         => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
        'plus'        => '<path d="M5 12h14"/><path d="M12 5v14"/>',
        'bell'        => '<path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>',
        'inbox'       => '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
        'clock'       => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'checkCircle' => '<path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/>',
        'undo'        => '<path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"/>',
        'fileText'    => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>',
        'layers'      => '<path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>',
        'compass'     => '<path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z"/><circle cx="12" cy="12" r="10"/>',
        'sparkles'    => '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>',
    ];

    public static function icon(string $name, string $class = 'sb-icon'): string
    {
        return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            . (self::ICONS[$name] ?? '') . '</svg>';
    }

    // The sections the signed-in user sees: hidden items (no permission) are left out,
    // and a section with nothing left in it is dropped.
    public static function sectionsFor(string $role, array $permissions): array
    {
        $sections = [];
        foreach (self::MENUS[$role] ?? [] as $label => $items) {
            $shown = array_values(array_filter($items, fn ($item) => $item[0] === null || ($permissions[$item[0]] ?? true)));
            if ($shown) {
                $sections[$label] = $shown;
            }
        }

        return $sections;
    }
}
