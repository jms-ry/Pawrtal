<?php

use App\Http\Controllers\AdoptionApplicationController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RescueController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/',[WelcomeController::class, 'index'])->name('welcome');
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');
Route::resource('rescues', RescueController::class);
Route::resource('reports', ReportController::class)->except('show');
Route::resource('donations', DonationController::class);
Route::resource('adoption-applications', AdoptionApplicationController::class);

Route::get('/donate', function () {
    return view('donate.index');
});

Route::get('register', function () {
    return view('register');
});

Route::get('sign-in', function () {
    return view('sign_in');
});
require __DIR__.'/auth.php';