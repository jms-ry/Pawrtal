<?php

namespace App\Http\Controllers;

use App\Services\PayMongoService;

class TestPayMongoController extends Controller
{
  public function test(PayMongoService $paymongo)
  {
    // Test: Create a ₱10 test source
    $source = $paymongo->createGCashSource(
      1000, // ₱10.00 in centavos
      'Test Donation',
      [
        'name' => 'Juan Dela Cruz',
        'email' => 'test@example.com',
        'phone' => '09123456789'
      ]
    );
        
    if ($source) {
      return response()->json([
        'success' => true,
        'source_id' => $source['id'],
        'checkout_url' => $source['attributes']['redirect']['checkout_url'],
        'status' => $source['attributes']['status']
      ]);
    }
        
    return response()->json([
      'success' => false,
      'message' => 'Failed to create source'
    ], 500);
  }
}