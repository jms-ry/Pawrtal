<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdoptionApplicationController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RescueController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/',[WelcomeController::class, 'index'])->name('welcome');
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');
Route::resource('rescues', RescueController::class);
Route::resource('reports', ReportController::class)->except('show');
Route::resource('donations', DonationController::class);
Route::resource('addresses',AddressController::class);
Route::resource('households',HouseholdController::class);

Route::get('/donate', function () {
  return view('donate.index');
});

Route::get('register', function () {
  return view('register');
});

Route::get('login', function () {
  return view('sign_in');
});

Route::middleware(['auth'])->group(function () {
  Route::get('/dashboard',[AdminStaffController::class, 'index']);
  Route::resource('users',UserController::class);
});

Route::middleware(['auth'])->group(function () {
  Route::resource('adoption-applications', AdoptionApplicationController::class);
});

require __DIR__.'/auth.php';