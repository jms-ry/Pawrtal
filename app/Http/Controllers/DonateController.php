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
    // Debug: Log all query parameters
    error_log('===== SUCCESS PAGE ACCESSED =====');
    error_log('All params: ' . json_encode($request->all()));
    error_log('Query string: ' . $request->getQueryString());
    error_log('Full URL: ' . $request->fullUrl());
    
    // Get source ID from query parameter
    $sourceId = $request->query('id');
    
    error_log('Source ID from query: ' . ($sourceId ?? 'NULL'));
    
    $donation = null;
    
    if ($sourceId) {
        $donation = Donation::where('payment_intent_id', $sourceId)
            ->where('donation_type', 'monetary')
            ->first();
            
        error_log('Donation found: ' . ($donation ? 'YES - ID: ' . $donation->id : 'NO'));
    } else {
        error_log('No source ID provided, trying fallback to latest paid donation');
        
        if (Auth::check()) {
            // Fallback: Get user's most recent paid donation
            $donation = Donation::where('user_id', Auth::id())
                ->where('donation_type', 'monetary')
                ->where('payment_status', 'paid')
                ->latest('paid_at')
                ->first();
                
            error_log('Fallback donation found: ' . ($donation ? 'YES - ID: ' . $donation->id : 'NO'));
        } else {
            error_log('User not authenticated for fallback');
        }
    }
    
    return Inertia::render('Donate/Success', [
        'donation' => $donation
    ]);
}
}
