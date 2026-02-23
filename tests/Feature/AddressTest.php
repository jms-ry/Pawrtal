<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Address;

class AddressTest extends TestCase
{
  use RefreshDatabase;

  public function test_full_address_returns_concatenated_address_components(): void
  {
    $address = Address::factory()->make([
      'barangay'    => 'Barangay 1',
      'municipality' => 'Municipality A',
      'province'     => 'Province X',
      'zip_code'     => '12345',
    ]);

    $expected = 'Barangay 1, Municipality A, Province X, 12345';
    $this->assertSame($expected, $address->fullAddress());
  }

  public function test_full_address_skips_null_values(): void
  {
    $address = Address::factory()->make([
      'barangay' => null,
      'municipality' => 'Municipality A',
      'province' => 'Province X',
      'zip_code' => null,
    ]);

    $this->assertSame(
      'Municipality A, Province X',
      $address->fullAddress()
    );
  }
}
