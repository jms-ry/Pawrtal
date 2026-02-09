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
    // Log the incoming webhook for debugging
    Log::info('PayMongo Webhook Received', [
      'payload' => $request->all()
    ]);

    try {
      $payload = $request->all();
            
      // Verify webhook payload
      $paymongoService = new PayMongoService();
      if (!$paymongoService->verifyWebhookPayload($payload)) {
        Log::warning('Webhook verification failed', ['payload' => $payload]);
        return response()->json(['message' => 'Invalid webhook'], 400);
      }
            
      // Extract event data
      $eventType = $payload['data']['attributes']['type'] ?? null;
      $eventData = $payload['data']['attributes']['data'] ?? null;

      if (!$eventType || !$eventData) {
        Log::warning('Invalid webhook payload', ['payload' => $payload]);
        return response()->json(['message' => 'Invalid payload'], 400);
      }

      // Handle different event types
      switch ($eventType) {
        case 'source.chargeable':
          $this->handleSourceChargeable($eventData);
        break;
                    
        case 'payment.paid':
          $this->handlePaymentPaid($eventData);
        break;
                    
        case 'payment.failed':
          $this->handlePaymentFailed($eventData);
        break;
                    
        default:
          Log::info('Unhandled webhook event type', ['type' => $eventType]);
      }

      return response()->json(['message' => 'Webhook handled successfully'], 200);

    } catch (\Exception $e) {
      Log::error('Webhook handling failed', [
        'error' => $e->getMessage(),
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
    $sourceId = $eventData['attributes']['source']['id'] ?? null;
    $paymentId = $eventData['id'] ?? null;
        
    if (!$sourceId) {
      Log::warning('Source ID missing in payment.paid event');
      return;
    }

    Log::info('Payment paid', [
      'source_id' => $sourceId,
      'payment_id' => $paymentId
    ]);

    // Find donation by payment_intent_id
    $donation = Donation::where('payment_intent_id', $sourceId)->first();
        
    if (!$donation) {
      Log::warning('Donation not found for payment', ['source_id' => $sourceId]);
      return;
    }

    // Update donation: payment succeeded
    $donation->update([
      'payment_status' => 'paid',
      'status' => 'accepted', // Auto-accept monetary donations
      'transaction_reference' => $paymentId,
      'paid_at' => now(),
    ]);

    Log::info('Donation marked as paid and accepted', [
      'donation_id' => $donation->id,
      'payment_id' => $paymentId
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