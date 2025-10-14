<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Carbon\Carbon;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
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
      'donation_type' => 'in-kind',
      'status' => 'pending',
      'donation_date' => Carbon::now()
    ];
  }

  public function monetary()
  {
    return $this->state(fn (array $attributes) => [
      'amount'=> $this->faker->numberBetween(1, 99999),
      'donation_type' => 'monetary',
      'item_description' => null,
      'item_quantity' => null,
      'pick_up_location' => null,
      'contact_person' => null,
      'donation_image' => null,
    ]);
    
  }

  public function inKind()
  {
    return $this->state(fn (array $attributes) => [
      'amount'=> null,
      'donation_type' => 'in-kind',
      'item_description' => $this->faker->sentence(),
      'item_quantity' => $this->faker->numberBetween(1, 99999),
      'pick_up_location' => $this->faker->streetAddress(),
      'contact_person' => $this->faker->firstName(),
      'donation_image' => $this->faker->imageUrl(640, 480, 'donations', true)
    ]);
  }

  public function accepted()
  {
    return $this->state(fn (array $attributes) => [
      'status' => 'accepted',
    ]);
  }

  public function rejected()
  {
    return $this->state(fn (array $attributes) => [
      'status' => 'rejected',
    ]);
  }

  public function cancelled()
  {
    return $this->state(fn (array $attributes) => [
      'status' => 'cancelled',
    ]);
  }
  public function trashed(): static
  {
    return $this->state(fn (array $attributes) => [
      'deleted_at' => Carbon::now(),
    ]);
  }
}
