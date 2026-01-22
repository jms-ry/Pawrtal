<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\AdoptionApplication;
use App\Models\Household;
use App\Models\Rescue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class AdoptionApplicationStoreTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_cannot_store_adoption_application()
  {
    $applicationData = [
      'reason_for_adoption' => 'I want to adopt.'
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('login'));
  }

  public function test_admin_user_cannot_store_adoption_application()
  {
    //Has address and household info but is an admin or staff

    $admin = User::factory()->admin()->create();

    Address::factory()->create(['user_id' => $admin->id]);
    Household::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin);

    $rescue = Rescue::factory()->available()->create();

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $admin->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => UploadedFile::fake()->image('validId.jpg'),
      'supporting_documents' => [
        UploadedFile::fake()->create('image1.jpg'),
        UploadedFile::fake()->create('document.pdf')
      ],
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertForbidden();
  }

  public function test_staff_user_cannot_store_adoption_application()
  {
    //Has address and household info but is an admin or staff

    $staff = User::factory()->staff()->create();

    Address::factory()->create(['user_id' => $staff->id]);
    Household::factory()->create(['user_id' => $staff->id]);

    $this->actingAs($staff);

    $rescue = Rescue::factory()->available()->create();

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $staff->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => UploadedFile::fake()->create('validId.jpg'),
      'supporting_documents' => [
        UploadedFile::fake()->create('image1.jpg'),
        UploadedFile::fake()->create('document.pdf')
      ],
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertForbidden();
  }

  public function test_regular_user_who_cannot_adopt_cannot_store_adoption_application()
  {
    //Lacks address and household info

    $user = User::factory()->create();

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => UploadedFile::fake()->create('validId.jpg'),
      'supporting_documents' => [
        UploadedFile::fake()->create('image1.jpg'),
        UploadedFile::fake()->create('document.pdf')
      ],
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertForbidden();
  }

  public function test_regular_user_who_can_adopt_can_store_adoption_application()
  {
    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => UploadedFile::fake()->create('validId.jpg'),
      'supporting_documents' => [
        UploadedFile::fake()->create('image1.jpg'),
        UploadedFile::fake()->create('document.pdf')
      ],
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');
  }

  public function test_regular_user_who_can_adopt_cannot_submit_adoption_application_for_unavailable_rescue()
  {
    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->unavailable()->create();

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => UploadedFile::fake()->create('validId.jpg'),
      'supporting_documents' => [
        UploadedFile::fake()->create('image1.jpg'),
        UploadedFile::fake()->create('document.pdf')
      ],
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('rescue_id');
  }

  public function test_regular_user_who_can_adopt_cannot_submit_adoption_application_for_adopted_rescue()
  {
    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->create();

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => UploadedFile::fake()->create('validId.jpg'),
      'supporting_documents' => [
        UploadedFile::fake()->create('image1.jpg'),
        UploadedFile::fake()->create('document.pdf')
      ],
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('rescue_id');
  }

  public function test_adoption_application_record_is_created_in_database_with_correct_data()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');

    
    $this->assertDatabaseHas('adoption_applications', [
      'user_id' => $applicationData['user_id'],
      'rescue_id' => $applicationData['rescue_id'],
      'preferred_inspection_start_date' => $applicationData['preferred_inspection_start_date'],
      'preferred_inspection_end_date' => $applicationData['preferred_inspection_end_date'],
      'reason_for_adoption' => $applicationData['reason_for_adoption'],
    ]);

    // Assert valid id was stored
    Storage::disk('public')->assertExists('images/adoption_applications/valid_ids/' . $validId->hashName());

    // Assert supporting documents were stored
    foreach ($supportingDocuments as $document) {
      Storage::disk('public')->assertExists('images/adoption_applications/supporting_documents/' . $document->hashName());
    }
  }

  public function test_valid_id_is_stored_in_correct_location()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');

    $this->assertDatabaseHas('adoption_applications', [
      'user_id' => $applicationData['user_id'],
      'rescue_id' => $applicationData['rescue_id'],
      'preferred_inspection_start_date' => $applicationData['preferred_inspection_start_date'],
      'preferred_inspection_end_date' => $applicationData['preferred_inspection_end_date'],
      'reason_for_adoption' => $applicationData['reason_for_adoption'],
    ]);

    $application = AdoptionApplication::where('rescue_id', $applicationData['rescue_id'])->first();

    Storage::disk('public')->assertExists($application->valid_id);
  }

  public function test_multiple_supporting_documents_are_stored_correctly()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');

    
    $this->assertDatabaseHas('adoption_applications', [
      'user_id' => $applicationData['user_id'],
      'rescue_id' => $applicationData['rescue_id'],
      'preferred_inspection_start_date' => $applicationData['preferred_inspection_start_date'],
      'preferred_inspection_end_date' => $applicationData['preferred_inspection_end_date'],
      'reason_for_adoption' => $applicationData['reason_for_adoption'],
    ]);

    // Assert additional images were stored
    $application = AdoptionApplication::where('rescue_id', $applicationData['rescue_id'])->first();

    $this->assertIsArray($application->supporting_documents);
    $this->assertCount(count($supportingDocuments), $application->supporting_documents);

    foreach ($application->supporting_documents as $index => $path) {
      Storage::disk('public')->assertExists($path);
      $this->assertStringContainsString('images/adoption_applications/supporting_documents', $path);
    }
  }

  public function test_supporting_documents_array_is_properly_converted_to_paths_in_database()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');

    $application = AdoptionApplication::where('rescue_id', $applicationData['rescue_id'])->first();;

    Storage::disk('public')->assertExists($application->valid_id);

    $this->assertIsArray($application->supporting_documents);
    $this->assertCount(count($supportingDocuments), $application->supporting_documents);

    foreach ($application->supporting_documents as $index => $path) {
      Storage::disk('public')->assertExists($path);
      $this->assertStringContainsString('images/adoption_applications/supporting_documents', $path);
    }
  }

  public function test_missing_user_id_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      //'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('user_id');
  }

  public function test_missing_rescue_id_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      //'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('rescue_id');
  }

  public function test_missing_preferred_inspection_start_date_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      //'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('preferred_inspection_start_date');
  }

  public function test_missing_preferred_inspection_end_date_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      //'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('preferred_inspection_end_date');
  }

  public function test_missing_valid_id_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      //'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('valid_id');
  }

  public function test_missing_supporting_documents_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      //'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('supporting_documents');
  }

  public function test_missing_reason_for_adoption_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      //'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('reason_for_adoption');
  }

  public function test_invalid_valid_id_format_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.docx');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('valid_id');
  }

  public function test_valid_id_exceeds_max_size_limit_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.pdf', 10000);
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('valid_id');
  }
  
  public function test_one_of_multiple_supporting_document_has_invalid_file_format_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.docx'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('supporting_documents.0');
  }

  public function test_one_of_multiple_supporting_document_exceeds_ma_file_size_limit_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg', 10000),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('supporting_documents.0');
  }

  public function test_preferred_inspection_start_date_must_be_a_date()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => 'not a date',
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('preferred_inspection_start_date');
  }

  public function test_preferred_inspection_end_date_must_be_a_date()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(11)->format('Y-m-d'),
      'preferred_inspection_end_date' => 'not a date',
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('preferred_inspection_end_date');
  }

  public function test_preferred_inspection_start_date_must_not_be_from_the_past()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->subDays(11)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('preferred_inspection_start_date');
  }

  public function test_preferred_inspection_end_date_must_not_be_from_the_past()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(11)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->subDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('preferred_inspection_end_date');
  }

  public function test_preferred_inspection_end_date_must_not_before_the_preferred_inspection_start_date()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $preferredStartDate = now()->addDays(11);

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => $preferredStartDate->format('Y-m-d'),
      'preferred_inspection_end_date' => $preferredStartDate->subDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('preferred_inspection_end_date');
  }

  public function test_preferred_inspection_end_date_must_be_after_or_equal_the_preferred_inspection_start_date()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg', 1000),
      UploadedFile::fake()->create('document.pdf')
    ];

    $preferredStartDate = now()->addDays(11);

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => $preferredStartDate->format('Y-m-d'),
      'preferred_inspection_end_date' => $preferredStartDate->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');

    
    $this->assertDatabaseHas('adoption_applications', [
      'user_id' => $applicationData['user_id'],
      'rescue_id' => $applicationData['rescue_id'],
      'preferred_inspection_start_date' => $applicationData['preferred_inspection_start_date'],
      'preferred_inspection_end_date' => $applicationData['preferred_inspection_end_date'],
      'reason_for_adoption' => $applicationData['reason_for_adoption'],
    ]);
  }

  public function test_creating_adoption_application_with_nullable_fields_empty_succeeds()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg', 1000),
      UploadedFile::fake()->create('document.pdf')
    ];

    $preferredStartDate = now()->addDays(11);

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => $preferredStartDate->format('Y-m-d'),
      'preferred_inspection_end_date' => $preferredStartDate->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
      'reviewed_by' => null,
      'review_date' => null,
      'review_notes' => null
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');

    
    $this->assertDatabaseHas('adoption_applications', [
      'user_id' => $applicationData['user_id'],
      'rescue_id' => $applicationData['rescue_id'],
      'preferred_inspection_start_date' => $applicationData['preferred_inspection_start_date'],
      'preferred_inspection_end_date' => $applicationData['preferred_inspection_end_date'],
      'reason_for_adoption' => $applicationData['reason_for_adoption'],
      'reviewed_by' => $applicationData['reviewed_by'],
      'review_date' => $applicationData['review_date'],
      'review_notes' => $applicationData['review_notes']
    ]);
  }

  public function test_created_adoption_application_has_pending_status_by_default()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg', 1000),
      UploadedFile::fake()->create('document.pdf')
    ];

    $preferredStartDate = now()->addDays(11);

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => $preferredStartDate->format('Y-m-d'),
      'preferred_inspection_end_date' => $preferredStartDate->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertRedirect(route('users.myAdoptionApplications'));
    $response->assertSessionHas('success', 'Adoption application for '. $rescue->name. ' was submitted!');

    $application = AdoptionApplication::first();

    $this->assertEquals('pending', $application->status);
  }

  public function test_creating_adoption_application_with_nonexistent_user_id_fails()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => 99999,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => fake()->sentence(),
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('user_id');
  }

  public function test_reason_for_adoption_exceed_limit_fails_validation()
  {
    Storage::fake('public');

    $user = User::factory()->create();

    Address::factory()->create(['user_id' => $user->id]);
    Household::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $rescue = Rescue::factory()->available()->create();

    $validId = UploadedFile::fake()->create('validId.jpg');
    $supportingDocuments = [
      UploadedFile::fake()->create('image1.jpg'),
      UploadedFile::fake()->create('document.pdf')
    ];
    $longText = str_repeat('a', 65535);

    $applicationData = [
      'rescue_id' => $rescue->id,
      'user_id' => $user->id,
      'preferred_inspection_start_date' => now()->addDays(10)->format('Y-m-d'),
      'preferred_inspection_end_date' => now()->addDays(11)->format('Y-m-d'),
      'valid_id' => $validId,
      'supporting_documents' => $supportingDocuments,
      'reason_for_adoption' => $longText,
    ];

    $response = $this->post(route('adoption-applications.store'), $applicationData);
    $response->assertSessionHasErrors('reason_for_adoption');
  }
}
