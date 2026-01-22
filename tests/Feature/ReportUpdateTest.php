<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class ReportUpdateTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_update_report()
  {
    $report = Report::factory()->lost()->create();

    $response = $this->patch(route('reports.update', $report), [
      'animal_name' => 'Update animal name'
    ]);

    $response->assertRedirect(route('login'));
  }

  public function test_regular_user_cannot_update_others_report()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($user2);

    $updatedData = [
      'animal_name' => 'Update animal name',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertForbidden();
  }

  public function test_admin_user_cannot_update_others_report()
  {
    $user1 = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($admin);

    $updatedData = [
      'animal_name' => 'Update animal name',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertForbidden();
  }

  public function test_staff_user_cannot_update_others_report()
  {
    $user1 = User::factory()->create();
    $staff = User::factory()->admin()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
    ]);

    $this->actingAs($staff);

    $updatedData = [
      'animal_name' => 'Update animal name',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertForbidden();
  }

  public function test_report_owner_can_update_lost_report_of_any_status()
  {
    $user1 = User::factory()->create();

    //Simulate active report update
    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'animal_name' => 'Update animal name',
      'status' => 'active',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Lost Dog Report updated successfully!');

    //Simulate resolved report update
    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'animal_name' => 'Update animal name',
      'status' => 'resolved',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Lost Dog Report updated successfully!');
    
    $this->assertDatabaseCount('reports',2);
  }

  public function test_report_owner_can_update_found_report_of_any_status()
  {
    $user1 = User::factory()->create();

    //Simulate active report update
    $report = Report::factory()->found()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'condition' => 'Updated condition',
      'status' => 'active',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Found Dog Report updated successfully!');

    //Simulate resolved report update
    $report = Report::factory()->found()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'condition' => 'Updated condition',
      'status' => 'resolved',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Found Dog Report updated successfully!');
    
    $this->assertDatabaseCount('reports',2);
  }

  public function test_updating_non_existent_report_returns_404()
  {
    $user1 = User::factory()->create();

    $this->actingAs($user1);

    $updatedData = [
      'animal_name' => 'Update animal name',
    ];

    $response = $this->patch(route('reports.update', 999999), $updatedData);
    $response->assertNotFound();
  }

  public function test_cannot_update_report_with_invalid_sex()
  {
    $user1 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'sex' => 'not male, female or unknown',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertSessionHasErrors('sex');
  }

  public function test_cannot_update_report_with_invalid_size()
  {
    $user1 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'size' => 'not small, medium or large',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertSessionHasErrors('size');
  }

  public function test_cannot_update_report_with_invalid_status()
  {
    $user1 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'status' => 'not active or resolved',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertSessionHasErrors('status');
  }

  public function test_cannot_update_report_with_invalid_image_format()
  {
    Storage::fake('public');
    $user1 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertSessionHasErrors('image');
  }

  public function test_cannot_update_report_with_image_exceeding_max_size()
  {
    Storage::fake('public');
    $user1 = User::factory()->create();

    $report = Report::factory()->lost()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'image' => UploadedFile::fake()->create('report.jpg', 10000, ),
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertSessionHasErrors('image');
  }

  public function test_partial_update_of_report_succeeds()
  {
    $user1 = User::factory()->create();

    $report = Report::factory()->found()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'condition' => 'Updated condition',
      'temporary_shelter' => 'My House',
      'found_location' => 'Test Address'
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Found Dog Report updated successfully!');
  }

  public function test_updating_of_report_with_nullable_values_empty_succeeds()
  {
    $user1 = User::factory()->create();

    $report = Report::factory()->found()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'condition' => 'Updated condition',
      'temporary_shelter' => 'My House',
      'found_location' => 'Test Address',
      //the following fields are nullable (exclusive to lost report)
      'animal_name' => null,
      'last_seen_location' => null,
      'last_seen_date' => null
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Found Dog Report updated successfully!');
  }

  public function test_old_image_is_deleted_when_new_image_is_uploaded()
  {
    $user1 = User::factory()->create();

    $oldImage = UploadedFile::fake()->image('old_report.jpg');

    $report = Report::factory()->found()->create([
      'user_id' => $user1->id,
      'species' => 'dog',
      'image' => $oldImage->store('images/reports/reports_images', 'public'),
    ]);

    $this->actingAs($user1);

    $newImage = UploadedFile::fake()->image('new_report.jpg');

    $updatedData = [
      'image' => $newImage
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Found Dog Report updated successfully!');

    $report->refresh();

    // Assert old image is deleted
    Storage::disk('public')->assertMissing($oldImage);

    // Assert new image exists
    $this->assertNotNull($report->image);
    $this->assertNotEquals($oldImage, $report->image);
    Storage::disk('public')->assertExists($report->image);
  }

  public function test_image_is_updated_correctly_in_database()
  {
    $user1 = User::factory()->create();

    $report = Report::factory()->found()->create([
      'user_id' => $user1->id,
      'species' => 'dog',
    ]);

    $this->actingAs($user1);

    $newImage = UploadedFile::fake()->image('new_report.jpg');

    $updatedData = [
      'image' => $newImage
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Found Dog Report updated successfully!');

    $report->refresh();

    Storage::disk('public')->assertExists($report->image);

    // Assert database was updated (excluding the file object)
    $this->assertDatabaseHas('reports', [
      'id' => $report->id,
      'image' => $report->image, // actual stored path
    ]);
  }

  public function test_image_is_unchanged_when_no_new_image_is_uploaded()
  {
    $user1 = User::factory()->create();
    
    $newImage = UploadedFile::fake()->image('new_report.jpg');

    $report = Report::factory()->found()->create([
      'user_id' => $user1->id,
      'species' => 'dog',
      'image' => $newImage->store('images/reports/reports_images', 'public'),
    ]);

    $existingImagePath = $report->image;

    // Verify existing image exists
    Storage::disk('public')->assertExists($existingImagePath);

    $this->actingAs($user1);

    $updatedData = [
      'condition' => 'Updated condition',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Found Dog Report updated successfully!');

    $report->refresh();

    // Assert the profile image remains the same
    $this->assertEquals($existingImagePath, $report->image);

    $this->assertDatabaseHas('reports', [
      'id' => $report->id,
      'image' => $existingImagePath
    ]);

    Storage::disk('public')->assertExists($existingImagePath);
  }

  public function test_trashed_report_can_be_updated()
  {
    $user1 = User::factory()->create();

    //Simulate active report update
    $report = Report::factory()->lost()->trashed()->create([
      'user_id' => $user1->id,
      'species' => 'dog'
    ]);

    $this->actingAs($user1);

    $updatedData = [
      'animal_name' => 'Update animal name',
    ];

    $response = $this->patch(route('reports.update', $report), $updatedData);
    $response->assertRedirect();
    $response->assertSessionHas('info','Lost Dog (Archived) Report updated successfully!');

    // Ensure the record was updated in the database (even though it's soft deleted)
    $this->assertDatabaseHas('reports', array_merge(
      ['id' => $report->id],
      collect($updatedData)
        ->except(['image']) // exclude non-DB fields
      ->toArray()
    ));

    // Confirm it remains soft-deleted
    $this->assertSoftDeleted('reports', ['id' => $report->id]);
  }

  public function test_updating_report_when_original_image_no_longer_exists_succeeds()
  {
    // What if someone manually deleted the old image from storage?
    // Controller tries to delete non-existent file
    $user = User::factory()->create();
    $report = Report::factory()->create([
      'user_id' => $user->id,
      'image' => 'images/reports/reports_images/deleted_file.jpg' // doesn't exist
    ]);
    
    $this->actingAs($user);
    $newImage = UploadedFile::fake()->image('new.jpg');
    
    $response = $this->patch(route('reports.update', $report), [
      'image' => $newImage
    ]);
    
    $response->assertRedirect();
    $report->refresh();
    Storage::disk('public')->assertExists($report->image);
  }

  public function test_cannot_update_report_with_future_last_seen_date()
  {
    $user = User::factory()->create();
    $report = Report::factory()->lost()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $response = $this->patch(route('reports.update', $report), [
      'last_seen_date' => now()->addDays(5)->format('Y-m-d')
    ]);
    
    $response->assertSessionHasErrors('last_seen_date');
  }

  public function test_cannot_update_report_with_future_found_date()
  {
    $user = User::factory()->create();
    $report = Report::factory()->found()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $response = $this->patch(route('reports.update', $report), [
      'found_date' => now()->addDays(5)->format('Y-m-d')
    ]);
    
    $response->assertSessionHasErrors('found_date');
  }

  public function test_cannot_update_report_with_text_exceeding_max_length()
  {
    $user = User::factory()->create();
    $report = Report::factory()->lost()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $response = $this->patch(route('reports.update', $report), [
      'animal_name' => str_repeat('a', 256) // exceeds 255
    ]);
    
    $response->assertSessionHasErrors('animal_name');
  }

  public function test_cannot_update_report_with_invalid_date_format()
  {
    $user = User::factory()->create();
    $report = Report::factory()->lost()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);
    
    $response = $this->patch(route('reports.update', $report), [
      'last_seen_date' => 'not-a-date'
    ]);
    
    $response->assertSessionHasErrors('last_seen_date');
  }

  public function test_update_success_message_reflects_correct_species()
  {
    $user = User::factory()->create();
    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'species' => 'cat'
    ]);
    
    $this->actingAs($user);
    
    $response = $this->patch(route('reports.update', $report), [
      'animal_name' => 'Updated'
    ]);
    
    $response->assertSessionHas('info', 'Lost Cat Report updated successfully!');
  }

  public function test_cannot_update_user_id_through_request()
  {
    // Security: ensure user_id can't be changed
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $report = Report::factory()->lost()->create(['user_id' => $user1->id]);
    
    $this->actingAs($user1);
    
    $response = $this->patch(route('reports.update', $report), [
      'user_id' => $user2->id, // try to steal ownership
      'animal_name' => 'Updated'
    ]);
    
    $response->assertRedirect();
    $report->refresh();
    $this->assertEquals($report->user_id, $user1->id);
  }

  public function test_cannot_change_report_type_through_update()
  { 
    $user = User::factory()->create();
    $report = Report::factory()->lost()->create([
      'user_id' => $user->id,
      'type' => 'lost'
    ]);
    
    $this->actingAs($user);
    
    $response = $this->patch(route('reports.update', $report), [
      'type' => 'found', // Try to change type
      'animal_name' => 'Updated'
    ]);
    
    $response->assertRedirect();
    $report->refresh();
    
    // Type should remain unchanged
    $this->assertEquals('lost', $report->type);
  }
}
