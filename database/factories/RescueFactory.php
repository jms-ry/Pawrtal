<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rescue>
 */
class RescueFactory extends Factory
{
  /**
    * Define the model's default state.
    *
    * @return array<string, mixed>
  */
  public function definition(): array
  {
    return [
      //this is default rescue factory, it's an adopted rescue
      'name' => fake()->firstName(),
      'species' => 'fake()->word()',
      'breed' => fake()->word(),
      'description' => fake()->sentence(),
      'sex' => fake()->randomElement(['male', 'female']),
      'age' => fake()->word(),
      'size' => fake()->randomElement(['small', 'medium', 'large']),
      'color' => fake()->colorName(),
      'distinctive_features' => fake()->sentence(),
      'profile_image' => fake()->imageUrl(640,480,'animals',true),
      'images' => collect(range(1, fake()->numberBetween(1, 5)))
        ->map(fn() => fake()->imageUrl(640, 480, 'animals', true))
      ->toArray(),
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'adopted'

    ];
  }
  
  //health status override
  public function injured()
  {
    return $this->state(fn (array $attributes) => [
      'health_status' => 'injured',
    ]);
  }

  public function sick()
  {
    return $this->state(fn (array $attributes) => [
      'health_status' => 'sick',
    ]);
  }

  //vaccination status override
  public function partially_vaccinated()
  {
    return $this->state(fn (array $attributes) => [
      'vaccination_status' => 'partially_vaccinated',
    ]);
  }

  public function not_vaccinated()
  {
    return $this->state(fn (array $attributes) => [
      'vaccination_status' => 'not_vaccinated',
    ]);
  }

  //spayed_neutered override
  public function not_neutered()
  {
    return $this->state(fn (array $attributes) => [
      'spayed_neutered' => false,
    ]);
  }

  //adoption status override
  public function available()
  {
    return $this->state(fn (array $attributes) => [
      'adoption_status' => 'available',
    ]);
  }

  public function unavailable()
  {
    return $this->state(fn (array $attributes) => [
      'adoption_status' => 'unavailable',
    ]);
  }
  public function trashed(): static
  {
    return $this->state(fn (array $attributes) => [
      'deleted_at' => Carbon::now(),
    ]);
  }
}
