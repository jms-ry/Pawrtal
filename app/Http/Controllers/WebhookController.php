<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
  /**
    * Handle PayMongo webhook events
    * 
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
  */
  public function handlePayMongo(Request $request)
  {
    error_log('===== WEBHOOK ENDPOINT HIT =====');
    error_log('Payload: ' . json_encode($request->all()));
        
    try {
      $payload = $request->all();
            
      // Extract event type
      $eventType = $payload['data']['attributes']['type'] ?? null;
      $eventData = $payload['data']['attributes']['data'] ?? null;

      error_log('Event Type: ' . $eventType);
      error_log('Has Event Data: ' . ($eventData ? 'YES' : 'NO'));

      if (!$eventType || !$eventData) {
        Log::warning('Invalid webhook payload', ['payload' => $payload]);
        return response()->json(['message' => 'Invalid payload'], 400);
      }

      // Handle checkout session payment
      if ($eventType === 'checkout_session.payment.paid') {
        error_log('Handling checkout_session.payment.paid');
        $this->handleCheckoutSessionPaid($eventData);
      } else {
        Log::info('Unhandled webhook event type', ['type' => $eventType]);
      }

      return response()->json(['message' => 'Webhook handled successfully'], 200);

    } catch (\Exception $e) {
      error_log('ERROR: ' . $e->getMessage());
      Log::error('Webhook handling failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return response()->json(['message' => 'Webhook processing failed'], 500);
    }
  }

  /**
    * Handle checkout_session.payment.paid event
    * This fires when payment is successfully completed
  */
  private function handleCheckoutSessionPaid($eventData)
  {
    $sessionId = $eventData['id'] ?? null;
    $payments = $eventData['attributes']['payments'] ?? [];
    $paymentId = $payments[0]['id'] ?? null; // Get first payment
        
    error_log('handleCheckoutSessionPaid - Session ID: ' . $sessionId . ', Payment ID: ' . $paymentId);
        
    if (!$sessionId) {
      Log::warning('Session ID missing in checkout_session.payment.paid event');
      return;
    }

    Log::info('Checkout session payment paid', [
      'session_id' => $sessionId,
      'payment_id' => $paymentId
    ]);

    // Find donation by payment_intent_id (which stores checkout session ID)
    $donation = Donation::where('payment_intent_id', $sessionId)
      ->where('donation_type', 'monetary')
    ->first();
        
    if (!$donation) {
      error_log('Donation not found for session: ' . $sessionId);
      Log::error('Donation not found for checkout session', ['session_id' => $sessionId]);
      return;
    }

    error_log('Updating donation ID: ' . $donation->id);

    // Update donation: payment succeeded
    $donation->update([
      'payment_status' => 'paid',
      'status' => 'accepted',
      'transaction_reference' => $paymentId,
      'paid_at' => now(),
    ]);

    error_log('Donation updated successfully');
    Log::info('Donation marked as paid and accepted', [
      'donation_id' => $donation->id,
      'payment_id' => $paymentId,
      'session_id' => $sessionId
    ]);
  }
}