<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rescues', function () {
    return view('rescues.index');
})->name('rescues');

Route::get('/reports', function () {
    return view('reports.index');
});

Route::get('/donate', function () {
    return view('donate.index');
});

Route::get('adoption', function () {
    return view('adoption.index');
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