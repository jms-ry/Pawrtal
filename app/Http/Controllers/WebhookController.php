<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\PayMongoService;
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
    // Log the COMPLETE incoming webhook for debugging
    Log::info('===== PayMongo Webhook Received =====');
    Log::info('Full Payload', ['payload' => $request->all()]);
    Log::info('Headers', ['headers' => $request->headers->all()]);

    try {
        $payload = $request->all();
        
        // Verify webhook payload
        $paymongoService = new PayMongoService();
        if (!$paymongoService->verifyWebhookPayload($payload)) {
            Log::warning('Webhook verification failed', ['payload' => $payload]);
            return response()->json(['message' => 'Invalid webhook'], 400);
        }

        // Extract event data
        $eventType = $payload['data']['attributes']['type'];
        $eventData = $payload['data']['attributes']['data'];

        Log::info('Event Type Extracted', [
            'type' => $eventType,
            'event_data' => $eventData
        ]);

        // Handle different event types
        switch ($eventType) {
            case 'source.chargeable':
                Log::info('Handling source.chargeable');
                $this->handleSourceChargeable($eventData);
                break;
                
            case 'payment.paid':
                Log::info('Handling payment.paid');
                $this->handlePaymentPaid($eventData);
                break;
                
            case 'payment.failed':
                Log::info('Handling payment.failed');
                $this->handlePaymentFailed($eventData);
                break;
                
            default:
                Log::info('Unhandled webhook event type', ['type' => $eventType]);
        }

        Log::info('===== Webhook Handled Successfully =====');
        return response()->json(['message' => 'Webhook handled successfully'], 200);

    } catch (\Exception $e) {
        Log::error('===== Webhook Handling Failed =====', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json(['message' => 'Webhook processing failed'], 500);
    }
}

  /**
    * Handle source.chargeable event
    * This fires when payment source is ready to be charged
  */
  private function handleSourceChargeable($eventData)
  {
    $sourceId = $eventData['id'] ?? null;
        
    if (!$sourceId) {
      Log::warning('Source ID missing in source.chargeable event');
      return;
    }

    Log::info('Source chargeable received', [
      'source_id' => $sourceId,
      'note' => 'No action needed - waiting for payment.paid or payment.failed'
    ]);
  }

  /**
    * Handle payment.paid event
    * This fires when payment is successfully completed
  */
  private function handlePaymentPaid($eventData)
{
    Log::info('=== handlePaymentPaid START ===', ['eventData' => $eventData]);
    
    $sourceId = $eventData['attributes']['source']['id'] ?? null;
    $paymentId = $eventData['id'] ?? null;
    
    Log::info('Extracted IDs', [
        'source_id' => $sourceId,
        'payment_id' => $paymentId
    ]);
    
    if (!$sourceId) {
        Log::warning('Source ID missing in payment.paid event');
        return;
    }

    Log::info('Searching for donation', ['payment_intent_id' => $sourceId]);

    // Find donation by payment_intent_id
    $donation = Donation::where('payment_intent_id', $sourceId)->first();
    
    if (!$donation) {
        Log::error('DONATION NOT FOUND!', [
            'source_id' => $sourceId,
            'all_donations' => Donation::where('donation_type', 'monetary')->get(['id', 'payment_intent_id'])
        ]);
        return;
    }

    Log::info('Donation found, updating...', [
        'donation_id' => $donation->id,
        'current_payment_status' => $donation->payment_status,
        'current_status' => $donation->status
    ]);

    // Update donation: payment succeeded
    $donation->update([
        'payment_status' => 'paid',
        'status' => 'accepted',
        'transaction_reference' => $paymentId,
        'paid_at' => now(),
    ]);

    $donation->refresh();

    Log::info('Donation updated successfully!', [
        'donation_id' => $donation->id,
        'new_payment_status' => $donation->payment_status,
        'new_status' => $donation->status,
        'transaction_reference' => $donation->transaction_reference,
        'paid_at' => $donation->paid_at
    ]);
}

  /**
    * Handle payment.failed event
    * This fires when payment fails or is cancelled
  */
  private function handlePaymentFailed($eventData)
  {
    $sourceId = $eventData['attributes']['source']['id'] ?? null;
        
    if (!$sourceId) {
      Log::warning('Source ID missing in payment.failed event');
      return;
    }

    Log::info('Payment failed', ['source_id' => $sourceId]);

    // Find donation by payment_intent_id
    $donation = Donation::where('payment_intent_id', $sourceId)->first();
        
    if (!$donation) {
      Log::warning('Donation not found for failed payment', ['source_id' => $sourceId]);
      return;
    }

    // Update donation: payment failed
    $donation->update([
      'payment_status' => 'failed',
      'status' => 'cancelled', // Cancel donation when payment fails
    ]);

    Log::info('Donation marked as failed and cancelled', [
      'donation_id' => $donation->id
    ]);
  }
}