<?php

namespace Database\Factories;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class HouseholdFactory extends Factory
{ 
  protected $model = Household::class;
  public function definition(): array
  {
    return [
      'house_structure' => $this->faker->randomElement(['Concrete', 'Wood', 'Mixed']),
      'household_members' => $this->faker->numberBetween(1, 8),
      'have_children' => $this->faker->boolean(),
      'number_of_children' => $this->faker->numberBetween(0, 4),
      'has_other_pets' => $this->faker->boolean(),
      'current_pets' => $this->faker->word(),
      'number_of_current_pets' => $this->faker->numberBetween(0, 5),
      'user_id' => User::factory(), // creates and links a user automatically
    ];
  }
}
