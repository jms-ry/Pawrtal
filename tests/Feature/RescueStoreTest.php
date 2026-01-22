<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rescue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RescueStoreTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_create_rescue()
  {
    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => fake()->imageUrl(640,480,'animals',true),
      'images' => collect(range(1, fake()->numberBetween(1, 5)))
        ->map(fn() => fake()->imageUrl(640, 480, 'animals', true))
      ->toArray(),
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted'
    ];

    $response = $this->post(route('rescues.store'), $rescueData);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_regular_user_cannot_create_rescue()
  {
    $user = User::factory()->create();

    $this->actingAs($user);
    
    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => UploadedFile::fake()->image('profile.jpg'),
      'images' => [
        UploadedFile::fake()->image('image1.jpg'),
        UploadedFile::fake()->image('image2.jpg')
      ],
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);

    $response->assertForbidden();
  }

  public function test_admin_user_can_create_rescue()
  {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => UploadedFile::fake()->image('profile.jpg'),
      'images' => [
        UploadedFile::fake()->image('image1.jpg'),
        UploadedFile::fake()->image('image2.jpg')
      ],
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for '. $rescueData['name']. ' has been created!');
  }

  public function test_staff_user_can_create_rescue()
  {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => UploadedFile::fake()->image('profile.jpg'),
      'images' => [
        UploadedFile::fake()->image('image1.jpg'),
        UploadedFile::fake()->image('image2.jpg')
      ],
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for '. $rescueData['name']. ' has been created!');

  }

  public function test_rescue_record_is_created_in_database_with_correct_data()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $images = [
      UploadedFile::fake()->image('image1.jpg'),
      UploadedFile::fake()->image('image2.jpg'),
    ];

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'images' => $images,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for ' . $rescueData['name'] . ' has been created!');

    
    $this->assertDatabaseHas('rescues', [
      'name' => $rescueData['name'],
      'species' => $rescueData['species'],
      'breed' => $rescueData['breed'],
      'description' => $rescueData['description'],
      'sex' => $rescueData['sex'],
      'age' => $rescueData['age'],
      'size' => $rescueData['size'],
      'color' => $rescueData['color'],
      'distinctive_features' => $rescueData['distinctive_features'],
      'health_status' => $rescueData['health_status'],
      'vaccination_status' => $rescueData['vaccination_status'],
      'spayed_neutered' => $rescueData['spayed_neutered'],
      'adoption_status' => $rescueData['adoption_status'],
    ]);

    // Assert profile image was stored
    Storage::disk('public')->assertExists('images/rescues/profile_images/' . $profileImage->hashName());

    // Assert additional images were stored
    foreach ($images as $image) {
      Storage::disk('public')->assertExists('images/rescues/gallery_images/' . $image->hashName());
    }
  }

  public function test_profile_image_is_stored_in_correct_location()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for ' . $rescueData['name'] . ' has been created!');

    $this->assertDatabaseHas('rescues', [
      'name' => $rescueData['name'],
      'species' => $rescueData['species'],
      'breed' => $rescueData['breed'],
      'description' => $rescueData['description'],
      'sex' => $rescueData['sex'],
      'age' => $rescueData['age'],
      'size' => $rescueData['size'],
      'color' => $rescueData['color'],
      'distinctive_features' => $rescueData['distinctive_features'],
      'health_status' => $rescueData['health_status'],
      'vaccination_status' => $rescueData['vaccination_status'],
      'spayed_neutered' => $rescueData['spayed_neutered'],
      'adoption_status' => $rescueData['adoption_status'],
    ]);

    $rescue = Rescue::where('name', $rescueData['name'])->first();

    Storage::disk('public')->assertExists($rescue->profile_image);
  }

  public function test_multiple_gallery_images_are_stored_correctly()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $images = [
      UploadedFile::fake()->image('image1.jpg'),
      UploadedFile::fake()->image('image2.jpg'),
      UploadedFile::fake()->image('image3.jpg'),
    ];

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'images' => $images,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for ' . $rescueData['name'] . ' has been created!');

    $this->assertDatabaseHas('rescues', [
      'name' => $rescueData['name'],
      'species' => $rescueData['species'],
      'breed' => $rescueData['breed'],
      'description' => $rescueData['description'],
      'sex' => $rescueData['sex'],
      'age' => $rescueData['age'],
      'size' => $rescueData['size'],
      'color' => $rescueData['color'],
      'distinctive_features' => $rescueData['distinctive_features'],
      'health_status' => $rescueData['health_status'],
      'vaccination_status' => $rescueData['vaccination_status'],
      'spayed_neutered' => $rescueData['spayed_neutered'],
      'adoption_status' => $rescueData['adoption_status'],
    ]);

    // Assert additional images were stored
    $rescue = Rescue::where('name', $rescueData['name'])->first();

    $this->assertIsArray($rescue->images);
    $this->assertCount(count($images), $rescue->images);

    foreach ($rescue->images as $index => $path) {
      Storage::disk('public')->assertExists($path);
      $this->assertStringContainsString('images/rescues/gallery_images', $path);
    }
  }

  public function test_empty_images_array_is_stored_when_no_gallery_images_provided()
  {
    //  Fake storage (in case profile_image is uploaded)
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    // Prepare rescue data WITHOUT gallery images
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
        'species' => fake()->word(),
        'breed' => fake()->word(),
        'description' => fake()->sentence(),
        'sex' => fake()->randomElement(['male', 'female']),
        'age' => fake()->word(),
        'size' => fake()->randomElement(['small', 'medium', 'large']),
        'color' => fake()->colorName(),
        'distinctive_features' => fake()->sentence(),
        'profile_image' => $profileImage,
        'images' => [], // no gallery images
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => 'true',
        'adoption_status' => 'adopted',
    ];

    // Make the POST request
    $response = $this->post(route('rescues.store'), $rescueData);

    //  Assert redirect + session success
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for ' . $rescueData['name'] . ' has been created!');

    // Assert database record exists
    $this->assertDatabaseHas('rescues', [
      'name' => $rescueData['name'],
      'species' => $rescueData['species'],
      'breed' => $rescueData['breed'],
      'description' => $rescueData['description'],
      'sex' => $rescueData['sex'],
      'age' => $rescueData['age'],
      'size' => $rescueData['size'],
      'color' => $rescueData['color'],
      'distinctive_features' => $rescueData['distinctive_features'],
      'health_status' => $rescueData['health_status'],
      'vaccination_status' => $rescueData['vaccination_status'],
      'spayed_neutered' => $rescueData['spayed_neutered'],
      'adoption_status' => $rescueData['adoption_status'],
    ]);

    // Assert profile image was stored
    Storage::disk('public')->assertExists('images/rescues/profile_images/' . $profileImage->hashName());

    //  Assert images field is stored as empty array
    $rescue = Rescue::where('name', $rescueData['name'])->first();
    $this->assertIsArray($rescue->images);
    $this->assertEmpty($rescue->images);
  }


  public function test_images_array_is_properly_converted_to_paths_in_database()
  {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $galleryImages = [
      UploadedFile::fake()->image('image1.jpg'),
      UploadedFile::fake()->image('image2.jpg'),
      UploadedFile::fake()->image('image3.jpg'),
    ];

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'images' => $galleryImages,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for ' . $rescueData['name'] . ' has been created!');

    $rescue = Rescue::where('name', $rescueData['name'])->first();

    Storage::disk('public')->assertExists($rescue->profile_image);

    $this->assertIsArray($rescue->images);
    $this->assertCount(count($galleryImages), $rescue->images);

    foreach ($rescue->images as $index => $path) {
      Storage::disk('public')->assertExists($path);
      $this->assertStringContainsString('images/rescues/gallery_images', $path);
    }
  }

  public function test_creating_rescue_without_name_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('name');
    $this->assertDatabaseCount('rescues', 0);
  }
  public function test_creating_rescue_without_species_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('species');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_without_descriptions_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      //'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('description');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_without_sex_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      //'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('sex');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_without_health_status_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);
    
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      // 'health_status' => 'healthy', // MISSING
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('health_status');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_without_vaccination_status_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);
    
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      // 'vaccination_status' => 'vaccinated', // MISSING
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('vaccination_status');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_without_spayed_neutered_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);
    
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      // 'spayed_neutered' => 'true', // MISSING
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('spayed_neutered');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_without_profile_image_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      //'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('profile_image');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_invalid_sex_value_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => 'ramdom sex',
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('sex');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_invalid_health_status_value_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'random status',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('health_status');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_invalid_vaccination_status_value_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'random status',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('vaccination_status');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_invalid_adoption_status_value_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'random status',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('adoption_status');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_invalid_profile_image_type_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.gif');

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'random status',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('profile_image');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_profile_image_exceeding_max_size_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg')->size(3000); // 3MB

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'random status',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('profile_image');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_gallery_images_exceeding_max_limit_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $images = [
      UploadedFile::fake()->image('image1.jpg'),
      UploadedFile::fake()->image('image2.jpg'),
      UploadedFile::fake()->image('image3.jpg'),
      UploadedFile::fake()->image('image4.jpg'),
      UploadedFile::fake()->image('image4.jpg'),
      UploadedFile::fake()->image('image4.jpg'),
    ]; // 6 images, exceeding limit of 5

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'images' => $images,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('images');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_invalid_gallery_image_type_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $images = [
      UploadedFile::fake()->image('image1.jpg'),
      UploadedFile::fake()->image('image2.gif'), // invalid type
    ];

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'images' => $images,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('images.1');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_gallery_image_exceed_file_size_limit_fails_validation()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $images = [
      UploadedFile::fake()->image('image1.jpg'),
      UploadedFile::fake()->image('image2.jpg')->size(3000), // 3MB
    ];

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => $profileImage,
      'images' => $images,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    $response = $this->post(route('rescues.store'), $rescueData);
    $response->assertSessionHasErrors('images.1');
    $this->assertDatabaseCount('rescues', 0);
  }

  public function test_creating_rescue_with_nullable_fields_empty_succeeds()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $images = null;

    $rescueData = [
      'name' => fake()->firstName(),
      'species' => fake()->word(),
      'breed' => null,
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male','female']),
      'age' => null,
      'size' => null,
      'color' => null,
      'distinctive_features' => null,
      'profile_image' => $profileImage,
      'images' => $images,
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted',
    ];

    // Make the POST request
    $response = $this->post(route('rescues.store'), $rescueData);

    //  Assert redirect + session success
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Rescue profile for ' . $rescueData['name'] . ' has been created!');

    // Assert database record exists
    $this->assertDatabaseHas('rescues', [
      'name' => $rescueData['name'],
      'species' => $rescueData['species'],
      'breed' => $rescueData['breed'],
      'description' => $rescueData['description'],
      'sex' => $rescueData['sex'],
      'age' => $rescueData['age'],
      'size' => $rescueData['size'],
      'color' => $rescueData['color'],
      'distinctive_features' => $rescueData['distinctive_features'],
      'health_status' => $rescueData['health_status'],
      'vaccination_status' => $rescueData['vaccination_status'],
      'spayed_neutered' => $rescueData['spayed_neutered'],
      'adoption_status' => $rescueData['adoption_status'],
    ]);

    // Assert profile image was stored
    Storage::disk('public')->assertExists('images/rescues/profile_images/' . $profileImage->hashName());
  }
}
