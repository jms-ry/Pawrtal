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
    error_log('===== SUCCESS PAGE ACCESSED =====');
    error_log('All params: ' . json_encode($request->all()));
    error_log('Query string: ' . $request->getQueryString());
    error_log('Full URL: ' . $request->fullUrl());
    
    $sourceId = $request->query('id');
    error_log('Source ID from query: ' . ($sourceId ?? 'NULL'));
    
    $donation = null;
    
    if ($sourceId) {
        // Try to find by source ID
        $donation = Donation::where('payment_intent_id', $sourceId)
            ->where('donation_type', 'monetary')
            ->first();
            
        error_log('Donation found by source: ' . ($donation ? 'YES - ID: ' . $donation->id : 'NO'));
    }
    
    // Fallback: Get user's most recent monetary donation (any status)
    if (!$donation && Auth::check()) {
        error_log('Trying fallback to latest monetary donation');
        
        $donation = Donation::where('user_id', Auth::id())
            ->where('donation_type', 'monetary')
            ->latest('donation_date') // Use latest created, not latest paid
            ->first();
            
        error_log('Fallback donation found: ' . ($donation ? 'YES - ID: ' . $donation->id : 'NO'));
        
        if ($donation) {
            error_log('Donation status: ' . $donation->payment_status);
        }
    }
    
    return Inertia::render('Donate/Success', [
        'donation' => $donation
    ]);
}
}
