<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Carbon\Carbon;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
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
      'type' => 'lost', // default type
      'species' => fake()->randomElement(['Dog', 'Cat']),
      'breed' => fake()->word(),
      'color' => fake()->safeColorName(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age_estimate' => fake()->numberBetween(1, 10),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => fake()->sentence(),
      'image' => fake()->imageUrl(640, 480, 'animals', true),
      'status' => 'active',
      'deleted_at' => null,
    ];
  }

  public function lost()
  {
    return $this->state(fn (array $attributes) => [
      'type' => 'lost',
      'animal_name' => fake()->firstName(),
      'last_seen_location' => fake()->streetAddress(),
      'last_seen_date' => fake()->date(),
      // remove found-only fields just to keep data consistent
      'found_location' => null,
      'found_date' => null,
      'condition' => null,
      'temporary_shelter' => null,
    ]);
  }

  public function found()
  {
    return $this->state(fn (array $attributes) => [
      'type' => 'found',
      'found_location' => fake()->streetAddress(),
      'found_date' => fake()->date(),
      'condition' => fake()->sentence(),
      'temporary_shelter' => fake()->company(),
      // remove lost-only fields
      'animal_name' => null,
      'last_seen_location' => null,
      'last_seen_date' => null,
    ]);
  }

  public function trashed(): static
  {
    return $this->state(fn (array $attributes) => [
      'deleted_at' => Carbon::now(),
    ]);
  }
}
