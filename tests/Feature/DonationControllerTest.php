<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DonationControllerTest extends TestCase
{
  use RefreshDatabase;

  /** Start of store function test cases */
  public function test_guest_cannot_store_donation()
  {
    $donationData = [
      'user_id' => 1,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Lorem ipsum'],
      'item_quantity' => [1],
      'pick_up_location' => ['Location Test'],
      'contact_person' => ['Test Person'],
    ];

    $response = $this->post(route('donations.store'), $donationData);
        
    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('donations', 0);
  }

  public function test_logged_user_can_store_single_in_kind_donation_with_image()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $image = UploadedFile::fake()->image('donation.jpg');

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Used clothing'],
      'item_quantity' => [5],
      'pick_up_location' => ['123 Main St'],
      'contact_person' => ['John Doe'],
      'donation_image' => [$image],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
    $response->assertSessionHas('success', 'In-kind Donation has been submitted!');
        
    $this->assertDatabaseCount('donations', 1);
    $this->assertDatabaseHas('donations', [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => 'Used clothing',
      'item_quantity' => 5,
      'pick_up_location' => '123 Main St',
      'contact_person' => 'John Doe',
    ]);

    $donation = Donation::first();
    $this->assertNotNull($donation->donation_image);
    Storage::disk('public')->assertExists($donation->donation_image);
  }

  public function test_logged_user_can_store_single_in_kind_donation_without_image()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Books'],
      'item_quantity' => [10],
      'pick_up_location' => ['456 Oak Ave'],
      'contact_person' => ['Jane Smith'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
    $response->assertSessionHas('success', 'In-kind Donation has been submitted!');
        
    $this->assertDatabaseCount('donations', 1);
    $this->assertDatabaseHas('donations', [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'item_description' => 'Books',
      'donation_image' => null,
    ]);
  }

  public function test_logged_user_can_store_multiple_in_kind_donations()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $image1 = UploadedFile::fake()->image('donation1.jpg');
    $image2 = UploadedFile::fake()->image('donation2.jpg');
    $image3 = UploadedFile::fake()->image('donation3.jpg');

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Clothing', 'Books', 'Toys'],
      'item_quantity' => [5, 10, 3],
      'pick_up_location' => ['Location A', 'Location B', 'Location C'],
      'contact_person' => ['Person A', 'Person B', 'Person C'],
      'donation_image' => [$image1, $image2, $image3],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
    $response->assertSessionHas('success', 'In-kind Donations (3 items) have been submitted!');
        
    $this->assertDatabaseCount('donations', 3);
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Clothing',
      'item_quantity' => 5,
    ]);
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Books',
      'item_quantity' => 10,
    ]);
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Toys',
      'item_quantity' => 3,
    ]);
  }

  public function test_logged_user_can_store_multiple_donations_with_mixed_images()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $image1 = UploadedFile::fake()->image('donation1.jpg');
    // No image for second donation
    $image3 = UploadedFile::fake()->image('donation3.jpg');

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Item with image', 'Item without image', 'Another with image'],
      'item_quantity' => [1, 2, 3],
      'pick_up_location' => ['Loc 1', 'Loc 2', 'Loc 3'],
      'contact_person' => ['Contact 1', 'Contact 2', 'Contact 3'],
      'donation_image' => [0 => $image1, 2 => $image3], // Note: index 1 is missing
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
        
    $this->assertDatabaseCount('donations', 3);
        
    $donations = Donation::all();
    $this->assertNotNull($donations[0]->donation_image);
    $this->assertNull($donations[1]->donation_image);
    $this->assertNotNull($donations[2]->donation_image);
  }

  public function test_item_quantity_defaults_to_one_when_not_provided()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Item A', 'Item B'],
      'item_quantity' => [0 => 5], // Only first item has quantity
      'pick_up_location' => ['Location 1', 'Location 2'],
      'contact_person' => ['Contact 1', 'Contact 2'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Item A',
      'item_quantity' => 5,
    ]);
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Item B',
      'item_quantity' => 1, // Should default to 1
    ]);
  }

  public function test_pick_up_location_can_be_null()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Item A', 'Item B'],
      'item_quantity' => [1, 2],
      'pick_up_location' => [0 => 'Location 1'], // Only first item has location
      'contact_person' => ['Contact 1', 'Contact 2'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Item A',
      'pick_up_location' => 'Location 1',
    ]);
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Item B',
      'pick_up_location' => null,
    ]);
  }

  public function test_contact_person_can_be_null()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Item A', 'Item B'],
      'item_quantity' => [1, 2],
      'pick_up_location' => ['Location 1', 'Location 2'],
      'contact_person' => [0 => 'Contact 1'], // Only first item has contact
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Item A',
      'contact_person' => 'Contact 1',
    ]);
        
    $this->assertDatabaseHas('donations', [
      'item_description' => 'Item B',
      'contact_person' => null,
    ]);
  }

  public function test_store_validates_required_fields_for_in_kind_donation()
  {
    $user = User::factory()->create();

    // Missing required fields: item_description and item_quantity
    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      // item_description is missing
      // item_quantity is missing
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertSessionHasErrors(['item_description', 'item_quantity']);
    $this->assertDatabaseCount('donations', 0);
  }

  public function test_donation_type_field_is_required()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      // donation_type is missing
      'status' => 'pending',
      'item_description' => ['Test item'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertSessionHasErrors(['donation_type']);
    $this->assertDatabaseCount('donations', 0);
  }

  public function test_user_id_field_is_required()
  {
    $user = User::factory()->create();

    $donationData = [
      // user_id is missing
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Test item'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertSessionHasErrors(['user_id']);
    $this->assertDatabaseCount('donations', 0);
  }

  public function test_status_field_is_stored_correctly()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Test item'],
      'item_quantity' => [1],
      'pick_up_location' => ['Test location'],
      'contact_person' => ['Test person'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
        
    $this->assertDatabaseHas('donations', [
      'status' => 'pending',
    ]);
  }
  public function test_donation_image_must_be_valid_image_file_when_provided()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Test item'],
      'item_quantity' => [1],
      'pick_up_location' => ['Test location'],
      'contact_person' => ['Test person'],
      'donation_image' => [$invalidFile],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertSessionHasErrors(['donation_image.0']);
    $this->assertDatabaseCount('donations', 0);
  }

  public function test_success_message_is_singular_for_one_donation()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Single item'],
      'item_quantity' => [1],
      'pick_up_location' => ['Location'],
      'contact_person' => ['Person'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertSessionHas('success', 'In-kind Donation has been submitted!');
  }

  public function test_success_message_is_plural_for_multiple_donations()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => ['Item 1', 'Item 2', 'Item 3', 'Item 4', 'Item 5'],
      'item_quantity' => [1, 2, 3, 4, 5],
      'pick_up_location' => ['Loc 1', 'Loc 2', 'Loc 3', 'Loc 4', 'Loc 5'],
      'contact_person' => ['P1', 'P2', 'P3', 'P4', 'P5'],
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertSessionHas('success', 'In-kind Donations (5 items) have been submitted!');
  }

  public function test_monetary_donation_type_is_handled()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'status' => 'accepted',
      'amount' => 100.00,
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    // Since monetary donation is not implemented, adjust expectations
    // This test documents expected behavior for future implementation
    $response->assertRedirect(route('users.myDonations'));
    // Add assertions based on your future implementation
  }

  public function test_amount_is_required_for_monetary_donation()
  {
    $user = User::factory()->create();

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'monetary',
      'status' => 'pending',
      // amount is missing
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertSessionHasErrors(['amount']);
    $this->assertDatabaseCount('donations', 0);
  }

  public function test_handles_large_number_of_donations()
  {
    $user = User::factory()->create();

    $count = 50;
    $descriptions = array_fill(0, $count, 'Item');
    $quantities = array_fill(0, $count, 1);
    $locations = array_fill(0, $count, 'Location');
    $contacts = array_fill(0, $count, 'Contact');

    $donationData = [
      'user_id' => $user->id,
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'item_description' => $descriptions,
      'item_quantity' => $quantities,
      'pick_up_location' => $locations,
      'contact_person' => $contacts,
    ];

    $response = $this->actingAs($user)->post(route('donations.store'), $donationData);

    $response->assertRedirect(route('users.myDonations'));
    $response->assertSessionHas('success', "In-kind Donations ($count items) have been submitted!");
    $this->assertDatabaseCount('donations', $count);
  }

  /** End of store function test cases */

  /** Start of update function test cases */
  public function test_guest_cannot_update_donation()
  {
    $donation = Donation::factory()->inKind()->create();
      
    $response = $this->put(route('donations.update', $donation), [
      'item_description' => 'Hacked Donation',
    ]);
      
    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('donations', ['item_description' => 'Hacked Donation']);
  }

  public function test_non_admin_or_staff_user_cannot_update_others_donation()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'item_description' => 'Original donation',
      'user_id' => $user2->id,
    ]);

    $this->actingAs($user1);

    $response = $this->put(route('donations.update', $donation), [
      'item_description' => 'Hacked donation',
      'item_quantity' => 1,
      'pick_up_location' => 'test loaction',
      'contact_person' => 'test contact person',
      'user_id' => $user2->id,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['item_description' => 'Original donation']);

  }

  public function test_donation_owner_can_update_its_own_donation_details()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $updatedData = [
      'item_description' => 'Updated Description',
    ];
    
    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('info','Donation has been updated.');
    $this->assertDatabaseHas('donations', $updatedData);
  }

  public function test_updating_multiple_fields_at_once()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $updatedData = [
      'item_description' => 'Updated Description',
      'item_quantity' => 2,
      'pick_up_location' => 'Updated Location',
      'contact_person' => 'Updated Contact Person',
    ];
    
    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('info','Donation has been updated.');
    $this->assertDatabaseHas('donations', $updatedData);
  }

  public function test_donation_owner_can_update_its_donation_image()
  {
    Storage::fake('public'); // Add this to fake the storage
    
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $newImage = UploadedFile::fake()->image('donation.jpg');
    
    $updatedData = [
      'donation_image' => $newImage
    ];
    
    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('info','Donation has been updated.');
    
    // Refresh the donation to get updated data from database
    $donation->refresh();
    
    // Assert the image path is stored
    $this->assertNotNull($donation->donation_image);
    $this->assertStringContainsString('images/donation/donation_images', $donation->donation_image);
    
    // Assert the file exists in storage
    Storage::disk('public')->assertExists($donation->donation_image);
  }

  public function test_donation_owner_can_cancel_its_donation()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $updatedData = [
      'status' => 'cancelled'
    ];
    
    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('warning','Donation has been cancelled.');
    $this->assertDatabaseHas('donations', $updatedData);
  }

  public function test_donation_owner_cannot_accept_its_donation()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $updatedData = [
      'status' => 'accepted'
    ];
    
    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['status' => 'pending']);
  }

  public function test_donation_owner_cannot_reject_its_donation()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $updatedData = [
      'status' => 'rejected'
    ];
    
    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['status' => 'pending']);
  }

  public function test_non_admin_or_staff_cannot_cancel_others_donation()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user2->id,
    ]);

    $this->actingAs($user1);

    $response = $this->put(route('donations.update', $donation), [
      'status' => 'cancelled',
      'user_id' => $user2->id,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['status'=> 'pending']);
  }

  public function test_admin_cannot_update_other_donation_details()
  {
    $admin = User::factory()->admin()->create();
    $user2 = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'item_description' => 'Original donation',
      'user_id' => $user2->id,
    ]);

    $this->actingAs($admin);

    $response = $this->put(route('donations.update', $donation), [
      'item_description' => 'Hacked donation',
      'item_quantity' => 1,
      'pick_up_location' => 'test loaction',
      'contact_person' => 'test contact person',
      'user_id' => $user2->id,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['item_description' => 'Original donation']);
  }

  public function test_admin_can_accept_donation()
  {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);

    $this->actingAs($admin);

    $updatedData = [
      'status' => 'accepted'
    ];

    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('success','Donation has been accepted.');
    $this->assertDatabaseHas('donations', $updatedData);
  }

  public function test_admin_can_reject_donation()
  {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);

    $this->actingAs($admin);

    $updatedData = [
      'status' => 'rejected'
    ];

    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('error','Donation has been rejected.');
    $this->assertDatabaseHas('donations', $updatedData);
  }

  public function test_updating_nonexistent_donation_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $updatedData = [
      'item_description' => 'Updated Description',
      'item_quantity' => 2,
      'pick_up_location' => 'Updated Location',
      'contact_person' => 'Updated Contact Person',
    ];

    $response = $this->put(route('donations.update',99999), $updatedData);
    $response->assertNotFound();
  }

  public function test_staff_can_accept_donations()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);

    $this->actingAs($staff);

    $updatedData = [
      'status' => 'accepted'
    ];

    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('success','Donation has been accepted.');
    $this->assertDatabaseHas('donations', $updatedData);
  }

  public function test_staff_can_reject_donations()
  {
    $user = User::factory()->create();
    $staff = User::factory()->staff()->create();

    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);

    $this->actingAs($staff);

    $updatedData = [
      'status' => 'rejected'
    ];

    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertRedirect();
    $response->assertSessionHas('error','Donation has been rejected.');
    $this->assertDatabaseHas('donations', $updatedData);
  }

  public function test_cannot_update_with_invalid_status()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $updatedData = [
      'status' => 'invalid'
    ];
    
    $response = $this->put(route('donations.update', $donation), $updatedData);
    
    $response->assertSessionHasErrors('status');
  }

  public function test_updating_image_deletes_old_image()
  {
    Storage::fake('public');
    
    $user = User::factory()->create();
    
    // Create donation with an initial image
    $oldImage = UploadedFile::fake()->image('old_donation.jpg');
    $donation = Donation::factory()->inKind()->create([
        'user_id' => $user->id,
        'donation_image' => $oldImage->store('images/donation/donation_images', 'public')
    ]);
    
    $oldImagePath = $donation->donation_image;
    
    // Verify old image exists
    Storage::disk('public')->assertExists($oldImagePath);
    
    $this->actingAs($user);
    
    // Upload new image
    $newImage = UploadedFile::fake()->image('new_donation.jpg');
    
    $response = $this->put(route('donations.update', $donation), [
        'donation_image' => $newImage
    ]);
    
    $response->assertRedirect();
    $response->assertSessionHas('info', 'Donation has been updated.');
    
    // Refresh donation
    $donation->refresh();
    
    // Assert old image is deleted
    Storage::disk('public')->assertMissing($oldImagePath);
    
    // Assert new image exists
    $this->assertNotNull($donation->donation_image);
    $this->assertNotEquals($oldImagePath, $donation->donation_image);
    Storage::disk('public')->assertExists($donation->donation_image);
  }

  public function test_updating_without_image_keeps_existing_image()
  {
    Storage::fake('public');
    
    $user = User::factory()->create();
    
    // Create a fake existing image path
    $existingImagePath = 'images/donation/donation_images/existing_donation.jpg';
    Storage::disk('public')->put($existingImagePath, 'fake-image-content');
    
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user->id,
      'donation_image' => $existingImagePath,
      'item_description' => 'Original Description'
    ]);
    
    $this->actingAs($user);
    
    // Update other fields without touching the image
    $response = $this->put(route('donations.update', $donation), [
      'item_description' => 'Updated Description',
      'item_quantity' => 5,
    ]);
    
    $response->assertRedirect();
    $response->assertSessionHas('info', 'Donation has been updated.');
    
    // Refresh donation
    $donation->refresh();
    
    // Assert the existing image is still there
    $this->assertEquals($existingImagePath, $donation->donation_image);
    Storage::disk('public')->assertExists($existingImagePath);
    
    // Assert other fields were updated
    $this->assertEquals('Updated Description', $donation->item_description);
    $this->assertEquals(5, $donation->item_quantity);

  }

  public function test_uploaded_donation_image_type_is_invalid()
  {
    Storage::fake('public');
    
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user->id,
      'item_description' => 'Test Donation'
    ]);
    
    $this->actingAs($user);
    
    // Try uploading a PDF file (invalid type)
    $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
    
    $response = $this->put(route('donations.update', $donation), [
      'item_description' => 'Test Donation',
      'donation_image' => $invalidFile
    ]);
    
    $response->assertSessionHasErrors('donation_image');
    $response->assertStatus(302);
  }

  public function test_uploaded_donation_image_file_size_exceed_size_limit()
  {
    Storage::fake('public');
    
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user->id,
      'item_description' => 'Test Donation'
    ]);
    
    $this->actingAs($user);
    
    // Create a file larger than 2048 KB (2MB limit)
    $oversizedImage = UploadedFile::fake()->create('huge_donation.jpg', 3000, 'image/jpeg'); // 3MB
    
    $response = $this->put(route('donations.update', $donation), [
        'item_description' => 'Test Donation',
        'donation_image' => $oversizedImage
    ]);
    
    $response->assertSessionHasErrors('donation_image');
    $response->assertStatus(302);

  }

  public function test_admin_cannot_cancel_donation()
  {
    $admin = User::factory()->admin()->create();
    $user2 = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user2->id,
    ]);

    $this->actingAs($admin);

    $response = $this->put(route('donations.update', $donation), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['status' => 'pending']);
  }

  public function test_staff_cannot_cancel_donation()
  {
    $staff = User::factory()->staff()->create();
    $user2 = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user2->id,
    ]);

    $this->actingAs($staff);

    $response = $this->put(route('donations.update', $donation), [
      'status' => 'cancelled'
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['status' => 'pending']);
  }

  public function test_staff_cannot_update_other_donation_details()
  {
    $staff = User::factory()->staff()->create();
    $user2 = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'item_description' => 'Original donation',
      'user_id' => $user2->id,
    ]);

    $this->actingAs($staff);

    $response = $this->put(route('donations.update', $donation), [
      'item_description' => 'Hacked donation',
      'item_quantity' => 1,
      'pick_up_location' => 'test loaction',
      'contact_person' => 'test contact person',
      'user_id' => $user2->id,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['item_description' => 'Original donation']);
  }
  /** End of update function test cases */


  /** Start of destroy/archive function test cases */

  public function test_guest_cannot_destroyArchive_donation()
  {
    $donation = Donation::factory()->inKind()->create();

    $response = $this->delete(route('donations.destroy', $donation));
    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('donations', ['id' => $donation->id]);
  }

  public function test_regular_user_cannot_destroyArchive_others_donation()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($user1);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['id' => $donation->id]);
  }

  public function test_destroyingArchiving_nonexistent_donation_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->delete(route('donations.destroy', 99999));
        
    $response->assertNotFound();
  }

  public function test_donation_owner_cannot_destroyArchive_pending_donations()
  {
    $user = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Pending donations cannot be deleted.');
  }

  public function test_donation_owner_can_destroyArchive_accepted_donations()
  {
    $user = User::factory()->create();

    $donation = Donation::factory()->inKind()->accepted()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_donation_owner_can_destroyArchive_cancelled_donations()
  {
    $user = User::factory()->create();

    $donation = Donation::factory()->inKind()->cancelled()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_donation_owner_can_destroyArchive_rejected_donations()
  {
    $user = User::factory()->create();

    $donation = Donation::factory()->inKind()->rejected()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_admin_user_cannot_destroyArchive_pending_donations()
  {
    $admin = User::factory()->admin()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($admin);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Pending donations cannot be deleted.');
  }

  public function test_admin_user_can_destroyArchive_accepted_donations()
  {
    $admin = User::factory()->admin()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->accepted()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($admin);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_admin_user_can_destroyArchive_cancelled_donations()
  {
    $admin = User::factory()->admin()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->cancelled()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($admin);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_admin_user_can_destroyArchive_rejected_donations()
  {
    $admin = User::factory()->admin()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->rejected()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($admin);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_staff_user_cannot_destroyArchive_pending_donations()
  {
    $staff = User::factory()->staff()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($staff);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Pending donations cannot be deleted.');
  }

  public function test_staff_user_can_destroyArchive_accepted_donations()
  {
    $staff = User::factory()->staff()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->accepted()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($staff);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_staff_user_can_destroyArchive_cancelled_donations()
  {
    $staff = User::factory()->staff()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->cancelled()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($staff);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }

  public function test_staff_user_can_destroyArchive_rejected_donations()
  {
    $staff = User::factory()->staff()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->rejected()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($staff);
    $response = $this->delete(route('donations.destroy', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('warning', 'Donation has been archived!');
  }
  public function test_donation_is_soft_deleted_when_destoryedArchived()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->accepted()->create([
      'user_id' => $user->id,
    ]);

    $donationId = $donation->id;

    $this->actingAs($user);
    $this->delete(route('donations.destroy', $donation));

    // This checks that the record exists with a non-null deleted_at
    $this->assertSoftDeleted('donations', ['id' => $donationId]);
  }

  public function test_trashed_donation_cannot_be_destroyed_again()
  {
    $user = User::factory()->create();
    $donation = Donation::factory()->inKind()->accepted()->trashed()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('donations.destroy', $donation));
    $response->assertForbidden();
  }
  /** End of destroy/archive function test cases */


  /** Start of restore/unarchive function test cases */
  public function test_guest_cannot_restore_donation()
  {
    $donation = Donation::factory()->inKind()->trashed()->create();

    $response = $this->patch(route('donations.restore', $donation));
    $response->assertRedirect(route('login'));
  }

  public function test_restoring_nonexistent_donation_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->patch(route('donations.restore', 99999));
        
    $response->assertNotFound();
  }

  public function test_regular_user_cannot_restore_donation()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $donation = Donation::factory()->inKind()->trashed()->create([
      'user_id' => $user2->id
    ]);

    $this->actingAs($user1);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertForbidden();
    $this->assertDatabaseHas('donations', ['id' => $donation->id]);
  }

  public function test_donation_owner_cannot_restore_non_trashed_donation()
  {
    $user1 = User::factory()->create();

    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user1);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertForbidden();
  }

  public function test_donation_owner_can_restore_trashed_donation()
  {
    $user1 = User::factory()->create();

    $donation = Donation::factory()->inKind()->trashed()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($user1);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('success',  'Donation has been restored!');
    $this->assertDatabaseHas('donations', ['id' => $donation->id]);
  }

  public function test_admin_user_cannot_restore_non_trashed_donation()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertForbidden();
  }

  public function test_admin_user_can_restore_trashed_donation()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $donation = Donation::factory()->inKind()->trashed()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($admin);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('success',  'Donation has been restored!');
    $this->assertDatabaseHas('donations', ['id' => $donation->id]);
  }

  public function test_staff_user_cannot_restore_non_trashed_donation()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertForbidden();
  }

  public function test_staff_user_can_restore_trashed_donation()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();
    $donation = Donation::factory()->inKind()->trashed()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertRedirect();
    $response->assertSessionHas('success',  'Donation has been restored!');
    $this->assertDatabaseHas('donations', ['id' => $donation->id]);
  }

  public function test_deleted_at_set_to_null_when_donation_is_restored()
  {
    $user = User::factory()->create();
    
    $donation = Donation::factory()->inKind()->trashed()->create([
      'user_id' => $user->id
    ]);
    
    // Verify donation is trashed
    $this->assertNotNull($donation->deleted_at);
    $this->assertTrue($donation->trashed());
    
    $this->actingAs($user);
    $response = $this->patch(route('donations.restore', $donation));
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Donation has been restored!');
    
    // Refresh the donation from database
    $donation->refresh();
    
    // Verify deleted_at is now null
    $this->assertNull($donation->deleted_at);
    $this->assertFalse($donation->trashed());
    
    // Verify donation can be found without withTrashed()
    $this->assertNotNull(Donation::find($donation->id));
  }

  public function test_non_trashed_donation_cannot_be_restored()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->staff()->create();
    $donation = Donation::factory()->inKind()->create([
      'user_id' => $user1->id
    ]);

    $this->actingAs($staff);
    $response = $this->patch(route('donations.restore', $donation));

    $response->assertForbidden();
  }
  /** End of restore/unarchive function test cases */

}
