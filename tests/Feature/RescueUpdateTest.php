<?php

namespace Tests\Feature;

use App\Models\Rescue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RescueUpdateTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_update_rescue()
  {
    $rescue = Rescue::factory()->create();

    $response = $this->put(route('rescues.update', $rescue), [
      'name' => 'Updated Name',
    ]);

    $response->assertRedirect(route('login'));
  }

  public function test_regular_user_cannot_update_rescue()
  {
    $user = User::factory()->create();
    $rescue = Rescue::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('rescues.update', $rescue), [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ]);

    $response->assertStatus(403);
  }

  public function test_admin_user_can_update_rescue()
  {
    $admin = User::factory()->admin()->create();
    $rescue = Rescue::factory()->create();

    $this->actingAs($admin);

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect();

    $response->assertSessionHas('info','Rescue Profile for '. $updatedData['name']. ' has been updated!');
    $this->assertDatabaseHas('rescues', $updatedData);

  }

  public function test_staff_user_can_update_rescue()
  {
    $staff = User::factory()->staff()->create();
    $rescue = Rescue::factory()->create();

    $this->actingAs($staff);

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect();

    $response->assertSessionHas('info','Rescue Profile for '. $updatedData['name']. ' has been updated!');
    $this->assertDatabaseHas('rescues', $updatedData);

  }

  public function test_updating_nonexistent_rescue_returns_404()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

     $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', 999999), $updatedData);

    $response->assertStatus(404);
  }

  public function test_partial_update_of_rescue_succeeds()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show',$rescue->id));
    $response->assertSessionHas('info','Rescue Profile for '. $updatedData['name']. ' has been updated!');

    $this->assertDatabaseHas('rescues', array_merge(
      ['id' => $rescue->id],$updatedData
    ));
  }

  public function test_old_profile_image_is_deleted_when_new_profile_image_is_uploaded()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $oldImage = UploadedFile::fake()->image('old_rescue.jpg');

    $rescue = Rescue::factory()->create([
      'profile_image' => $oldImage->store('images/rescues/profile_images', 'public'),
    ]);

    $oldImagePath = $rescue->profile_image;

    // Verify old image exists
    Storage::disk('public')->assertExists($oldImagePath);

    $this->actingAs($admin);

    $newImage = UploadedFile::fake()->image('new_rescue.jpg');

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'profile_image' => $newImage,
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show',$rescue->id));
    $response->assertSessionHas('info','Rescue Profile for '. $updatedData['name']. ' has been updated!');

    $rescue->refresh();

    // Assert old image is deleted
    Storage::disk('public')->assertMissing($oldImagePath);

    // Assert new image exists
    $this->assertNotNull($rescue->profile_image);
    $this->assertNotEquals($oldImagePath, $rescue->profile_image);
    Storage::disk('public')->assertExists($rescue->profile_image);
  }

  public function test_profile_image_is_updated_correctly_in_database()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $rescue = Rescue::factory()->create();

    $this->actingAs($admin);

    $newImage = UploadedFile::fake()->image('new_rescue.jpg');

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'profile_image' => $newImage,
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show',$rescue->id));
    $response->assertSessionHas('info','Rescue Profile for '. $updatedData['name']. ' has been updated!');

    // Get the updated rescue record
    $rescue->refresh();

    // Assert the new image file exists in storage
    Storage::disk('public')->assertExists($rescue->profile_image);

    // Assert database was updated (excluding the file object)
    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => $rescue->profile_image, // actual stored path
    ]);
  }

  public function test_profile_image_is_unchanged_when_no_new_image_is_uploaded()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $existingImage = UploadedFile::fake()->image('existing_rescue.jpg');

    $rescue = Rescue::factory()->create([
      'profile_image' => $existingImage->store('images/rescues/profile_images', 'public'),
    ]);

    $existingImagePath = $rescue->profile_image;

    // Verify existing image exists
    Storage::disk('public')->assertExists($existingImagePath);

    $this->actingAs($admin);

    // Prepare updated data WITHOUT new image
    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'Still the same friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      // Note: no 'profile_image' key
    ];

    // Perform update request
    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show', $rescue->id));
    $response->assertSessionHas('info', 'Rescue Profile for ' . $updatedData['name'] . ' has been updated!');

    // Refresh the model from the database
    $rescue->refresh();

    // Assert the profile image remains the same
    $this->assertEquals($existingImagePath, $rescue->profile_image);

    // Assert database has updated text fields but the same image path
    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'Still the same friendly dog',
      'profile_image' => $existingImagePath,
    ]);

    // Assert the old image still exists in storage
    Storage::disk('public')->assertExists($existingImagePath);
  }

  public function test_new_gallery_images_are_appended_to_existing_images()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $existingImage1 = UploadedFile::fake()->image('existing1.jpg');
    $existingImage2 = UploadedFile::fake()->image('existing2.jpg');

    $rescue = Rescue::factory()->create([
      'images' => [
        $existingImage1->store('images/rescues/gallery_images', 'public'),
        $existingImage2->store('images/rescues/gallery_images', 'public'),
      ],
    ]);

    // Confirm the old images exist
    foreach ($rescue->images as $path) {
      Storage::disk('public')->assertExists($path);
    }

    $this->actingAs($admin);

    $newImage1 = UploadedFile::fake()->image('new1.jpg');
    $newImage2 = UploadedFile::fake()->image('new2.jpg');

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'Still the same friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'images' => [$newImage1, $newImage2],
    ];

    // Perform the update request
    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show', $rescue->id));
    $response->assertSessionHas('info', 'Rescue Profile for ' . $updatedData['name'] . ' has been updated!');

    // Refresh model to get latest state
    $rescue->refresh();

    // Assert all new images exist in storage
    foreach ($rescue->images as $path) {
      Storage::disk('public')->assertExists($path);
    }

    // Assert that the new images were appended (not replaced)
    $this->assertCount(4, $rescue->images, 'Rescue should now have 4 images total.');

    // Assert that the existing image paths are still in the images array
    $this->assertTrue(in_array($existingImage1->hashName('images/rescues/gallery_images'), $rescue->images));
    $this->assertTrue(in_array($existingImage2->hashName('images/rescues/gallery_images'), $rescue->images));

    // Assert that the new image paths are also included
    $this->assertTrue(in_array($newImage1->hashName('images/rescues/gallery_images'), $rescue->images));
    $this->assertTrue(in_array($newImage2->hashName('images/rescues/gallery_images'), $rescue->images));

    // Assert database was updated with the merged images array
    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
      'name' => 'Updated Name',
    ]);

    // Assert total number of stored files is 4
    $this->assertCount(4, Storage::disk('public')->allFiles('images/rescues/gallery_images'));
  }

  public function test_gallery_images_field_is_unchanged_when_no_new_images_are_uploaded()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $existingImage1 = UploadedFile::fake()->image('existing1.jpg');
    $existingImage2 = UploadedFile::fake()->image('existing2.jpg');

    $rescue = Rescue::factory()->create([
      'images' => [
        $existingImage1->store('images/rescues/gallery_images', 'public'),
        $existingImage2->store('images/rescues/gallery_images', 'public'),
      ],
    ]);

    // Confirm the old images exist
    foreach ($rescue->images as $path) {
      Storage::disk('public')->assertExists($path);
    }

    $existingImages = $rescue->images;
    $this->actingAs($admin);

    // Prepare updated data WITHOUT new gallery images
    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'Still the same friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      //No images uploaded
    ];

    // Perform the update request
    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show', $rescue->id));
    $response->assertSessionHas('info', 'Rescue Profile for ' . $updatedData['name'] . ' has been updated!');

    // Refresh model to get latest state
    $rescue->refresh();

    // ✅ Assert that the gallery images array did not change
    $this->assertEquals($existingImages, $rescue->images, 'Gallery images should remain unchanged.');

    // ✅ Assert database reflects the same image paths
    $this->assertDatabaseHas('rescues', [
      'id' => $rescue->id,
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'Still the same friendly dog',
    ]);

    // ✅ Assert the old images still exist in storage
    foreach ($existingImages as $path) {
      Storage::disk('public')->assertExists($path);
    }

    // ✅ Optional: make sure no new files were added
    $this->assertCount(2, Storage::disk('public')->allFiles('images/rescues/gallery_images'));
  }

  public function test_gallery_images_uploaded_exceeds_max_5_images_per_request_fail_validation()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    // Create a rescue with no existing images
    $rescue = Rescue::factory()->create();

    // Upload 6 new images — exceeds max of 5
    $tooManyImages = [];
    for ($i = 1; $i <= 6; $i++) {
        $tooManyImages[] = UploadedFile::fake()->image("new{$i}.jpg");
    }

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Dog',
      'description' => 'Still the same friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'images' => $tooManyImages,
    ];

    // Perform the update request
    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    // Assert that validation fails specifically on the 'images' field
    $response->assertSessionHasErrors('images');

    // Ensure that none of the images were actually stored
    foreach ($tooManyImages as $image) {
      Storage::disk('public')->assertMissing('images/rescues/gallery_images/' . $image->hashName());
    }

    // Assert the database still has the old data (no change in images)
    $this->assertEquals($rescue->images, $rescue->fresh()->images);
  }

  public function test_updating_rescue_without_name_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      //'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('name');
  }

  public function test_updating_rescue_without_species_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      //'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('species');
  }

  public function test_updating_rescue_without_description_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      //'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('description');
  }

  public function test_updating_rescue_without_sex_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      //'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('sex');
  }

  public function test_updating_rescue_without_health_status_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      //'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('health_status');
  }

  public function test_updating_rescue_without_vaccination_status_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      //'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('vaccination_status');
  }

  public function test_updating_rescue_without_spayed_neutered_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      //'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('spayed_neutered');
  }

  public function test_updating_rescue_without_adoption_status_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      //'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('adoption_status');
  }

  public function test_updating_rescue_invalid_sex_value_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'test_invalid',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('sex');
  }

  public function test_updating_rescue_invalid_health_status_value_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'invalid healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('health_status');
  }

  public function test_updating_rescue_invalid_vaccination_status_value_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'invalid vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('vaccination_status');
  }

  public function test_updating_rescue_invalid_spayed_neutered_value_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'invalid true',
      'adoption_status' => 'available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('spayed_neutered');
  }

  public function test_updating_rescue_invalid_adoption_status_value_fails_validation()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'invalid available',
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertSessionHasErrors('adoption_status');
  }

  public function test_updating_rescue_with_invalid_profile_image_fails_validation()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'profile_image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);
    $response->assertSessionHasErrors('profile_image');
  }

  public function test_updating_rescue_with_profile_image_exceed_max_size_limit_fails_validation()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'profile_image' => UploadedFile::fake()->create('dog.jpg', 10000, 'image/jpg'), // 10MB
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);
    $response->assertSessionHasErrors('profile_image');
  }

  public function test_updating_rescue_with_invalid_gallery_image_fails_validation()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'images' => [
        UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
      ],
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);
    $response->assertSessionHasErrors('images.0');

  }

  public function test_updating_rescue_with_gallery_image_that_exceeds_max_file_size_fails_validation()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'images' => [
        UploadedFile::fake()->create('dog.jpg', 10000, 'image/jpg'),
      ],
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);
    $response->assertSessionHasErrors('images.0');
    
  }

  public function test_updating_rescue_with_all_nullable_fields_as_null_succeeds()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'breed' => null,
      'distinctive_features' => null,
      'age' => null,
      'size' => null,
      'color' => null,
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'images' => null,
      'profile_image' => null,
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show',$rescue->id));
    $response->assertSessionHas('info','Rescue Profile for '. $updatedData['name']. ' has been updated!');

    $this->assertDatabaseHas('rescues', [
      'name' => $updatedData['name'],
      'species' => $updatedData['species'],
      'breed' => $updatedData['breed'],
      'description' => $updatedData['description'],
      'sex' => $updatedData['sex'],
      'age' => $updatedData['age'],
      'size' => $updatedData['size'],
      'color' => $updatedData['color'],
      'distinctive_features' => $updatedData['distinctive_features'],
      'health_status' => $updatedData['health_status'],
      'vaccination_status' => $updatedData['vaccination_status'],
      'spayed_neutered' => $updatedData['spayed_neutered'],
      'adoption_status' => $updatedData['adoption_status'],
    ]);
  }

  public function test_uploading_non_image_file_in_gallery_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $rescue = Rescue::factory()->create();

    $invalidFile = UploadedFile::fake()->create('not_image.pdf', 200, 'application/pdf');

    $response = $this->put(route('rescues.update', $rescue), [
        'name' => 'Updated Name',
        'species' => 'Dog',
        'description' => 'A friendly dog',
        'sex' => 'male',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => 'true',
        'adoption_status' => 'available',
        'images' => [$invalidFile],
    ]);

    $response->assertSessionHasErrors('images.0');
  }

  public function test_updating_trashed_rescue_succeeds()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $rescue = Rescue::factory()->trashed()->create();

    $updatedData = [
      'name' => 'Updated Name',
      'species' => 'Updated Dog',
      'breed' => null,
      'distinctive_features' => null,
      'age' => null,
      'size' => null,
      'color' => null,
      'description' => 'A friendly dog',
      'sex' => 'male',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'images' => null,
      'profile_image' => null,
    ];

    $response = $this->put(route('rescues.update', $rescue), $updatedData);

    $response->assertRedirect(route('rescues.show', $rescue->id));
    $response->assertSessionHas('info', 'Rescue Profile for ' . $updatedData['name'] . ' has been updated!');

    // Ensure the record was updated in the database (even though it's soft deleted)
    $this->assertDatabaseHas('rescues', array_merge(
      ['id' => $rescue->id],
      collect($updatedData)
        ->except(['images', 'profile_image']) // exclude non-DB fields
      ->toArray()
    ));

    // Confirm it remains soft-deleted
    $this->assertSoftDeleted('rescues', ['id' => $rescue->id]);
  }

}
