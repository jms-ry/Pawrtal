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
      'reason_for_adoption' => $this->faker->sentence(),
      'preferred_inspection_start_date' => Carbon::now()->addDays(3),
      'preferred_inspection_end_date' => Carbon::now()->addDays(7),
      'valid_id' => $this->faker->imageUrl(640, 480, 'documents', true),
      'supporting_documents' => [
        $this->faker->imageUrl(640, 480, 'documents', true),
        $this->faker->imageUrl(640, 480, 'documents', true)
      ],
      'reviewed_by' => null,
      'review_date' => null,
      'review_notes' => null,
    ];
  }
}
