<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Rescue;

class WelcomeControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_can_view_welcome_page()
  {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('app');
  }

  public function test_regular_user_can_view_welcome_page()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('app');
  }

  public function test_admin_user_can_view_welcome_page()
  {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('app');
  }

  public function test_staff_user_can_view_welcome_page()
  {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('app');
  }

  public function test_sheltered_count_includes_all_rescues_regardless_of_status()
  {
    // Create rescues with different statuses
    Rescue::factory()->count(5)->create(['adoption_status' => 'available']);
    Rescue::factory()->count(3)->create(['adoption_status' => 'adopted']);
    Rescue::factory()->count(2)->create(['adoption_status' => 'unavailable']);
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->where('shelteredCount', 10)
    );
  }

  public function test_sheltered_count_excludes_soft_deleted_rescues()
  {
    Rescue::factory()->count(5)->create();
    Rescue::factory()->trashed()->count(3)->create();
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->where('shelteredCount', 5)
    );
  }

  public function test_spayed_neutered_count_only_includes_true_values_of_non_trashed_rescue()
  {
    Rescue::factory()->count(7)->create(['spayed_neutered' => true]);
    Rescue::factory()->count(3)->create(['spayed_neutered' => false]);
    Rescue::factory()->count(2)->trashed()->create(['spayed_neutered' => true]);
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->where('spayedNeuteredCount', 7)
    );
  }

  public function test_vaccinated_count_only_includes_vaccinated_status_of_non_trashed_rescue()
  {
    Rescue::factory()->count(6)->create(['vaccination_status' => 'vaccinated']);
    Rescue::factory()->count(2)->create(['vaccination_status' => 'not_vaccinated']);
    Rescue::factory()->count(2)->create(['vaccination_status' => 'partially_vaccinated']);
    Rescue::factory()->count(6)->trashed()->create(['vaccination_status' => 'vaccinated']);
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->where('vaccinatedCount', 6)
    );
  }

  public function test_adopted_count_only_includes_adopted_status_of_non_trashed_rescue()
  {
    Rescue::factory()->count(4)->create(['adoption_status' => 'adopted']);
    Rescue::factory()->count(3)->create(['adoption_status' => 'available']);
    Rescue::factory()->count(2)->create(['adoption_status' => 'unavailable']);
    Rescue::factory()->count(4)->trashed()->create(['adoption_status' => 'adopted']);
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->where('adoptedCount', 4)
    );
  }

  public function test_rescues_are_ordered_by_name_in_descending_order()
  {
    Rescue::factory()->create(['name' => 'Alice']);
    Rescue::factory()->create(['name' => 'Bella']);
    Rescue::factory()->create(['name' => 'Charlie']);
    Rescue::factory()->create(['name' => 'Duke']);
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')
        ->where('rescues.0.name', 'Duke')
        ->where('rescues.1.name', 'Charlie')
        ->where('rescues.2.name', 'Bella')
      ->where('rescues.3.name', 'Alice')
    );
  }

  public function test_only_four_rescues_are_returned()
  {
    Rescue::factory()->count(10)->create();
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->has('rescues', 4)
    );
  }

  public function test_soft_deleted_rescues_are_not_included_in_rescues_list()
  {
    Rescue::factory()->count(5)->create();
    Rescue::factory()->trashed()->count(3)->create();
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->has('rescues', 4)
    );
  }

  public function test_all_counts_are_zero_when_no_rescues_exist()
  {
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')
        ->where('shelteredCount', 0)
        ->where('spayedNeuteredCount', 0)
        ->where('vaccinatedCount', 0)
        ->where('adoptedCount', 0)
      ->has('rescues', 0)
    );
  }

  public function test_returns_available_rescues_when_less_than_four_exist()
  {
    Rescue::factory()->count(2)->create();
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')->has('rescues', 2)
    );
  }

  public function test_all_statistics_are_calculated_correctly_with_mixed_data()
  {
    // Create diverse rescue data
    Rescue::factory()->create([
      'spayed_neutered' => true,
      'vaccination_status' => 'vaccinated',
      'adoption_status' => 'adopted',
    ]);
    
    Rescue::factory()->count(2)->create([
      'spayed_neutered' => false,
      'vaccination_status' => 'not_vaccinated',
      'adoption_status' => 'available',
    ]);
    
    Rescue::factory()->create([
      'spayed_neutered' => true,
      'vaccination_status' => 'vaccinated',
      'adoption_status' => 'available',
    ]);
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')
        ->where('shelteredCount', 4)
        ->where('spayedNeuteredCount', 2)
        ->where('vaccinatedCount', 2)
        ->where('adoptedCount', 1)
      ->has('rescues', 4)
    );
  }

  public function test_rescues_include_necessary_attributes()
  {
    $rescue = Rescue::factory()->create();
    
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')
        ->has('rescues.0.id')
        ->has('rescues.0.name')
        ->has('rescues.0.species')
      ->etc()
    );
  }

  public function test_correct_inertia_component_is_rendered()
  {
    $response = $this->get('/');
    
    $response->assertInertia(fn ($page) =>
      $page->component('Welcome/Index')
    );
  }
}
