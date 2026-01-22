<?php

namespace Database\Factories;

use App\Models\AdoptionApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InspectionSchedule>
 */
class InspectionScheduleFactory extends Factory
{
  /**
    * Define the model's default state.
    *
    * @return array<string, mixed>
  */
  public function definition(): array
  {
    return [
      'application_id' => AdoptionApplication::factory(),
      'inspector_id' => User::factory()->staff(),
      'inspection_location' => fake()->streetAddress(),
      'inspection_date' => now(),
      'status' => 'now'
    ];
  }
}
