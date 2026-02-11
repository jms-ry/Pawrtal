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
    // Get source ID from query parameter (PayMongo redirects with ?id=src_xxx)
    $sourceId = $request->query('id');
    
    $donation = null;
    
    if ($sourceId) {
        // Find the donation by payment_intent_id
        $donation = Donation::where('payment_intent_id', $sourceId)
            ->where('donation_type', 'monetary')
            ->first();
    }
    
    return Inertia::render('Donate/Success', [
        'donation' => $donation
    ]);
}
}
