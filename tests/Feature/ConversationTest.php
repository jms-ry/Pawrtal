<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
{
  use RefreshDatabase;

  // Conversation model tests
  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function createConversation(User $participant1, User $participant2): Conversation
  {
    return Conversation::factory()->create([
      'participant1_id' => $participant1->id,
      'participant2_id' => $participant2->id,
    ]);
  }

  // -------------------------------------------------------------------------
  // otherParticipant()
  // -------------------------------------------------------------------------

  public function test_other_participant_returns_participant2_when_current_user_is_participant1(): void
  {
    $participant1 = User::factory()->create();
    $participant2 = User::factory()->create();

    $conversation = $this->createConversation($participant1, $participant2);

    $other = $conversation->otherParticipant($participant1->id);

    $this->assertEquals($participant2->id, $other->id);
  }

  public function test_other_participant_returns_participant1_when_current_user_is_participant2(): void
  {
    $participant1 = User::factory()->create();
    $participant2 = User::factory()->create();

    $conversation = $this->createConversation($participant1, $participant2);

    $other = $conversation->otherParticipant($participant2->id);

    $this->assertEquals($participant1->id, $other->id);
  }

  // -------------------------------------------------------------------------
  // findOrCreate()
  // -------------------------------------------------------------------------

  public function test_find_or_create_creates_a_new_conversation_when_none_exists(): void
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Conversation::findOrCreate($user1->id, $user2->id);

    $this->assertDatabaseHas('conversations', [
      'participant1_id' => min($user1->id, $user2->id),
      'participant2_id' => max($user1->id, $user2->id),
    ]);
  }

  public function test_find_or_create_always_assigns_lower_id_as_participant1(): void
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $conversation = Conversation::findOrCreate($user1->id, $user2->id);

    $this->assertEquals(min($user1->id, $user2->id), $conversation->participant1_id);
    $this->assertEquals(max($user1->id, $user2->id), $conversation->participant2_id);
  }

  public function test_find_or_create_assigns_correct_ids_regardless_of_argument_order(): void
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $conversationA = Conversation::findOrCreate($user1->id, $user2->id);
    $conversationB = Conversation::findOrCreate($user2->id, $user1->id);

    $this->assertEquals($conversationA->id, $conversationB->id);
    $this->assertEquals($conversationA->participant1_id, $conversationB->participant1_id);
    $this->assertEquals($conversationA->participant2_id, $conversationB->participant2_id);
  }

  public function test_find_or_create_returns_existing_conversation_instead_of_creating_a_duplicate(): void
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Conversation::findOrCreate($user1->id, $user2->id);
    Conversation::findOrCreate($user1->id, $user2->id);

    $this->assertDatabaseCount('conversations', 1);
  }

  public function test_find_or_create_returns_existing_conversation_when_called_with_reversed_argument_order(): void
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Conversation::findOrCreate($user1->id, $user2->id);
    Conversation::findOrCreate($user2->id, $user1->id);

    $this->assertDatabaseCount('conversations', 1);
  }

  public function test_find_or_create_returns_the_conversation_model(): void
  {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $conversation = Conversation::findOrCreate($user1->id, $user2->id);

    $this->assertInstanceOf(Conversation::class, $conversation);
  }
}