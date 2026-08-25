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
use App\Http\Controllers\CourseAssignmentController;
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
    Route::post('/my-template', [TemplateController::class, 'store']);
    Route::put('/my-template/{template}', [TemplateController::class, 'update']);
    Route::delete('/my-template/{template}', [TemplateController::class, 'destroy']);
    Route::get('/exam-generator', fn () => view('faculty.exam-generator'));
    Route::get('/shared-library', [SharedLibraryController::class, 'index']);
    Route::post('/shared-library', [SharedLibraryController::class, 'store']);
    Route::delete('/shared-library/{resource}', [SharedLibraryController::class, 'destroy']);
    Route::get('/course-coordination', [CourseCoordinationController::class, 'index']);
    Route::get('/analytics', fn () => view('faculty.analytics'));
    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::get('/announcements', [FacultyAnnouncementController::class, 'index']);
    Route::get('/submissions', [FacultySubmissionController::class, 'index']);
    Route::post('/submissions/{requirement}', [FacultySubmissionController::class, 'store']);
    Route::get('/cms', fn () => view('faculty.cms'));

    // Collaboration API
    Route::get('/collab/courses/{course}/documents', [CollaborationController::class, 'index']);
    Route::post('/collab/courses/{course}/documents', [CollaborationController::class, 'store']);
    Route::get('/collab/documents/{document}', [CollaborationController::class, 'show']);
    Route::put('/collab/documents/{document}', [CollaborationController::class, 'update']);
    Route::get('/collab/documents/{document}/versions', [CollaborationController::class, 'versions']);
    Route::post('/collab/documents/{document}/versions/{version}/restore', [CollaborationController::class, 'restoreVersion']);
    Route::post('/collab/documents/{document}/heartbeat', [CollaborationController::class, 'heartbeat']);
    Route::get('/collab/documents/{document}/export', [CollaborationController::class, 'export']);
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
    Route::post('/template-approvals/{template}/approve', [TemplateApprovalController::class, 'approve']);
    Route::post('/template-approvals/{template}/reject', [TemplateApprovalController::class, 'reject']);

    Route::get('/audit-logs', [AuditLogController::class, 'index']);

    // Announcements
    Route::get('/announcements', [AdminAnnouncementController::class, 'index']);
    Route::post('/announcements', [AdminAnnouncementController::class, 'store']);
    Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy']);

    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::post('/calendar', [CalendarController::class, 'store']);
    Route::put('/calendar/{activity}', [CalendarController::class, 'update']);
    Route::delete('/calendar/{activity}', [CalendarController::class, 'destroy']);

    Route::get('/course-assignment', [CourseAssignmentController::class, 'index']);
    Route::post('/course-assignment', [CourseAssignmentController::class, 'store']);
    Route::delete('/course-assignment/{assignment}', [CourseAssignmentController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Program Head
|--------------------------------------------------------------------------
*/

Route::middleware('role:program_head')->prefix('program-head')->group(function () {
    Route::get('/dashboard', [PHDashboardController::class, 'index']);
    Route::get('/template-review', [TemplateReviewController::class, 'index']);
    Route::post('/template-review/{template}/approve', [TemplateReviewController::class, 'approve']);
    Route::post('/template-review/{template}/needs-revision', [TemplateReviewController::class, 'needsRevision']);
    Route::get('/course-oversight', [CourseOversightController::class, 'index']);
    Route::post('/course-oversight/materials', [CourseOversightController::class, 'store']);
    Route::delete('/course-oversight/materials/{material}', [CourseOversightController::class, 'destroy']);

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

    Route::get('/course-assignment', [CourseAssignmentController::class, 'index']);
    Route::post('/course-assignment', [CourseAssignmentController::class, 'store']);
    Route::delete('/course-assignment/{assignment}', [CourseAssignmentController::class, 'destroy']);
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
    Route::post('/template-distribution/{template}/distribute', [TemplateDistributionController::class, 'distribute']);
    Route::post('/template-distribution/{template}/undistribute', [TemplateDistributionController::class, 'undistribute']);
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

    Route::get('/course-assignment', [CourseAssignmentController::class, 'index']);
    Route::post('/course-assignment', [CourseAssignmentController::class, 'store']);
    Route::delete('/course-assignment/{assignment}', [CourseAssignmentController::class, 'destroy']);
});