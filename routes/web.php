<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdoptionApplicationController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RescueController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/donate', [DonateController::class, 'index'])->name('donate.index');
Route::get('/',[WelcomeController::class, 'index'])->name('welcome');
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');
Route::resource('rescues', RescueController::class)->except('create','edit');
Route::resource('reports', ReportController::class)->except('show','create','edit');
Route::resource('donations', DonationController::class);
Route::resource('addresses',AddressController::class)->except('index','show','create','edit');
Route::resource('households',HouseholdController::class)->except('index','show','create','edit');
Route::resource('adoption-applications',AdoptionApplicationController::class)->except('create','edit');


Route::get('register', function () {
  return view('register');
});

Route::get('login', function () {
  return view('sign_in');
});

Route::middleware(['auth'])->group(function () {
  Route::get('/dashboard',[AdminStaffController::class, 'index']);
  Route::prefix('users')->group(function () {
    Route::get('my-reports', [UserController::class, 'myReports'])->name('users.myReports');
    Route::get('my-donations', [UserController::class, 'myDonations'])->name('users.myDonations');
    Route::get('my-adoption-applications', [UserController::class, 'myAdoptionApplications'])->name('users.myAdoptionApplications');
  });
  Route::resource('users',UserController::class)->except('create','edit');
});


require __DIR__.'/auth.php';