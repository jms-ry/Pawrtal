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

class RescueShowPageTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_user_can_view_non_trashed_rescue()
  {
    $rescue = Rescue::factory()->create();

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertStatus(200);
  }

  public function test_regular_user_can_view_non_trashed_rescue()
  {
    $rescue = Rescue::factory()->create();

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertStatus(200);
  }

  public function test_admin_user_can_view_non_trashed_rescue()
  {
    $rescue = Rescue::factory()->create();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertStatus(200);
  }

  public function test_staff_user_can_view_non_trashed_rescue()
  {
    $rescue = Rescue::factory()->create();

    $staff = User::factory()->staff()->create();

    $this->actingAs($staff);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertStatus(200);
  }

  public function test_guest_user_cannot_view_trashed_rescue()
  {
    $rescue = Rescue::factory()->trashed()->create();

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'You are not authorized to view this rescue profile.');
  }

  public function test_regular_user_cannot_view_trashed_rescue()
  {
    $rescue = Rescue::factory()->trashed()->create();
    $user = User::factory()->create();

    $this->actingAs($user);
    $response = $this->get(route('rescues.show', $rescue));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'You are not authorized to view this rescue profile.');
  }

  public function test_admin_user_can_view_trashed_rescue()
  {
    $rescue = Rescue::factory()->trashed()->create();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertStatus(200);
  }

  public function test_staff_user_can_view_trashed_rescue()
  {
    $rescue = Rescue::factory()->trashed()->create();

    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertStatus(200);
  }

  public function test_viewing_non_existent_rescue_returns_404()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
        
    $response = $this->get(route('rescues.show', 99999));
        
    $response->assertNotFound();
  }

  public function test_user_data_includes_canAdopt_result_for_authencticated_user()
  {
    $user = User::factory()->create();
    $rescue = Rescue::factory()->create();

    $address = Address::factory()->create([
      'user_id' => $user->id,
    ]);

    $household = Household::factory()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->has('user')
        ->where('user.id', $user->id)
      ->where('user.canAdopt', true)
    );
  }

  public function test_user_data_includes_address_relationship_for_authenticated_user()
  {
    $user = User::factory()->create();
    $rescue = Rescue::factory()->create();

    $address = Address::factory()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->where('user.id', $user->id)
      ->has('user.address')
      ->where('user.address.id', $address->id)
    );
  }

  public function test_user_data_includes_household_relationship_for_authenticated_user()
  {
    $user = User::factory()->create();
    $rescue = Rescue::factory()->create();

    $household = Household::factory()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->where('user.id', $user->id)
      ->has('user.household')
      ->where('user.household.id', $household->id)
    );
  }

  public function test_user_data_is_null_for_guest_user()
  {
    $rescue = Rescue::factory()->create();

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->where('user', null)
    );
  }

  public function test_response_inlcude_rescue_with_adoption_application_count()
  {
    $rescue = Rescue::factory()->create();

    AdoptionApplication::factory()->count(3)->create([
      'rescue_id' => $rescue->id,
    ]);

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->has('rescue', fn ($page) =>
          $page->where('id', $rescue->id)
          ->where('adoption_applications_count', 3)
          ->etc()
        )
    );
  }

  public function test_random_images_are_selected_from_rescues_images_up_to_3_images()
  {
    $rescue = Rescue::factory()->create([
      'images' => [
        'image1.jpg',
        'image2.jpg',
        'image3.jpg',
        'image4.jpg',
        'image5.jpg',
      ],
    ]);

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->has('randomImages', 3)
      ->etc()
    );
  }

  public function test_random_images_are_selected_from_rescues_images_when_less_than_3_images_exist()
  {
    $rescue = Rescue::factory()->create([
      'images' => [
        'image1.jpg',
        'image2.jpg',
      ],
    ]);

    $user = User::factory()->create();

    $this->actingAs($user);
    $response = $this->get(route('rescues.show', $rescue));
    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->has('randomImages', 2)
      ->etc()
    );
  }

  public function test_navigation_context_data_is_included_in_response()
  {
    $rescue = Rescue::factory()->create();

    $user = User::factory()->create();

    $this->actingAs($user);
    
    $response = $this->from('/rescues')->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')
        ->has('previousUrl')
        ->where('previousUrl', url('/rescues'))
        ->has('urlText')
      ->where('urlText', 'to Rescues')
    );
  }

  public function test_notEmpty_flag_is_true_when_rescue_has_images()
  {
    $rescue = Rescue::factory()->create([
      'images' => [
        'image1.jpg',
      ],
    ]);

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('rescues.show', $rescue));

    $response->assertInertia(fn ($page) =>
      $page->component('Rescues/Show')->where('notEmpty', true)
    );
  }

}
