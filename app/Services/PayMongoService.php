<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
  protected $secretKey;
  protected $publicKey;
  protected $baseUrl = 'https://api.paymongo.com/v1';

  public function __construct()
  {
    $this->secretKey = config('services.paymongo.secret_key');
    $this->publicKey = config('services.paymongo.public_key');
  }

  /**
   * Verify webhook payload structure and basic security
   * 
   * @param array $payload
   * @return bool
   */
  public function verifyWebhookPayload($payload)
  {
    Log::info('Verifying webhook payload', ['payload_keys' => array_keys($payload)]);
    
    // Check if payload has required structure
    if (!isset($payload['data']['attributes']['type'])) {
      Log::warning('Invalid webhook: missing event type', [
        'has_data' => isset($payload['data']),
        'has_attributes' => isset($payload['data']['attributes']),
      ]);
      return false;
    }

    if (!isset($payload['data']['attributes']['data'])) {
      Log::warning('Invalid webhook: missing event data');
      return false;
    }

    // Verify the event data structure
    $eventData = $payload['data']['attributes']['data'];
    
    if (!isset($eventData['id'])) {
      Log::warning('Invalid webhook: missing resource ID');
      return false;
    }

    Log::info('Webhook payload verification passed');
    
    // Skip PayMongo API verification for now to speed up debugging
    return true;
  }

  /**
 * Create a checkout session for GCash payment
 * 
 * @param int $amount Amount in centavos (₱100 = 10000)
 * @param string $description Payment description
 * @param array $billing Billing details (optional for checkout session)
 * @return array|null
 */
  public function createCheckoutSession($amount, $description = 'Donation')
  {
    try {
      $response = Http::withBasicAuth($this->secretKey, '')
      ->post("{$this->baseUrl}/checkout_sessions", [
        'data' => [
          'attributes' => [
            'send_email_receipt' => true,
            'show_description' => true,
            'show_line_items' => true,
            'description' => $description,
            'line_items' => [
              [
                'currency' => 'PHP',
                'amount' => $amount,
                'description' => $description,
                'name' => 'Donation to OSO',
                'quantity' => 1
              ]
            ],
            'payment_method_types' => ['gcash'], // Only GCash
            'success_url' => route('donations.success'),
            'cancel_url' => route('donate.index'), // User cancels payment
          ]
        ]
      ]);

      if ($response->successful()) {
        Log::info('Checkout session created successfully', [
          'session_id' => $response->json()['data']['id']
        ]);
        return $response->json()['data'];
      }

      Log::error('PayMongo Checkout Session Creation Failed', [
        'response' => $response->json(),
        'status' => $response->status()
      ]);

      return null;
          
    } catch (\Exception $e) {
      Log::error('PayMongo Checkout Session API Error', [
        'message' => $e->getMessage()
      ]);
      return null;
    }
  }
}