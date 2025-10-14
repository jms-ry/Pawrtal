<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
  protected static ?string $password;

  public function definition(): array
  {
    return [
      'first_name' => fake()->firstName(),
      'last_name' => fake()->lastName(),
      'contact_number' => '09' . fake()->numerify('#########'), // Matches your 09XXXXXXXXX format
      'email' => fake()->unique()->safeEmail(),
      //'email_verified_at' => now(),
      'password' => static::$password ??= Hash::make('password'),
      //'remember_token' => Str::random(10),
      'role' => 'regular_user',
    ];
  }

  public function admin(): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => 'admin',
    ]);
  }
  
   public function staff(): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => 'staff',
    ]);
  }

  public function regularUser(): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => 'regular_user',
    ]);
  }
    // public function unverified(): static
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'email_verified_at' => null,
    //     ]);
    // }
}