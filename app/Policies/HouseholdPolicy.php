<?php

namespace App\Policies;

use App\Models\Household;
use App\Models\User;

class HouseholdPolicy
{
  /**
    * Create a new policy instance.
  */
  public function __construct()
  {
    //
  }

  public function update(User $user, Household $household)
  {
    return $user->id === $household->user_id;
  }

  public function delete(User $user, Household $household)
  {
    return $user->id === $household->user_id;
  }
}
