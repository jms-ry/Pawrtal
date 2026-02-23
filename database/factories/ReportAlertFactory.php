<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportAlert>
 */
class ReportAlertFactory extends Factory
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
      'report_id' => Report::factory(),
      'alerted_at' => $this->faker->dateTime(),
    ];
  }
}
