<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\ProgramHead\AccountManagementController as PHAccountManagementController;
use App\Http\Controllers\Secretary\AccountManagementController as SecAccountManagementController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Faculty
|--------------------------------------------------------------------------
*/

Route::get('/faculty/dashboard', fn() => view('faculty.dashboard'));
Route::get('/faculty/my-template', fn() => view('faculty.my-template'));
Route::get('/faculty/exam-generator', fn() => view('faculty.exam-generator'));
Route::get('/faculty/shared-library', fn() => view('faculty.shared-library'));
Route::get('/faculty/course-coordination', fn() => view('faculty.course-coordination'));
Route::get('/faculty/analytics', fn() => view('faculty.analytics'));
Route::get('/faculty/user-manuals', fn() => view('faculty.user-manuals'));
Route::get('/faculty/calendar', fn() => view('faculty.calendar'));
Route::get('/faculty/announcements', fn() => view('faculty.announcements'));
Route::get('/faculty/submissions', fn() => view('faculty.submissions'));
Route::get('/faculty/cms', fn() => view('faculty.cms'));

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', fn() => view('admin.admin-dashboard'));
Route::get('/admin/account-management', [AccountManagementController::class, 'index']);
Route::post('/admin/account-management', [AccountManagementController::class, 'store']);
Route::put('/admin/account-management/{user}', [AccountManagementController::class, 'update']);
Route::delete('/admin/account-management/{user}', [AccountManagementController::class, 'destroy']);
Route::get('/admin/roles-permissions', fn() => view('admin.roles-permissions'));
Route::get('/admin/system-approvals', fn() => view('admin.system-approvals'));
Route::get('/admin/edit-roles', fn() => view('admin.edit-roles'));
Route::get('/admin/audit-logs', fn() => view('admin.audit-logs'));
Route::get('/admin/announcements', fn() => view('admin.admin-announcements'));


/*
|--------------------------------------------------------------------------
| Program Head
|--------------------------------------------------------------------------
*/

Route::middleware('role:program_head')->prefix('program-head')->group(function () {
    Route::get('/dashboard', fn () => view('program-head.program-head-dashboard'));
    Route::get('/template-review', fn () => view('program-head.template-review'));
    Route::get('/course-oversight', fn () => view('program-head.course-oversight'));
    Route::get('/account-management', [PHAccountManagementController::class, 'index']);
    Route::post('/account-management', [PHAccountManagementController::class, 'store']);
    Route::put('/account-management/{user}', [PHAccountManagementController::class, 'update']);
    Route::delete('/account-management/{user}', [PHAccountManagementController::class, 'destroy']);
    Route::get('/announcements', fn () => view('program-head.ph-announcements'));
});

/*
|--------------------------------------------------------------------------
| Secretary
|--------------------------------------------------------------------------
*/

Route::middleware('role:secretary')->prefix('secretary')->group(function () {
    Route::get('/dashboard', fn () => view('secretary.secretary-dashboard'));
    Route::get('/document-repository', fn () => view('secretary.document-repository'));
    Route::get('/template-distribution', fn () => view('secretary.template-distribution'));
    Route::get('/course-filing', fn () => view('secretary.course-filing'));
    Route::get('/account-management', [SecAccountManagementController::class, 'index']);
    Route::post('/account-management', [SecAccountManagementController::class, 'store']);
    Route::put('/account-management/{user}', [SecAccountManagementController::class, 'update']);
    Route::delete('/account-management/{user}', [SecAccountManagementController::class, 'destroy']);
    Route::get('/announcements', fn () => view('secretary.sec-announcements'));
});