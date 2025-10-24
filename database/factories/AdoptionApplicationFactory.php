<?php

namespace Database\Factories;

use App\Models\Rescue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdoptionApplication>
 */
class AdoptionApplicationFactory extends Factory
{
  /**
    * Define the model's default state.
    *
    * @return array<string, mixed>
  */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'rescue_id' => Rescue::factory(),
      'application_date' => Carbon::now(),
      'status' => 'pending',
      'reason_for_adoption' => fake()->sentence(),
      'preferred_inspection_start_date' => Carbon::now()->addDays(3),
      'preferred_inspection_end_date' => Carbon::now()->addDays(7),
      'valid_id' => fake()->imageUrl(640, 480, 'documents', true),
      'supporting_documents' => [
        fake()->imageUrl(640, 480, 'documents', true),
        fake()->imageUrl(640, 480, 'documents', true)
      ],
      'reviewed_by' => null,
      'review_date' => null,
      'review_notes' => null,
    ];
  }

  public function approved()
  {
    return $this->state(fn (array $attributes) => [
      'status' => 'approved',
      'review_date' => Carbon::now(),
      'review_notes' => "We are glad to announce that your adoption application has been approved."
    ]);
  }

  public function rejected()
  {
    return $this->state(fn (array $attributes) => [
      'status' => 'rejected',
      'review_date' => Carbon::now(),
      'review_notes' => "Unfortunately, we decided to reject your adoption application."
    ]);
  }

  public function cancelled()
  {
    return $this->state(fn (array $attributes) => [
      'status' => 'cancelled',
    ]);
  }

  public function under_review()
  {
    return $this->state(fn (array $attributes) => [
      'status' => 'under_review',
    ]);
  }
  public function trashed(): static
  {
    return $this->state(fn (array $attributes) => [
      'deleted_at' => Carbon::now(),
    ]);
  }
}
