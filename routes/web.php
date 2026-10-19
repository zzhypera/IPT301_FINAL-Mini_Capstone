<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RepairJobController;
use App\Http\Controllers\TechnicianController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::resource('customers', CustomerController::class);
Route::resource('devices', DeviceController::class);
Route::resource('technicians', TechnicianController::class);
Route::resource('repair-jobs', RepairJobController::class)
    ->parameters(['repair-jobs' => 'repairJob']);
Route::resource('payments', PaymentController::class);
