<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Address;

class AddressPolicy
{
  /**
    * Create a new policy instance.
  */
  public function __construct()
  {
    //
  }

  public function delete(User $user, Address $address): bool
  {
    return $user->id === $address->user_id;
  }

  public function update(User $user, Address $address): bool
  {
    return $user->id === $address->user_id;
  }
}
