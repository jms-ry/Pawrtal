<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WebhookHandlerTest extends TestCase
{
  use RefreshDatabase;

  /**
    * Test: Successful payment updates donation to paid and accepted
  */
  public function test_successful_payment_updates_donation_to_paid_and_accepted()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_test_session_123',
      'amount' => 100,
      'payment_status' => 'pending',
      'status' => 'pending',
      'transaction_reference' => null,
      'paid_at' => null,
    ]);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_test_session_123',
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => [
                [
                  'id' => 'pay_test_payment_123',
                  'type' => 'payment',
                  'attributes' => [
                    'status' => 'paid'
                  ]
                ]
              ]
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();
    $response->assertJson(['message' => 'Webhook handled successfully']);

    // Verify donation was updated
    $donation->refresh();
    $this->assertEquals('paid', $donation->payment_status);
    $this->assertEquals('accepted', $donation->status);
    $this->assertEquals('pay_test_payment_123', $donation->transaction_reference);
    $this->assertNotNull($donation->paid_at);
  }

  /**
    * Test: Webhook with missing session ID logs warning and returns successfully
  */
  public function test_webhook_with_missing_session_id_logs_warning()
  {
    Log::shouldReceive('warning')
      ->once()
    ->with('Session ID missing in checkout_session.payment.paid event');

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            // Missing 'id' field
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => []
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();
  }

  /**
    * Test: Webhook with non-existent donation logs error
  */
  public function test_webhook_with_non_existent_donation_logs_error()
  {
    Log::shouldReceive('info')->once();
    Log::shouldReceive('error')
      ->once()
    ->with('Donation not found for checkout session', ['session_id' => 'cs_nonexistent']);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_nonexistent',
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => [
                ['id' => 'pay_123']
              ]
            ]
          ]
        ]
      ]
    ];

        $response = $this->postJson('/webhook/paymongo', $payload);

        $response->assertOk();
        
        // Verify no donations were created or updated
        $this->assertDatabaseMissing('donations', [
            'payment_intent_id' => 'cs_nonexistent'
        ]);
  }

  /**
    * Test: Webhook only updates monetary donations
  */
  public function test_webhook_only_updates_monetary_donations()
  {
    $user = User::factory()->create();
        
    // Create in-kind donation with same session ID
    $inKindDonation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'payment_intent_id' => 'cs_test_session_456',
      'payment_status' => 'pending',
      'status' => 'pending',
    ]);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_test_session_456',
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => [
                ['id' => 'pay_456']
              ]
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();

    // In-kind donation should NOT be updated
    $inKindDonation->refresh();
    $this->assertEquals('pending', $inKindDonation->payment_status);
    $this->assertEquals('pending', $inKindDonation->status);
  }

  /**
    * Test: Webhook with empty payments array still updates donation
  */
  public function test_webhook_with_empty_payments_array_updates_donation_without_payment_id()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_test_empty_payments',
      'payment_status' => 'pending',
      'status' => 'pending',
    ]);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_test_empty_payments',
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => [] // Empty payments array
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();

    $donation->refresh();
    $this->assertEquals('paid', $donation->payment_status);
    $this->assertEquals('accepted', $donation->status);
    $this->assertNull($donation->transaction_reference); // No payment ID available
    $this->assertNotNull($donation->paid_at);
  }

  /**
    * Test: Webhook with missing payments attribute handles gracefully
  */
  public function test_webhook_with_missing_payments_attribute_updates_donation()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_test_no_payments_attr',
      'payment_status' => 'pending',
      'status' => 'pending',
    ]);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_test_no_payments_attr',
            'type' => 'checkout_session',
            'attributes' => [
              // Missing 'payments' attribute entirely
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();

    $donation->refresh();
    $this->assertEquals('paid', $donation->payment_status);
    $this->assertEquals('accepted', $donation->status);
    $this->assertNull($donation->transaction_reference);
  }

  /**
    * Test: Multiple payments in array uses first payment ID
  */
  public function test_webhook_with_multiple_payments_uses_first_payment_id()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_test_multiple_payments',
      'payment_status' => 'pending',
      'status' => 'pending',
    ]);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_test_multiple_payments',
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => [
                ['id' => 'pay_first_payment'],
                ['id' => 'pay_second_payment'],
                ['id' => 'pay_third_payment'],
              ]
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();

    $donation->refresh();
    $this->assertEquals('pay_first_payment', $donation->transaction_reference);
  }

  /**
    * Test: Webhook updates already paid donation (idempotency)
  */
  public function test_webhook_is_idempotent_for_already_paid_donation()
  {
    $user = User::factory()->create();
        
    $paidAt = now()->subHour();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_already_paid',
      'payment_status' => 'paid',
      'status' => 'accepted',
      'transaction_reference' => 'pay_original',
      'paid_at' => $paidAt,
    ]);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
        'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_already_paid',
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => [
                ['id' => 'pay_duplicate']
              ]
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();

    // Verify it was updated (idempotent - safe to process multiple times)
    $donation->refresh();
    $this->assertEquals('paid', $donation->payment_status);
    $this->assertEquals('accepted', $donation->status);
    $this->assertEquals('pay_duplicate', $donation->transaction_reference); // Updated
  }

  /**
    * Test: Webhook logs correct information on success
  */
  public function test_webhook_logs_correct_information_on_success()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_logging_test',
      'payment_status' => 'pending',
      'status' => 'pending',
    ]);

    Log::shouldReceive('info')
      ->once()
    ->with('Checkout session payment paid', [
       'session_id' => 'cs_logging_test',
      'payment_id' => 'pay_logging_test'
    ]);

    Log::shouldReceive('info')
      ->once()
    ->with('Donation marked as paid and accepted', [
       'donation_id' => $donation->id,
        'payment_id' => 'pay_logging_test',
      'session_id' => 'cs_logging_test'
    ]);

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_logging_test',
            'type' => 'checkout_session',
            'attributes' => [
              'payments' => [
                ['id' => 'pay_logging_test']
              ]
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();
  }

  /**
    * Test: Webhook handles malformed payload gracefully
  */
  public function test_webhook_rejects_malformed_payload()
  {
    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          // Missing 'data' field entirely
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    // Should not crash, return 200
    $response->assertStatus(400);
    $response->assertJsonStructure(['message']);
  }

  /**
    * Test: Different event types are ignored gracefully
  */
  public function test_different_event_types_are_ignored()
  {
    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'source.chargeable', // Different event type
          'data' => [
            'id' => 'src_123',
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();
    $response->assertJson(['message' => 'Webhook handled successfully']);
  }

  /**
    * Test: Webhook endpoint is accessible without authentication
  */
  public function test_webhook_endpoint_is_accessible_without_authentication()
  {
    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_test',
            'attributes' => [
              'payments' => []
            ]
          ]
        ]
    ]
    ];

    // No actingAs() - unauthenticated request
    $response = $this->postJson('/webhook/paymongo', $payload);

    $response->assertOk();
  }

  /**
    * Test: paid_at timestamp is set to current time
  */
  public function test_paid_at_timestamp_is_set_correctly()
  {
    $user = User::factory()->create();
        
    $donation = Donation::factory()->create([
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'payment_intent_id' => 'cs_timestamp_test',
      'payment_status' => 'pending',
      'paid_at' => null,
    ]);

    $beforeWebhook = now()->subSecond();

    $payload = [
      'data' => [
        'type' => 'event',
        'attributes' => [
          'type' => 'checkout_session.payment.paid',
          'data' => [
            'id' => 'cs_timestamp_test',
            'attributes' => [
              'payments' => [['id' => 'pay_123']]
            ]
          ]
        ]
      ]
    ];

    $response = $this->postJson('/webhook/paymongo', $payload);

    $afterWebhook = now()->addSecond();

    $response->assertOk();

    $donation->refresh();
    $this->assertNotNull($donation->paid_at);
    $this->assertTrue(
      $donation->paid_at->between($beforeWebhook, $afterWebhook),
      'paid_at should be set to current timestamp'
    );
  }
}