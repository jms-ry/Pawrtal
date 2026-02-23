<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationTest extends TestCase
{
  use RefreshDatabase;

  // Donation model tests
  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function makeDonation(array $attributes = []): Donation
  {
    return Donation::factory()->make($attributes);
  }

  private function createDonation(array $attributes = []): Donation
  {
    return Donation::factory()->create($attributes);
  }

  // -------------------------------------------------------------------------
  // isMonetary() / isInKind() / isPaid()
  // -------------------------------------------------------------------------

  public function test_is_monetary_returns_true_when_donation_type_is_monetary(): void
  {
    $donation = $this->makeDonation(['donation_type' => 'monetary']);

    $this->assertTrue($donation->isMonetary());
  }

  public function test_is_monetary_returns_false_when_donation_type_is_in_kind(): void
  {
    $donation = $this->makeDonation(['donation_type' => 'in-kind']);

    $this->assertFalse($donation->isMonetary());
  }

  public function test_is_in_kind_returns_true_when_donation_type_is_in_kind(): void
  {
    $donation = $this->makeDonation(['donation_type' => 'in-kind']);

    $this->assertTrue($donation->isInKind());
  }

  public function test_is_in_kind_returns_false_when_donation_type_is_monetary(): void
  {
    $donation = $this->makeDonation(['donation_type' => 'monetary']);

    $this->assertFalse($donation->isInKind());
  }

  public function test_is_paid_returns_true_when_payment_status_is_paid(): void
  {
    $donation = $this->makeDonation(['payment_status' => 'paid']);

    $this->assertTrue($donation->isPaid());
  }

  public function test_is_paid_returns_false_when_payment_status_is_not_paid(): void
  {
    $donation = $this->makeDonation(['payment_status' => 'pending']);

    $this->assertFalse($donation->isPaid());
  }

  // -------------------------------------------------------------------------
  // getDonationTypeFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_donation_type_formatted_returns_ucfirst_type_for_active_donation(): void
  {
    $donation = $this->makeDonation(['donation_type' => 'monetary']);

    $this->assertSame('Monetary', $donation->donation_type_formatted);
  }

  public function test_donation_type_formatted_returns_ucfirst_in_kind_for_active_donation(): void
  {
    $donation = $this->makeDonation(['donation_type' => 'in-kind']);

    $this->assertSame('In-kind', $donation->donation_type_formatted);
  }

  public function test_donation_type_formatted_appends_archived_label_for_soft_deleted_donation(): void
  {
    $donation = $this->createDonation(['donation_type' => 'monetary']);
    $donation->delete();

    $this->assertSame('Monetary (Archived)', $donation->fresh()->donation_type_formatted);
  }

  // -------------------------------------------------------------------------
  // getAmountFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_amount_formatted_returns_peso_formatted_amount_when_set(): void
  {
    $donation = $this->makeDonation(['amount' => 1500.00]);

    $this->assertSame('₱ 1,500.00', $donation->amount_formatted);
  }

  public function test_amount_formatted_returns_na_when_amount_is_null(): void
  {
    $donation = $this->makeDonation(['amount' => null]);

    $this->assertSame('N/A', $donation->amount_formatted);
  }

  public function test_amount_formatted_formats_large_numbers_with_comma_separator(): void
  {
    $donation = $this->makeDonation(['amount' => 10000.00]);

    $this->assertSame('₱ 10,000.00', $donation->amount_formatted);
  }

  // -------------------------------------------------------------------------
  // getItemDescriptionFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_item_description_formatted_returns_headline_cased_value_when_set(): void
  {
    $donation = $this->makeDonation(['item_description' => 'dog food']);

    $this->assertSame('Dog Food', $donation->item_description_formatted);
  }

  public function test_item_description_formatted_returns_na_when_null(): void
  {
    $donation = $this->makeDonation(['item_description' => null]);

    $this->assertSame('N/A', $donation->item_description_formatted);
  }

  public function test_item_description_formatted_returns_na_when_empty_string(): void
  {
    $donation = $this->makeDonation(['item_description' => '']);

    $this->assertSame('N/A', $donation->item_description_formatted);
  }

  // -------------------------------------------------------------------------
  // getItemQuantityFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_item_quantity_formatted_returns_value_when_set(): void
  {
    $donation = $this->makeDonation(['item_quantity' => 5]);

    $this->assertSame(5, $donation->item_quantity_formatted);
  }

  public function test_item_quantity_formatted_returns_na_when_null(): void
  {
    $donation = $this->makeDonation(['item_quantity' => null]);

    $this->assertSame('N/A', $donation->item_quantity_formatted);
  }

  public function test_item_quantity_formatted_returns_na_when_zero(): void
  {
    $donation = $this->makeDonation(['item_quantity' => 0]);

    $this->assertSame('N/A', $donation->item_quantity_formatted);
  }

  // -------------------------------------------------------------------------
  // getDonationDateFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_donation_date_formatted_returns_correctly_formatted_date(): void
  {
    $donation = $this->makeDonation(['donation_date' => '2025-03-15']);

    $this->assertSame('Mar 15, 2025', $donation->donation_date_formatted);
  }

  // -------------------------------------------------------------------------
  // getDonationImageUrlAttribute()
  // -------------------------------------------------------------------------

  public function test_donation_image_url_uses_storage_path_when_image_contains_donation_images_directory(): void
  {
    $donation = $this->makeDonation(['donation_image' => 'donation_images/photo.jpg']);

    $this->assertSame(asset('storage/donation_images/photo.jpg'), $donation->donation_image_url);
  }

  public function test_donation_image_url_uses_asset_path_when_image_does_not_contain_donation_images_directory(): void
  {
    $donation = $this->makeDonation(['donation_image' => 'images/default.jpg']);

    $this->assertSame(asset('images/default.jpg'), $donation->donation_image_url);
  }

  // -------------------------------------------------------------------------
  // getDonorNameFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_donor_name_formatted_returns_full_name_when_user_exists(): void
  {
    $user     = User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']);
    $donation = $this->createDonation(['user_id' => $user->id]);

    $this->assertSame($user->fullName(), $donation->donor_name_formatted);
  }

  public function test_donor_name_formatted_returns_na_when_user_is_null(): void
  {
    $donation = $this->makeDonation(['user_id' => null]);

    $this->assertSame('N/A', $donation->donor_name_formatted);
  }

  // -------------------------------------------------------------------------
  // getIsOwnedByLoggedUserAttribute()
  // -------------------------------------------------------------------------

  public function test_is_owned_by_logged_user_returns_string_true_when_authenticated_user_owns_the_donation(): void
  {
    $user     = User::factory()->create();
    $donation = $this->createDonation(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->assertSame('true', $donation->is_owned_by_logged_user);
  }

  public function test_is_owned_by_logged_user_returns_string_false_when_authenticated_user_does_not_own_the_donation(): void
  {
    $owner    = User::factory()->create();
    $other    = User::factory()->create();
    $donation = $this->createDonation(['user_id' => $owner->id]);

    $this->actingAs($other);

    $this->assertSame('false', $donation->is_owned_by_logged_user);
  }

  public function test_is_owned_by_logged_user_returns_string_false_when_not_authenticated(): void
  {
    $donation = $this->createDonation();

    $this->assertSame('false', $donation->is_owned_by_logged_user);
  }

  // -------------------------------------------------------------------------
  // getLoggedUserIsAdminOrStaffAttribute()
  // -------------------------------------------------------------------------

  public function test_logged_user_is_admin_or_staff_returns_string_true_for_admin(): void
  {
    $admin    = User::factory()->create(['role' => 'admin']);
    $donation = $this->createDonation();

    $this->actingAs($admin);

    $this->assertSame('true', $donation->logged_user_is_admin_or_staff);
  }

  public function test_logged_user_is_admin_or_staff_returns_string_true_for_staff(): void
  {
    $staff    = User::factory()->create(['role' => 'staff']);
    $donation = $this->createDonation();

    $this->actingAs($staff);

    $this->assertSame('true', $donation->logged_user_is_admin_or_staff);
  }

  public function test_logged_user_is_admin_or_staff_returns_string_false_for_regular_user(): void
  {
    $user     = User::factory()->create(['role' => 'regular_user']);
    $donation = $this->createDonation();

    $this->actingAs($user);

    $this->assertSame('false', $donation->logged_user_is_admin_or_staff);
  }

  public function test_logged_user_is_admin_or_staff_returns_string_false_when_not_authenticated(): void
  {
    $donation = $this->createDonation();

    $this->assertSame('false', $donation->logged_user_is_admin_or_staff);
  }

  // -------------------------------------------------------------------------
  // getPaymentMethodFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_payment_method_formatted_returns_headline_cased_value_when_set(): void
  {
    $donation = $this->makeDonation(['payment_method' => 'credit_card']);

    $this->assertSame('Credit Card', $donation->payment_method_formatted);
  }

  public function test_payment_method_formatted_returns_na_when_null(): void
  {
    $donation = $this->makeDonation(['payment_method' => null]);

    $this->assertSame('N/A', $donation->payment_method_formatted);
  }

  // -------------------------------------------------------------------------
  // getPaymentStatusFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_payment_status_formatted_returns_headline_cased_value_when_set(): void
  {
    $donation = $this->makeDonation(['payment_status' => 'paid']);

    $this->assertSame('Paid', $donation->payment_status_formatted);
  }

  public function test_payment_status_formatted_returns_na_when_null(): void
  {
    $donation = $this->makeDonation(['payment_status' => null]);

    $this->assertSame('N/A', $donation->payment_status_formatted);
  }

  // -------------------------------------------------------------------------
  // getTransactionReferenceFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_transaction_reference_formatted_returns_value_when_set(): void
  {
    $donation = $this->makeDonation(['transaction_reference' => 'TXN-20250315-001']);

    $this->assertSame('TXN-20250315-001', $donation->transaction_reference_formatted);
  }

  public function test_transaction_reference_formatted_returns_na_when_null(): void
  {
    $donation = $this->makeDonation(['transaction_reference' => null]);

    $this->assertSame('N/A', $donation->transaction_reference_formatted);
  }
}