<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use App\Services\PayMongoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DonationCreatePaymentTest extends TestCase
{
  use RefreshDatabase;

  /**
    * Test: Guest user cannot create payment (redirected to login)
  */
  public function test_guest_user_cannot_create_payment()
  {
    $response = $this->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response->assertUnauthorized();
  }

  /**
    * Test: Authenticated user can create payment successfully
  */
  public function test_authenticated_user_can_create_payment()
  {
    $user = User::factory()->create();

    // Mock PayMongoService
    $mockSession = [
      'id' => 'cs_test_session_123',
      'attributes' => [
        'checkout_url' => 'https://checkout.paymongo.com/sessions/cs_test_session_123'
      ]
    ];

    $this->mock(PayMongoService::class, function ($mock) use ($mockSession) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
        ->with(10000, 'Donation to Ormoc Stray Oasis') // ₱100 = 10000 centavos
      ->andReturn($mockSession);
    });

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response->assertOk();
    $response->assertJson([
      'success' => true,
      'checkout_url' => 'https://checkout.paymongo.com/sessions/cs_test_session_123',
    ]);

    // Verify donation was created in database
    $this->assertDatabaseHas('donations', [
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'amount' => 100,
      'payment_method' => 'gcash',
      'payment_intent_id' => 'cs_test_session_123',
      'payment_status' => 'pending',
      'status' => 'pending',
    ]);
  }

  /**
    * Test: Amount is required
  */
  public function test_amount_is_required()
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
      ->postJson('/api/donations/create-payment', [
      'payment_method' => 'gcash',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['amount']);
  }

  /**
    * Test: Amount must be numeric
  */
  public function test_amount_must_be_numeric()
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 'not-a-number',
      'payment_method' => 'gcash',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['amount']);
  }

  /**
    * Test: Amount must be at least 1
  */
  public function test_amount_must_be_at_least_one()
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 0,
      'payment_method' => 'gcash',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['amount']);
  }

  /**
    * Test: Payment method is required
  */
  public function test_payment_method_is_required()
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['payment_method']);
  }

  /**
    * Test: Payment method must be gcash
  */
  public function test_payment_method_must_be_gcash()
  {
    $user = User::factory()->create();

    $invalidMethods = ['paymaya', 'card', 'bitcoin', 'paypal'];

    foreach ($invalidMethods as $method) {
      $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
        'amount' => 100,
        'payment_method' => $method,
      ]);

      $response->assertUnprocessable();
      $response->assertJsonValidationErrors(['payment_method']);
    }
  }

  /**
    * Test: Amount is correctly converted to centavos
  */
  public function test_amount_is_converted_to_centavos_correctly()
  {
    $user = User::factory()->create();

    $mockSession = [
      'id' => 'cs_test',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/test']
    ];

    $this->mock(PayMongoService::class, function ($mock) use ($mockSession) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
        ->with(25050, 'Donation to Ormoc Stray Oasis') // ₱250.50 = 25050 centavos
      ->andReturn($mockSession);
    });

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 250.50,
      'payment_method' => 'gcash',
    ]);

    $response->assertOk();
  }

  /**
    * Test: Failed PayMongo session creation returns error
  */
  public function test_failed_paymongo_session_returns_error()
  {
    $user = User::factory()->create();

    // Mock PayMongoService to return null (failure)
    $this->mock(PayMongoService::class, function ($mock) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andReturn(null); // Simulate failure
    });

    Log::shouldReceive('error')
      ->once()
    ->with('Payment creation failed', \Mockery::type('array'));

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response->assertStatus(500);
    $response->assertJson([
      'success' => false,
      'message' => 'Failed to create payment. Please try again.',
    ]);

    // Verify no donation was created
    $this->assertDatabaseCount('donations', 0);
  }

  /**
    * Test: Exception during payment creation rolls back transaction
  */
  public function test_exception_rolls_back_transaction()
  {
    $user = User::factory()->create();

    // Mock PayMongoService to throw exception
    $this->mock(PayMongoService::class, function ($mock) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andThrow(new \Exception('PayMongo API error'));
    });

    Log::shouldReceive('error')
      ->once()
    ->with('Payment creation failed', \Mockery::type('array'));

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response->assertStatus(500);

    // Verify no donation was created (transaction rolled back)
    $this->assertDatabaseCount('donations', 0);
  }

  /**
    * Test: Database transaction is committed on success
  */
  public function test_database_transaction_is_committed_on_success()
  {
    $user = User::factory()->create();

    $mockSession = [
      'id' => 'cs_test_transaction',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/test']
    ];

    $this->mock(PayMongoService::class, function ($mock) use ($mockSession) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andReturn($mockSession);
    });

    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('commit')->once();
    DB::shouldReceive('rollBack')->never();

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response->assertOk();
  }

  /**
    * Test: Donation record contains correct data
  */
  public function test_donation_record_contains_correct_data()
  {
    $user = User::factory()->create();

    $mockSession = [
      'id' => 'cs_test_data_check',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/test']
    ];

    $this->mock(PayMongoService::class, function ($mock) use ($mockSession) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andReturn($mockSession);
    });

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 500,
      'payment_method' => 'gcash',
    ]);

    $response->assertOk();

    $donation = Donation::first();

    $this->assertEquals($user->id, $donation->user_id);
    $this->assertEquals('monetary', $donation->donation_type);
    $this->assertEquals(500, $donation->amount);
    $this->assertEquals('gcash', $donation->payment_method);
    $this->assertEquals('cs_test_data_check', $donation->payment_intent_id);
    $this->assertEquals('pending', $donation->payment_status);
    $this->assertEquals('pending', $donation->status);
    $this->assertNull($donation->transaction_reference);
    $this->assertNull($donation->paid_at);
  }

  /**
    * Test: Response contains donation ID
  */
  public function test_response_contains_donation_id()
  {
    $user = User::factory()->create();

    $mockSession = [
      'id' => 'cs_test_response',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/test']
    ];

    $this->mock(PayMongoService::class, function ($mock) use ($mockSession) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andReturn($mockSession);
    });

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response->assertOk();
    $response->assertJsonStructure([
      'success',
      'donation_id',
      'checkout_url',
    ]);

    $donation = Donation::first();
    $response->assertJson([
      'donation_id' => $donation->id,
    ]);
  }

  /**
    * Test: Decimal amounts are handled correctly
  */
  public function test_decimal_amounts_are_handled_correctly()
  {
    $user = User::factory()->create();

    $testCases = [
      ['amount' => 10.50, 'centavos' => 1050],
      ['amount' => 100.99, 'centavos' => 10099],
      ['amount' => 1000.01, 'centavos' => 100001],
      ['amount' => 99.90, 'centavos' => 9990],
    ];

    foreach ($testCases as $testCase) {
      $mockSession = [
        'id' => 'cs_test_' . $testCase['amount'],
        'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/test']
      ];

      $this->mock(PayMongoService::class, function ($mock) use ($mockSession, $testCase) {
        $mock->shouldReceive('createCheckoutSession')
          ->once()
          ->with($testCase['centavos'], 'Donation to Ormoc Stray Oasis')
        ->andReturn($mockSession);
      });

      $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
        'amount' => $testCase['amount'],
        'payment_method' => 'gcash',
      ]);

      $response->assertOk();
    }
  }

  /**
    * Test: Multiple users can create payments independently
  */
  public function test_multiple_users_can_create_payments_independently()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $mockSession1 = [
      'id' => 'cs_user1_session',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/user1']
    ];

    $mockSession2 = [
      'id' => 'cs_user2_session',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/user2']
    ];

    // User 1 creates payment
    $this->mock(PayMongoService::class, function ($mock) use ($mockSession1) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andReturn($mockSession1);
    });

    $response1 = $this->actingAs($user1)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response1->assertOk();

    // User 2 creates payment
    $this->mock(PayMongoService::class, function ($mock) use ($mockSession2) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andReturn($mockSession2);
    });

    $response2 = $this->actingAs($user2)->postJson('/api/donations/create-payment', [
      'amount' => 200,
      'payment_method' => 'gcash',
    ]);

    $response2->assertOk();

    // Verify both donations exist
    $this->assertDatabaseHas('donations', [
      'user_id' => $user1->id,
      'amount' => 100,
    ]);

    $this->assertDatabaseHas('donations', [
      'user_id' => $user2->id,
      'amount' => 200,
    ]);
  }

  /**
    * Test: CSRF protection is disabled for this route
  */
  public function test_csrf_protection_is_disabled()
  {
    $user = User::factory()->create();

    $mockSession = [
      'id' => 'cs_csrf_test',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/test']
    ];

    $this->mock(PayMongoService::class, function ($mock) use ($mockSession) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andReturn($mockSession);
    });

    // Make request without CSRF token
    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    // Should succeed (CSRF is disabled)
    $response->assertOk();
  }

  /**
    * Test: Error is logged when payment creation fails
  */
  public function test_error_is_logged_when_payment_fails()
  {
    $user = User::factory()->create();

    $this->mock(PayMongoService::class, function ($mock) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
      ->andThrow(new \Exception('Network timeout'));
    });

    Log::shouldReceive('error')
      ->once()
    ->with('Payment creation failed', [
      'user_id' => $user->id,
      'amount' => 100,
      'payment_method' => 'gcash',
      'error' => 'Network timeout',
    ]);

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 100,
      'payment_method' => 'gcash',
    ]);

    $response->assertStatus(500);
  }

  /**
    * Test: Large amounts are handled correctly
  */
  public function test_large_amounts_are_handled_correctly()
  {
    $user = User::factory()->create();

    $mockSession = [
      'id' => 'cs_large_amount',
      'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/test']
    ];

    $this->mock(PayMongoService::class, function ($mock) use ($mockSession) {
      $mock->shouldReceive('createCheckoutSession')
        ->once()
        ->with(1000000, 'Donation to Ormoc Stray Oasis') // ₱10,000 = 1,000,000 centavos
      ->andReturn($mockSession);
    });

    $response = $this->actingAs($user)->postJson('/api/donations/create-payment', [
      'amount' => 10000,
      'payment_method' => 'gcash',
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('donations', [
      'user_id' => $user->id,
      'amount' => 10000,
    ]);
  }
}