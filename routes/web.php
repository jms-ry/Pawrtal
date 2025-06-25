<?php

use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/',[WelcomeController::class, 'index'])->name('welcome');
Route::get('/rescues',[AnimalController::class,'index'])->name('rescues.index');
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');

Route::get('/reports', function () {
    return view('reports.index');
});

Route::get('/donate', function () {
    return view('donate.index');
});

Route::get('faqs', function () {
    return view('faqs');
});

Route::get('register', function () {
    return view('register');
});

Route::get('sign-in', function () {
    return view('sign_in');
});
require __DIR__.'/auth.php';