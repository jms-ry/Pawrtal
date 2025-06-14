<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rescues', function () {
    return view('rescues.index');
});

Route::get('/reports', function () {
    return view('reports.index');
});

Route::get('/donate', function () {
    return view('donate.index');
});

Route::get('adoption', function () {
    return view('adoption.index');
});