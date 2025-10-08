<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdoptionApplicationController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\InspectionScheduleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RescueController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;

Route::get('/donate', [DonateController::class, 'index'])->name('donate.index');
Route::get('/',[WelcomeController::class, 'index'])->name('welcome');
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');

Route::resource('rescues', RescueController::class)->except('create','edit');

Route::resource('reports', ReportController::class)->except('show','create','edit');

Route::resource('donations', DonationController::class)->except('show','edit');;

Route::resource('addresses',AddressController::class)->except('index','show','create','edit');

Route::resource('households',HouseholdController::class)->except('index','show','create','edit');

Route::resource('adoption-applications',AdoptionApplicationController::class)->except('create','show','edit');

Route::patch('/rescues/{rescue}/restore', [RescueController::class, 'restore'])->name('rescues.restore');

Route::patch('/reports/{report}/restore', [ReportController::class, 'restore'])->name('reports.restore');

Route::patch('/donations/{donation}/restore', [DonationController::class, 'restore'])->name('donations.restore');

Route::patch('/adoption-applications/{adoption_application}/restore', [AdoptionApplicationController::class, 'restore'])->name('adoption_applications.restore');

Route::resource('inspection-schedules',InspectionScheduleController::class)->except('index','show','create','edit','destroy');

Route::middleware('guest')->group(function () {
  Route::get('register', function () {
    return view('register');
  })->name('register');

  Route::get('login', function () {
    return view('sign_in');
  })->name('login');
});

Route::middleware(['auth'])->group(function () {

  Route::prefix('dashboard')->group(function () {
    Route::get('rescues', [AdminStaffController::class, 'rescues'])->name('dashboard.rescues');
    Route::get('reports', [AdminStaffController::class, 'reports'])->name('dashboard.reports');
    Route::get('donations', [AdminStaffController::class, 'donations'])->name('dashboard.donations');
    Route::get('adoption-applications', [AdminStaffController::class, 'adoptionApplications'])->name('dashboard.adoptionApplications');
  });

  Route::get('/dashboard',[AdminStaffController::class, 'index']);

  Route::prefix('users')->group(function () {
    Route::get('my-reports', [UserController::class, 'myReports'])->name('users.myReports');
    Route::get('my-donations', [UserController::class, 'myDonations'])->name('users.myDonations');
    Route::get('my-adoption-applications', [UserController::class, 'myAdoptionApplications'])->name('users.myAdoptionApplications');
    Route::get('my-schedules', [UserController::class, 'mySchedules'])->name('users.mySchedules');
    Route::get('my-notifications', [UserController::class, 'myNotifications'])->name('users.myNotifications');

    Route::post('notifications/{id}/mark-as-read', [UserController::class, 'markNotificationAsRead'])->name('users.markNotificationAsRead');
    Route::post('notifications/mark-all-as-read', [UserController::class, 'markAllNotificationsAsRead'])->name('users.markAllNotificationsAsRead');
  });

  Route::resource('users',UserController::class)->except('create','edit');

  // Conversation routes
  Route::prefix('conversations')->group(function () {
    Route::get('/', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
  });

  // Message routes
  Route::prefix('conversations/{conversation}/messages')->group(function () {
    Route::get('/', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('messages.markAllAsRead');
  });

  // Individual message actions
  Route::patch('messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');

});


require __DIR__.'/auth.php';