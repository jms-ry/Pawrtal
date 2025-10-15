<?php

namespace Database\Factories;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class HouseholdFactory extends Factory
{ 
    public function definition(): array
  {
    return [
      'house_structure' => $this->faker->word(),
      'household_members' => $this->faker->numberBetween(1, 9999),
      'have_children' => $this->faker->boolean(),
      'number_of_children' => $this->faker->numberBetween(0, 9999),
      'has_other_pets' => $this->faker->boolean(),
      'current_pets' => $this->faker->word(),
      'number_of_current_pets' => $this->faker->numberBetween(0, 9999),
      'user_id' => User::factory(), // creates and links a user automatically
    ];
  }
}
