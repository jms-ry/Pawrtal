<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Address;
use App\Models\Donation;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Report;
class UserShowTest extends TestCase
{
  use RefreshDatabase;
  /** Start of show function test cases */

  // Happy path with relationships
  public function test_user_can_view_their_profile_with_relationships()
  {
    $user = User::factory()
      ->has(Address::factory())
      ->has(Household::factory())
    ->create();
      
    $this->actingAs($user);
      
    $response = $this->from('/')->get(route('users.show', $user));
      
    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
      ->component('User/Show')
      ->where('user.id', $user->id)
      ->has('user.address')
      ->has('user.household')
      ->where('previousUrl', url('/'))
    );
  }

  // Happy path without relationships
  public function test_user_can_view_their_profile_without_relationships()
  {
    $user = User::factory()->create();
      
    $this->actingAs($user);
      
    $response = $this->get(route('users.show', $user));
      
    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
      ->component('User/Show')
      ->where('user.id', $user->id)
      ->where('user.address', null)
      ->where('user.household', null)
    );
  }

  public function test_user_cannot_view_another_users_profile()
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $this->actingAs($user1);
    
    $response = $this->get(route('users.show', $user2));
    
    $response->assertForbidden();
  }

  public function test_guest_cannot_view_user_profile()
  {
    $user = User::factory()->create();
    
    $response = $this->get(route('users.show', $user));
    
    $response->assertRedirect(route('login'));
  }

  public function test_show_returns_404_for_nonexistent_user()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->get(route('users.show', 99999));
    
    $response->assertNotFound();
  }

  public function test_show_page_includes_previous_url()
  {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->from('/')->get(route('users.show', $user));
    
    $response->assertInertia(fn (AssertableInertia $page) => $page
      ->component('User/Show')
      ->where('previousUrl', url('/'))
    );
  }
  /** End of show function test cases */
  
}
