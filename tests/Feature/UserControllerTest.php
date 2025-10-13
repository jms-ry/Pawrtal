<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Address;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
  use RefreshDatabase;
  /**
    * A basic feature test example.
  */

  // public function test_example(): void
  // {
  //   $response = $this->get('/');

  //   $response->assertStatus(200);
  // }

  public function test_it_displays_the_user_show_page_with_correct_data()
  {
    // Arrange: create a user with related models
    $user = User::factory()
      ->has(Address::factory())
      ->has(Household::factory())
    ->admin()->create();

    $this->actingAs($user);

    // Act: make a GET request to the show route
    $response = $this->from('/dashboard')->get(route('users.show', $user));

    // Assert: response is OK and Inertia view is correct
    $response->assertOk();

    $response->assertInertia(fn (AssertableInertia $page) => $page
      ->component('User/Show')
      ->has('user', fn ($userProps) => $userProps
        ->where('id', $user->id)
        ->etc()
      )
      ->where('previousUrl', url('/dashboard'))
    );
  }

  // public function test_show_user_myReports_page()
  // {
  //   $user = User::factory()->create();

  //   $this->actingAs($user);

  //   $response = $this->from('/')->get(route('users.myReports', $user));

  //   $response->assertOk();

  //   $response->assertInertia(fn (AssertableInertia $page) => $page
  //     ->component('User/MyReports')
  //     ->has('user', fn ($userProps) => $userProps
  //       ->where('id', $user->id)
  //       ->etc()
  //     )
  //     ->where('previousUrl', url('/'))
  //   );
  // }
}
