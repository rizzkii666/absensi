<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceController::class, 'create'])->name('attendance.create');
Route::post('/attendance', [AttendanceController::class, 'store'])
    ->middleware('throttle:attendance')
    ->name('attendance.store');
Route::post('/attendance/qr', [AttendanceController::class, 'storeQr'])
    ->middleware('throttle:attendance')
    ->name('attendance.store_qr');


Route::middleware('guest')->group(function (): void {
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])->middleware('throttle:login');
});

Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

Route::prefix('admin')->middleware('auth')->group(function (): void {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/employees', [AdminDashboardController::class, 'employees'])->name('admin.employees');
    Route::post('/employees', [AdminDashboardController::class, 'storeEmployee'])->name('admin.employees.store');
    Route::delete('/employees/{employee}', [AdminDashboardController::class, 'destroyEmployee'])->name('admin.employees.destroy');
    Route::get('/report', [AdminDashboardController::class, 'report'])->name('admin.report');
});
