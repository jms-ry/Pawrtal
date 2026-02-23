<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationStoreTest extends TestCase
{
  use RefreshDatabase;

  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function storeUrl(): string
  {
    return route('conversations.store');
  }

  private function createConversation(User $participant1, User $participant2): Conversation
  {
    return Conversation::factory()->create([
      'participant1_id' => $participant1->id,
      'participant2_id' => $participant2->id,
    ]);
  }

  // -------------------------------------------------------------------------
  // Authentication
  // -------------------------------------------------------------------------

  public function test_guest_cannot_create_a_conversation(): void
  {
    $other = User::factory()->create();

    $this->post($this->storeUrl(), ['participant_id' => $other->id])->assertRedirect(route('login'));
  }

  // -------------------------------------------------------------------------
  // Validation — participant_id field
  // -------------------------------------------------------------------------

  public function test_participant_id_is_required(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->post($this->storeUrl(), [])
    ->assertSessionHasErrors('participant_id');
  }

  public function test_participant_id_must_exist_in_users_table(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => 99999])
    ->assertSessionHasErrors('participant_id');
  }

  public function test_participant_id_cannot_be_the_authenticated_user_themselves(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => $user->id])
    ->assertSessionHasErrors('participant_id');
  }

  public function test_participant_id_must_be_a_valid_integer(): void
  {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => 'not-an-id'])
    ->assertSessionHasErrors('participant_id');
  }

  // -------------------------------------------------------------------------
  // Existing conversation — redirect without duplicate
  // -------------------------------------------------------------------------

  public function test_redirects_to_index_if_conversation_already_exists_as_participant1(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($user, $other);

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => $other->id])
    ->assertRedirect(route('conversations.index'));
  }

  public function test_redirects_to_index_if_conversation_already_exists_as_participant2(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($other, $user);

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => $other->id])
    ->assertRedirect(route('conversations.index'));
  }

  public function test_does_not_create_duplicate_conversation_when_one_already_exists_as_participant1(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($user, $other);

    $this->actingAs($user)->post($this->storeUrl(), ['participant_id' => $other->id]);

    $this->assertDatabaseCount('conversations', 1);
  }

  public function test_does_not_create_duplicate_conversation_when_one_already_exists_as_participant2(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($other, $user);

    $this->actingAs($user)->post($this->storeUrl(), ['participant_id' => $other->id]);

    $this->assertDatabaseCount('conversations', 1);
  }

  public function test_no_success_flash_message_when_conversation_already_exists(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->createConversation($user, $other);

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => $other->id])
    ->assertSessionMissing('success');
  }

  // -------------------------------------------------------------------------
  // New conversation creation
  // -------------------------------------------------------------------------

  public function test_creates_a_new_conversation_when_none_exists(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->post($this->storeUrl(), ['participant_id' => $other->id]);

    $this->assertDatabaseHas('conversations', [
      'participant1_id' => min($user->id, $other->id),
      'participant2_id' => max($user->id, $other->id),
    ]);
  }

  public function test_redirects_to_conversations_index_after_creating_a_new_conversation(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => $other->id])
    ->assertRedirect(route('conversations.index'));
  }

  public function test_success_flash_message_is_set_after_creating_a_new_conversation(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
      ->post($this->storeUrl(), ['participant_id' => $other->id])
    ->assertSessionHas('success', 'Conversation started successfully.');
  }

  public function test_only_one_conversation_is_created_per_store_call(): void
  {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->post($this->storeUrl(), ['participant_id' => $other->id]);

    $this->assertDatabaseCount('conversations', 1);
  }

  // -------------------------------------------------------------------------
  // Roles — any role can start a conversation with any other role
  // -------------------------------------------------------------------------

  public function test_admin_can_start_a_conversation_with_staff(): void
  {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->staff()->create();

    $this->actingAs($admin)
      ->post($this->storeUrl(), ['participant_id' => $staff->id])
    ->assertRedirect(route('conversations.index'));

    $this->assertDatabaseHas('conversations', [
      'participant1_id' => min($admin->id, $staff->id),
      'participant2_id' => max($admin->id, $staff->id),
    ]);
  }

  public function test_admin_can_start_a_conversation_with_regular_user(): void
  {
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create(['role' => 'regular_user']);

    $this->actingAs($admin)
      ->post($this->storeUrl(), ['participant_id' => $regularUser->id])
    ->assertRedirect(route('conversations.index'));

    $this->assertDatabaseHas('conversations', [
      'participant1_id' => min($admin->id, $regularUser->id),
      'participant2_id' => max($admin->id, $regularUser->id),
    ]);
  }

  public function test_staff_can_start_a_conversation_with_regular_user(): void
  {
    $staff = User::factory()->staff()->create();
    $regularUser = User::factory()->create();

    $this->actingAs($staff)
      ->post($this->storeUrl(), ['participant_id' => $regularUser->id])
    ->assertRedirect(route('conversations.index'));

    $this->assertDatabaseHas('conversations', [
      'participant1_id' => min($staff->id, $regularUser->id),
      'participant2_id' => max($staff->id, $regularUser->id),
    ]);
  }

  public function test_regular_user_can_start_a_conversation_with_another_regular_user(): void
  {
    $regularUser1 = User::factory()->create();
    $regularUser2 = User::factory()->create();

    $this->actingAs($regularUser1)
      ->post($this->storeUrl(), ['participant_id' => $regularUser2->id])
    ->assertRedirect(route('conversations.index'));

    $this->assertDatabaseHas('conversations', [
      'participant1_id' => min($regularUser1->id, $regularUser2->id),
      'participant2_id' => max($regularUser1->id, $regularUser2->id),
    ]);
  }
}