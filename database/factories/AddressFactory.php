<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AddressFactory extends Factory
{
  public function definition(): array
  {
    return [
      'barangay' => $this->faker->streetName(),
      'municipality' => $this->faker->city(),
      'province' => $this->faker->state(),
      'zip_code' => $this->faker->postcode(),
      'user_id' => User::factory(), // 👈 creates and links a user automatically
    ];
  }
}
