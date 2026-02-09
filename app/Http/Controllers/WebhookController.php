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
    error_log('===== WEBHOOK ENDPOINT HIT =====');
    error_log('Payload: ' . json_encode($request->all()));
        
    try {
      $payload = $request->all();
            
      // Extract event type - CORRECTED PATH
      $eventType = $payload['data']['attributes']['type'] ?? null;
      $eventData = $payload['data']['attributes']['data'] ?? null;

      error_log('Event Type: ' . $eventType);
      error_log('Has Event Data: ' . ($eventData ? 'YES' : 'NO'));

      if (!$eventType || !$eventData) {
        Log::warning('Invalid webhook payload', ['payload' => $payload]);
        return response()->json(['message' => 'Invalid payload'], 400);
      }

      // Handle different event types
      switch ($eventType) {
        case 'source.chargeable':
          error_log('Calling handleSourceChargeable');
          $this->handleSourceChargeable($eventData);
        break;
                    
        case 'payment.paid':
          error_log('Calling handlePaymentPaid');
          $this->handlePaymentPaid($eventData);
        break;
                    
        case 'payment.failed':
          error_log('Calling handlePaymentFailed');
          $this->handlePaymentFailed($eventData);
        break;
                    
        default:
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
    * Handle source.chargeable event
    * This fires when payment source is ready to be charged
    * We just log it without updating the donation status
  */
  private function handleSourceChargeable($eventData)
  {
    $sourceId = $eventData['id'] ?? null;
    
    error_log('handleSourceChargeable - Source ID: ' . $sourceId);
    
    if (!$sourceId) {
      Log::warning('Source ID missing in source.chargeable event');
      return;
    }

    Log::info('Source chargeable received', [
      'source_id' => $sourceId,
      'status' => $eventData['attributes']['status'] ?? null
    ]);

    // Find donation
    $donation = Donation::where('payment_intent_id', $sourceId)->first();
    
    if (!$donation) {
      error_log('Donation not found for source: ' . $sourceId);
      Log::warning('Donation not found for source', ['source_id' => $sourceId]);
      return;
    }

    error_log('Creating payment for source: ' . $sourceId);

    // Create payment to charge the source
    try {
      $paymongoService = new PayMongoService();
      $payment = $paymongoService->createPayment($sourceId, $donation->amount);
        
      if (!$payment) {
        error_log('Failed to create payment');
        Log::error('Failed to create payment for source', ['source_id' => $sourceId]);
        return;
      }

      error_log('Payment created successfully: ' . $payment['id']);
      Log::info('Payment created', [
        'source_id' => $sourceId,
        'payment_id' => $payment['id']
      ]);

      // The payment.paid webhook will handle updating the donation
        
    } catch (\Exception $e) {
      error_log('Error creating payment: ' . $e->getMessage());
      Log::error('Error creating payment', [
        'source_id' => $sourceId,
        'error' => $e->getMessage()
      ]);
    }
  }

  /**
    * Handle payment.paid event
    * This fires when payment is successfully completed
  */
  private function handlePaymentPaid($eventData)
  {  
    $paymentId = $eventData['id'] ?? null;
    $sourceId = $eventData['attributes']['source']['id'] ?? null;
        
    error_log('handlePaymentPaid - Payment ID: ' . $paymentId . ', Source ID: ' . $sourceId);
        
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
      error_log('Donation not found for source: ' . $sourceId);
      Log::error('Donation not found for payment', ['source_id' => $sourceId]);
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
        
    error_log('handlePaymentFailed - Source ID: ' . $sourceId);
        
    if (!$sourceId) {
      Log::warning('Source ID missing in payment.failed event');
      return;
    }

    Log::info('Payment failed', ['source_id' => $sourceId]);

    // Find donation by payment_intent_id
    $donation = Donation::where('payment_intent_id', $sourceId)->first();
        
    if (!$donation) {
      error_log('Donation not found for source: ' . $sourceId);
      Log::warning('Donation not found for failed payment', ['source_id' => $sourceId]);
      return;
    }

    error_log('Marking donation as failed');

    // Update donation: payment failed
    $donation->update([
      'payment_status' => 'failed',
      'status' => 'cancelled',
    ]);

    error_log('Donation marked as failed');
    Log::info('Donation marked as failed and cancelled', [
      'donation_id' => $donation->id
    ]);
  }
}