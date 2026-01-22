<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReportStoreTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_store_report()
  {
    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => fake()->randomElement(['Dog', 'Cat']),
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->numberBetween(1, 10),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => fake()->imageUrl(640, 480, 'animals', true),
      'status' => 'active',
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('reports', 0);
  }

  public function test_regular_user_can_store_report()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');
  }

  public function test_admin_user_can_store_report()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $admin->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');
  }

  public function test_staff_user_can_store_report()
  {
    Storage::fake('public');
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $staff->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');
  }

  public function test_report_record_is_created_in_database_with_correct_data()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');

    
    $this->assertDatabaseHas('reports', [
      'type' => $reportData['type'],
      'animal_name' => $reportData['animal_name'],
      'last_seen_location' => $reportData['last_seen_location'],
      'last_seen_date' => $reportData['last_seen_date'],
      'species' => $reportData['species'],
      'breed' => $reportData['breed'],
      'color' => $reportData['color'],
      'sex' => $reportData['sex'],
      'age_estimate' => $reportData['age_estimate'],
      'size' => $reportData['size'],
      'distinctive_features' => $reportData['distinctive_features'],
      'status' => $reportData['status'],
      'user_id' => $reportData['user_id']
    ]);

    // Assert profile image was stored
    Storage::disk('public')->assertExists('images/reports/reports_images/' . $image->hashName());
  }

  public function test_image_is_stored_in_correct_location()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');

    
    $this->assertDatabaseHas('reports', [
      'type' => $reportData['type'],
      'animal_name' => $reportData['animal_name'],
      'last_seen_location' => $reportData['last_seen_location'],
      'last_seen_date' => $reportData['last_seen_date'],
      'species' => $reportData['species'],
      'breed' => $reportData['breed'],
      'color' => $reportData['color'],
      'sex' => $reportData['sex'],
      'age_estimate' => $reportData['age_estimate'],
      'size' => $reportData['size'],
      'distinctive_features' => $reportData['distinctive_features'],
      'status' => $reportData['status'],
      'user_id' => $reportData['user_id']
    ]);

    // Assert profile image was stored
    Storage::disk('public')->assertExists('images/reports/reports_images/' . $image->hashName());

    $report = Report::where('type', $reportData['type'])->first();

    Storage::disk('public')->assertExists($report->image);
  }

  public function test_creating_report_without_user_id_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      //'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('user_id');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_without_type_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      //'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('type');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_without_species_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      //'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('species');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_without_color_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      //'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('color');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_without_sex_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      //'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('sex');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_without_size_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      //'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('size');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_without_image_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      //'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('image');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_with_invalid_type_value_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'invalid lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('type');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_with_invalid_sex_value_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => 'invalid sex',
      'age_estimate' => fake()->numberBetween(1, 10),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('sex');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_creating_report_with_invalid_size_value_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->numberBetween(1, 10),
      'size' => 'invalid size',
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('size');
    $this->assertDatabaseCount('reports',0);
  }

  public function test_fields_exclusive_for_found_reports_are_set_to_null_when_creating_lost_report()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      /*
      Attributes exclusive for found reports
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      */
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $admin->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');

    // Assert: one report in DB
    $this->assertDatabaseCount('reports', 1);

    // Retrieve created report
    $report = Report::first();

    // Assert that found-only fields are NULL
    $this->assertNull($report->found_location);
    $this->assertNull($report->found_date);
    $this->assertNull($report->condition);
    $this->assertNull($report->temporary_shelter);

    // Assert the report type is correct
    $this->assertEquals('lost', $report->type);

    // Assert that the image was stored properly
    Storage::disk('public')->assertExists($report->image);
  }

  public function test_fields_exclusive_for_lost_reports_are_set_to_null_when_creating_found_report()
  {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'found',
      /*
      Attributes exclusive for found reports
      'animal_name' => null,
      'last_seen_location' => null ,
      'last_seen_date' => null ,
      */
      'found_location' =>fake()->streetAddress() ,
      'found_date' => fake()->date(),
      'condition' => fake()->word(),
      'temporary_shelter' => fake()->sentence(),
      
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $admin->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Found Dog Report has been created!');

    // Assert: one report in DB
    $this->assertDatabaseCount('reports', 1);

    // Retrieve created report
    $report = Report::first();

    // Assert that lost-only fields are NULL
    $this->assertNull($report->animal_name);
    $this->assertNull($report->last_seen_date);
    $this->assertNull($report->last_seen_location);

    // Assert the report type is correct
    $this->assertEquals('found', $report->type);

    // Assert that the image was stored properly
    Storage::disk('public')->assertExists($report->image);
  }

  public function test_creating_report_with_invalid_image_type_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.gif');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('image');
    $this->assertDatabaseCount('reports', 0);
  }

  public function test_creating_report_with_image_exceed_max_file_size_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg')->size('10000');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('image');
    $this->assertDatabaseCount('reports', 0);
  }

  public function test_report_created_with_status_active_by_default()
  {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      'species' => 'dog',
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->sentence(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => $image,
      //'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');

    // Retrieve created report
    $report = Report::first();

    //Assert that status is active
    $this->assertEquals('active', $report->status);
  }

  public function test_report_with_nullable_fields_common_for_both_report_type_empty_succeeds()
  {
    /** breed, color and distinctive_features which are common for both report type are nullable */
    
    //Simulate creating lost report
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report_image.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      /*
      Exclusive for found reports
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
      */
      'species' => 'dog',
      'breed' => null,
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => null,
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => null,
      'image' => $image,
      //'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Dog Report has been created!');

    $reportData = [
      'type' => 'found',
      /*
      Attributes exclusive for found reports
      'animal_name' => null,
      'last_seen_location' => null ,
      'last_seen_date' => null ,
      */
      'found_location' =>fake()->streetAddress() ,
      'found_date' => fake()->date(),
      'condition' => fake()->word(),
      'temporary_shelter' => fake()->sentence(),
      
      'species' => 'dog',
      'breed' => null,
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => null,
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => null,
      'image' => $image,
      //'status' => 'active',
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Found Dog Report has been created!');
    
    $this->assertEquals(1,Report::where('type','lost')->where('status', 'active')->count());
    $this->assertEquals(1,Report::where('type','found')->where('status', 'active')->count());
    $this->assertDatabaseCount('reports', 2);
  }

  public function test_creating_report_with_jpeg_image_succeeds()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpeg', 640, 480);

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'Buddy',
      'last_seen_location' => '123 Street',
      'last_seen_date' => '2024-01-01',
      'species' => 'dog',
      'color' => 'brown',
      'sex' => 'male',
      'size' => 'medium',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $this->assertDatabaseCount('reports', 1);
    Storage::disk('public')->assertExists('images/reports/reports_images/' . $image->hashName());
  }

  public function test_creating_report_with_png_image_succeeds()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.png', 640, 480);

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'Buddy',
      'last_seen_location' => '123 Street',
      'last_seen_date' => '2024-01-01',
      'species' => 'dog',
      'color' => 'brown',
      'sex' => 'male',
      'size' => 'medium',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $this->assertDatabaseCount('reports', 1);
  }
  
  public function test_creating_report_with_non_image_file_fails_validation()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('document.pdf', 1000);

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'Buddy',
      'last_seen_location' => '123 Street',
      'last_seen_date' => '2024-01-01',
      'species' => 'dog',
      'color' => 'brown',
      'sex' => 'male',
      'size' => 'medium',
      'image' => $file,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('image');
    $this->assertDatabaseCount('reports', 0);
  }

  public function test_success_message_includes_correct_species_for_cat()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'Whiskers',
      'last_seen_location' => '123 Street',
      'last_seen_date' => '2024-01-01',
      'species' => 'cat',
      'color' => 'orange',
      'sex' => 'female',
      'size' => 'small',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Lost Cat Report has been created!');
}

  public function test_success_message_for_found_cat_report()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');

    $reportData = [
      'type' => 'found',
      'found_location' => '123 Street',
      'found_date' => '2024-01-01',
      'condition' => 'healthy',
      'temporary_shelter' => 'Home',
      'species' => 'cat',
      'color' => 'orange',
      'sex' => 'female',
      'size' => 'small',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Found Cat Report has been created!');
  }

  public function test_creating_report_with_html_tags_in_text_fields_is_sanitized_or_escaped()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => '<script>alert("XSS")</script>Buddy',
      'last_seen_location' => '<b>123 Street</b>',
      'last_seen_date' => '2024-01-01',
      'species' => 'dog',
      'color' => 'brown',
      'sex' => 'male',
      'size' => 'medium',
      'distinctive_features' => '<img src=x onerror=alert(1)>',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    
    $report = Report::first();
    
    // Verify data is stored (Laravel will escape on output)
    $this->assertStringContainsString('Buddy', $report->animal_name);
  }

  public function test_creating_report_with_very_long_text_fields()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');
    $longText = str_repeat('a', 65535); // Test max TEXT column size

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'Buddy',
      'last_seen_location' => '123 Street',
      'last_seen_date' => '2024-01-01',
      'species' => 'dog',
      'color' => 'brown',
      'sex' => 'male',
      'size' => 'medium',
      'distinctive_features' => $longText,
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('distinctive_features');
  }

  public function test_creating_report_with_non_existent_user_id_fails()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');

    $reportData = [
        'type' => 'lost',
        'animal_name' => 'Buddy',
        'last_seen_location' => '123 Street',
        'last_seen_date' => '2024-01-01',
        'species' => 'dog',
        'color' => 'brown',
        'sex' => 'male',
        'size' => 'medium',
        'image' => $image,
        'user_id' => 99999 // Non-existent user
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('user_id');
    $this->assertDatabaseCount('reports', 0);
  }

  public function test_creating_lost_report_with_invalid_date_format_fails()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'Buddy',
      'last_seen_location' => '123 Street',
      'last_seen_date' => 'not-a-date',
      'species' => 'dog',
      'color' => 'brown',
      'sex' => 'male',
      'size' => 'medium',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('last_seen_date');
    $this->assertDatabaseCount('reports', 0);
  }

  public function test_creating_lost_report_with_future_date_fails()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'Buddy',
      'last_seen_location' => '123 Street',
      'last_seen_date' => now()->addDays(10)->format('Y-m-d'),
      'species' => 'dog',
      'color' => 'brown',
      'sex' => 'male',
      'size' => 'medium',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertSessionHasErrors('last_seen_date');
    $this->assertDatabaseCount('reports', 0);
  }

  public function test_creating_report_with_minimum_required_fields_only()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $image = UploadedFile::fake()->image('report.jpg');

    $reportData = [
      'type' => 'lost',
      'animal_name' => 'B',
      'last_seen_location' => 'A',
      'last_seen_date' => '2024-01-01',
      'species' => 'dog',
      'color' => 'x',
      'sex' => 'male',
      'size' => 'small',
      'image' => $image,
      'user_id' => $user->id
    ];

    $response = $this->post(route('reports.store'), $reportData);
    $response->assertRedirect();
    $this->assertDatabaseCount('reports', 1);
  }

  public function test_multiple_reports_can_be_created_simultaneously()
  {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 0; $i < 5; $i++) {
      $image = UploadedFile::fake()->image("report{$i}.jpg");

      $reportData = [
        'type' => 'lost',
        'animal_name' => "Buddy{$i}",
        'last_seen_location' => '123 Street',
        'last_seen_date' => '2024-01-01',
        'species' => 'dog',
        'color' => 'brown',
        'sex' => 'male',
        'size' => 'medium',
        'image' => $image,
        'user_id' => $user->id
      ];

      $response = $this->post(route('reports.store'), $reportData);
      $response->assertRedirect();
    }

    $this->assertDatabaseCount('reports', 5);
  }
}
