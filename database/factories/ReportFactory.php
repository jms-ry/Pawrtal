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
      'species' => $this->faker->randomElement(['Dog', 'Cat']),
      'breed' => $this->faker->word(),
      'color' => $this->faker->safeColorName(),
      'sex' => $this->faker->randomElement(['male', 'female']),
      'age_estimate' => $this->faker->numberBetween(1, 10),
      'size' => $this->faker->randomElement(['small', 'medium', 'large']),
      'distinctive_features' => $this->faker->sentence(),
      'image' => $this->faker->imageUrl(640, 480, 'animals', true),
      'status' => $this->faker->randomElement(['active', 'resolved']),
      'deleted_at' => null,
    ];
  }

  public function lost()
  {
    return $this->state(fn (array $attributes) => [
      'type' => 'lost',
      'animal_name' => $this->faker->firstName(),
      'last_seen_location' => $this->faker->streetAddress(),
      'last_seen_date' => $this->faker->date(),
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
      'found_location' => $this->faker->streetAddress(),
      'found_date' => $this->faker->date(),
      'condition' => $this->faker->sentence(),
      'temporary_shelter' => $this->faker->company(),
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
