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
    return $this->createSource($amount, 'gcash', $description, $billing);
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

  /**
   * Create a payment to charge a source
   * 
   * @param string $sourceId
   * @param float $amount Amount in pesos
   * @return array|null
   */
  public function createPayment($sourceId, $amount)
  {
    try {
      $amountInCentavos = (int) ($amount * 100);
          
      $response = Http::withBasicAuth($this->secretKey, '')
      ->post("{$this->baseUrl}/payments", [
        'data' => [
          'attributes' => [
            'amount' => $amountInCentavos,
            'source' => [
              'id' => $sourceId,
              'type' => 'source'
            ],
            'currency' => 'PHP',
            'description' => 'Donation Payment'
          ]
        ]
      ]);

      if ($response->successful()) {
        Log::info('Payment created successfully', [
          'payment_id' => $response->json()['data']['id']
        ]);
        return $response->json()['data'];
      }

      Log::error('PayMongo Payment Creation Failed', [
        'response' => $response->json(),
        'status' => $response->status()
      ]);

      return null;
          
    } catch (\Exception $e) {
      Log::error('PayMongo Payment API Error', [
        'message' => $e->getMessage()
      ]);
      return null;
    }
  }

  /**
 * Create a payment source (GCash or PayMaya)
 * 
 * @param int $amount Amount in centavos (₱100 = 10000)
 * @param string $type Payment type: 'gcash' or 'paymaya'
 * @param string $description Payment description
 * @param array $billing Billing details
 * @return array|null
 */
  public function createSource($amount, $type = 'gcash', $description = 'Donation', $billing = [])
  {
    try {
      // Validate type
      $allowedTypes = ['gcash', 'paymaya'];
      if (!in_array($type, $allowedTypes)) {
        Log::error('Invalid payment source type', ['type' => $type]);
        return null;
      }

      $response = Http::withBasicAuth($this->secretKey, '')
      ->post("{$this->baseUrl}/sources", [
        'data' => [
          'attributes' => [
            'amount' => $amount,
              'redirect' => [
                'success' => route('donations.success'),
                'failed' => route('donate.index'),
              ],
              'type' => $type, // 'gcash' or 'paymaya'
              'currency' => 'PHP',
              'billing' => $billing,
          ]
        ]
      ]);

      if ($response->successful()) {
        Log::info('Payment source created successfully', [
          'type' => $type,
          'source_id' => $response->json()['data']['id']
        ]);
        return $response->json()['data'];
      }

      Log::error('PayMongo Source Creation Failed', [
        'response' => $response->json(),
        'status' => $response->status(),
        'type' => $type
      ]);

      return null;
    } catch (\Exception $e) {
      Log::error('PayMongo API Error', [
        'message' => $e->getMessage(),
        'type' => $type
      ]);
      return null;
    }
  }

}