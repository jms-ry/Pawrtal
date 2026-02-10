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
use App\Http\Controllers\ReportAlertController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Log;
use App\Models\Donation;

Route::get('/donate', [DonateController::class, 'index'])->name('donate.index');
Route::get('/',[WelcomeController::class, 'index'])->name('welcome');
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');

// index - show all rescues
Route::get('/rescues', [RescueController::class, 'index'])->name('rescues.index');

// show - display a specific rescue
Route::get('/rescues/{rescue}', [RescueController::class, 'show'])->name('rescues.show');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::middleware('guest')->group(function () {
  Route::get('register', function () {
    return view('register');
  });

  Route::get('login', function () {
    return view('sign_in');
  });
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

  Route::resource('donations', DonationController::class)->except('show','edit','create');;

  Route::resource('addresses',AddressController::class)->except('index','show','create','edit');

  Route::resource('households',HouseholdController::class)->except('index','show','create','edit');

  Route::resource('adoption-applications',AdoptionApplicationController::class)->except('create','show','edit');

  // store - save a new rescue
  Route::post('/rescues', [RescueController::class, 'store'])->name('rescues.store');

  // update - update a specific rescue
  Route::put('/rescues/{rescue}', [RescueController::class, 'update'])->name('rescues.update');

  // destroy - delete a specific rescue
  Route::delete('/rescues/{rescue}', [RescueController::class, 'destroy'])->name('rescues.destroy');

  Route::patch('/rescues/{rescue}/restore', [RescueController::class, 'restore'])->name('rescues.restore');

  // store - save a new report
  Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

  // update - update a specific report
  Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');

  // destroy - delete a specific report
  Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

  Route::patch('/reports/{report}/restore', [ReportController::class, 'restore'])->name('reports.restore');

  Route::patch('/donations/{donation}/restore', [DonationController::class, 'restore'])->name('donations.restore');

  Route::patch('/adoption-applications/{adoption_application}/restore', [AdoptionApplicationController::class, 'restore'])->name('adoption_applications.restore');

  Route::resource('inspection-schedules',InspectionScheduleController::class)->except('index','show','create','edit','destroy');

  Route::resource('users',UserController::class)->except('create','edit');

  // Conversation routes
  Route::prefix('conversations')->group(function () {
    Route::get('/', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/', [ConversationController::class, 'store'])->name('conversations.store');
    Route::put('/{conversation}/read', [ConversationController::class, 'markAsRead'])->name('conversations.markAsRead');
    //Route::get('/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
  });

  // Message routes
  Route::prefix('conversations/{conversation}/messages')->group(function () {
    Route::post('/', [MessageController::class, 'store'])->name('messages.store');
  });

  Route::post('/reports/{report}/alert', [ReportAlertController::class, 'store'])->name('reports.alert');

  Route::post('/api/donations/create-payment', [DonationController::class, 'createPayment']);
});

// Temporary placeholder routes for testing
Route::get('/donations/success', function() {
  return 'Payment Successful!';
})->name('donations.success');

Route::get('/donations/failed', function() {
  // Try to get source ID from query parameter
  $sourceId = request()->query('id');
    
  if ($sourceId) {
    // Find and cancel the donation
    $donation = Donation::where('payment_intent_id', $sourceId)
      ->where('payment_status', 'pending')
    ->first();
            
    if ($donation) {
      $donation->update([
        'payment_status' => 'failed',
        'status' => 'cancelled',
      ]);
            
      Log::info('Donation cancelled via failed route', [
        'donation_id' => $donation->id,
        'source_id' => $sourceId
      ]);
    }
  }
    
  return 'Payment Failed! Your donation has been cancelled.';
})->name('donations.failed');

// PayMongo Webhook - Must be public (no auth)
Route::post('/webhook/paymongo', [WebhookController::class, 'handlePayMongo'])->name('webhook.paymongo');

Route::get('/cron/cleanup-donations', function() {
  // Security: Check secret key
  if (request()->query('secret') !== env('CRON_SECRET')) {
    abort(403, 'Unauthorized');
  }
    
  // Run the cleanup command
  Artisan::call('donations:cleanup-expired');
    
    return response()->json([
      'success' => true,
      'message' => 'Cleanup executed',
      'output' => Artisan::output(),
      'timestamp' => now()->toDateTimeString()
    ]);
});
require __DIR__.'/auth.php';