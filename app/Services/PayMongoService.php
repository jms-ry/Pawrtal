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
    * Verify webhook signature
    * 
    * @param array $payload
    * @param string $signature
    * @return bool
  */
  public function verifyWebhookSignature($payload, $signature)
  {
    // PayMongo webhook signature verification
    // For now, we'll implement basic verification
    // You can enhance this based on PayMongo's documentation
    return true; // TODO: Implement proper signature verification
  }
}