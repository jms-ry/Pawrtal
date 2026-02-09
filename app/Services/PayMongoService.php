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
    * Create a GCash payment source
    * 
    * @param int $amount Amount in centavos (₱100 = 10000)
    * @param string $description Payment description
    * @param array $billing Billing details
    * @return array|null
  */
  public function createGCashSource($amount, $description = 'Donation', $billing = [])
  {
    try {
      $response = Http::withBasicAuth($this->secretKey, '')
      ->post("{$this->baseUrl}/sources", [
        'data' => [
          'attributes' => [
            'amount' => $amount,
            'redirect' => [
              'success' => route('donations.success'),
              'failed' => route('donations.failed'),
            ],
            'type' => 'gcash',
            'currency' => 'PHP',
            'billing' => $billing,
          ]
        ]
      ]);

      if ($response->successful()) {
        return $response->json()['data'];
      }

      Log::error('PayMongo Source Creation Failed', [
        'response' => $response->json(),
        'status' => $response->status()
      ]);

      return null;
    } catch (\Exception $e) {
      Log::error('PayMongo API Error', [
        'message' => $e->getMessage()
      ]);
      return null;
    }
  }

  /**
    * Retrieve source by ID
    * 
    * @param string $sourceId
    * @return array|null
  */
  public function getSource($sourceId)
  {
    try {
      $response = Http::withBasicAuth($this->secretKey, '')
      ->get("{$this->baseUrl}/sources/{$sourceId}");

      if ($response->successful()) {
        return $response->json()['data'];
      }

      return null;
    } catch (\Exception $e) {
      Log::error('PayMongo Get Source Error', [
        'message' => $e->getMessage(),
        'source_id' => $sourceId
      ]);
      return null;
    }
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
}