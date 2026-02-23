<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Household;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  // User Model Tests
  // -------------------------------------------------------------------------
  // fullName()
  // -------------------------------------------------------------------------

  public function test_full_name_returns_headline_cased_first_and_last_name(): void
  {
    $user = User::factory()->make([
      'first_name' => 'john',
      'last_name'  => 'doe',
    ]);

    $this->assertSame('John Doe', $user->fullName());
  }

  public function test_full_name_handles_multi_word_names(): void
  {
    $user = User::factory()->make([
      'first_name' => 'mary joy',
      'last_name'  => 'dela cruz',
    ]);

    $this->assertSame('Mary Joy Dela Cruz', $user->fullName());
  }

  // -------------------------------------------------------------------------
  // getFirstNameAttribute() — accessor
  // -------------------------------------------------------------------------

  public function test_first_name_accessor_returns_headline_cased_value(): void
  {
    $user = User::factory()->make(['first_name' => 'john']);

    $this->assertSame('John', $user->first_name);
  }

  public function test_first_name_accessor_handles_multi_word_first_name(): void
  {
    $user = User::factory()->make(['first_name' => 'mary joy']);

    $this->assertSame('Mary Joy', $user->first_name);
  }

  public function test_first_name_accessor_is_applied_when_retrieved_from_database(): void
  {
    $user = User::factory()->create(['first_name' => 'john']);

    $this->assertSame('John', $user->fresh()->first_name);
  }

  // -------------------------------------------------------------------------
  // getRole()
  // -------------------------------------------------------------------------

  public function test_get_role_returns_headline_cased_admin(): void
  {
    $user = User::factory()->make(['role' => 'admin']);

    $this->assertSame('Admin', $user->getRole());
  }

  public function test_get_role_returns_headline_cased_staff(): void
  {
    $user = User::factory()->make(['role' => 'staff']);

    $this->assertSame('Staff', $user->getRole());
  }

  public function test_get_role_returns_headline_cased_regular_user(): void
  {
    $user = User::factory()->make(['role' => 'regular_user']);

    $this->assertSame('Regular User', $user->getRole());
  }

  // -------------------------------------------------------------------------
  // isAdminOrStaff()
  // -------------------------------------------------------------------------

  public function test_is_admin_or_staff_returns_true_for_admin(): void
  {
    $user = User::factory()->make(['role' => 'admin']);

    $this->assertTrue($user->isAdminOrStaff());
  }

  public function test_is_admin_or_staff_returns_true_for_staff(): void
  {
    $user = User::factory()->make(['role' => 'staff']);

    $this->assertTrue($user->isAdminOrStaff());
  }

  public function test_is_admin_or_staff_returns_false_for_regular_user(): void
  {
    $user = User::factory()->make(['role' => 'regular_user']);

    $this->assertFalse($user->isAdminOrStaff());
  }

  // -------------------------------------------------------------------------
  // isNonAdminOrStaff()
  // -------------------------------------------------------------------------

  public function test_is_non_admin_or_staff_returns_true_for_regular_user(): void
  {
    $user = User::factory()->make(['role' => 'regular_user']);

    $this->assertTrue($user->isNonAdminOrStaff());
  }

  public function test_is_non_admin_or_staff_returns_false_for_admin(): void
  {
    $user = User::factory()->make(['role' => 'admin']);

    $this->assertFalse($user->isNonAdminOrStaff());
  }

  public function test_is_non_admin_or_staff_returns_false_for_staff(): void
  {
    $user = User::factory()->make(['role' => 'staff']);

    $this->assertFalse($user->isNonAdminOrStaff());
  }

  // -------------------------------------------------------------------------
  // canAdopt()
  // -------------------------------------------------------------------------

  public function test_regular_user_with_address_and_household_can_adopt(): void
  {
    $user = User::factory()->create(['role' => 'regular_user']);
    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->assertTrue($user->canAdopt());
  }

  public function test_regular_user_without_address_cannot_adopt(): void
  {
    $user = User::factory()->create(['role' => 'regular_user']);
    Household::factory()->create(['user_id' => $user->id]);

    $this->assertFalse($user->canAdopt());
  }

  public function test_regular_user_without_household_cannot_adopt(): void
  {
    $user = User::factory()->create(['role' => 'regular_user']);
    Address::factory()->create(['user_id' => $user->id]);

    $this->assertFalse($user->canAdopt());
  }

  public function test_regular_user_without_address_and_household_cannot_adopt(): void
  {
    $user = User::factory()->create(['role' => 'regular_user']);

    $this->assertFalse($user->canAdopt());
  }

  public function test_admin_cannot_adopt_even_with_address_and_household(): void
  {
    $user = User::factory()->create(['role' => 'admin']);
    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->assertFalse($user->canAdopt());
  }

  public function test_staff_cannot_adopt_even_with_address_and_household(): void
  {
    $user = User::factory()->create(['role' => 'staff']);
    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->assertFalse($user->canAdopt());
  }

  // -------------------------------------------------------------------------
  // fullAddress()
  // -------------------------------------------------------------------------

  public function test_full_address_returns_null_when_user_has_no_address(): void
  {
    $user = User::factory()->create();

    $this->assertNull($user->fullAddress());
  }

  public function test_full_address_delegates_to_address_full_address_method(): void
  {
    $user    = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);

    $this->assertSame($address->fullAddress(), $user->fullAddress());
  }
}