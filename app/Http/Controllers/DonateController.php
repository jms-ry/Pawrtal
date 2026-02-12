<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Donation;
class DonateController extends Controller
{
  public function index()
  {
    $user = Auth::user();

    return Inertia::render('Donate/Index',['user' => $user]);
  }

  public function donationSuccess(Request $request)
  {
    // Check if coming from PayMongo
    $referer = $request->header('referer');
    $isFromPayMongo = $referer && str_contains($referer, 'paymongo.com');

    error_log('Is from PayMongo: ' . ($isFromPayMongo ? 'YES' : 'NO'));
    // Block direct access (no PayMongo referer)
    if (!$isFromPayMongo) {
      error_log('Direct access blocked - redirecting to home');
      return redirect('/')->with('info', 'Please make a donation to view this page.');
    }

    $sourceId = $request->query('id');
    
    $donation = null;
    
    if ($sourceId) {
      // Try to find by source ID
      $donation = Donation::where('payment_intent_id', $sourceId)
        ->where('donation_type', 'monetary')
      ->first();
            
      error_log('Donation found by source: ' . ($donation ? 'YES - ID: ' . $donation->id : 'NO'));
    }

    if (!$donation && Auth::check()) {
      $donation = Donation::where('user_id', Auth::id())
        ->where('donation_type', 'monetary')
        ->latest('donation_date')
      ->first();
            
      error_log('Latest donation for user: ' . ($donation ? 'YES - ID: ' . $donation->id : 'NO'));
    }
    
    return Inertia::render('Donate/Success', [
      'donation' => $donation
    ]);
  }

}
