<?php

namespace App\Policies;

use App\Models\AdoptionApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdoptionApplicationPolicy
{
  /**
    * Determine whether the user can view any models.
  */
  public function viewAny(User $user): bool
  {
    return false;
  }

  /**
    * Determine whether the user can view the model.
  */
  public function view(User $user, AdoptionApplication $adoptionApplication): bool
  {
    return false;
  }

  /**
    * Determine whether the user can create models.
  */
  public function create(User $user): bool
  {
    return $user->canAdopt();
  }

  /**
    * Determine whether the user can update the model.
  */
  public function update(User $user, AdoptionApplication $adoptionApplication): bool
  {
    if($adoptionApplication->status === 'pending'){
      return $adoptionApplication->user_id === $user->id;
    }

    return false;
  }

  /**
    * Determine whether the user can delete the model.
  */
  public function delete(User $user, AdoptionApplication $adoptionApplication): bool
  {
    if($adoptionApplication->trashed()){
      return false;
    }
    
    if (in_array($adoptionApplication->status, ['approved', 'rejected'])) {
      return $adoptionApplication->user_id === $user->id;
    }

    return false;
  }

  /**
    * Determine whether the user can restore the model.
  */
  public function restore(User $user, AdoptionApplication $adoptionApplication): bool
  {
    if(!$adoptionApplication->trashed()){
      return false;
    }

    return $adoptionApplication->user_id === $user->id;
  }

  /**
    * Determine whether the user can permanently delete the model.
  */
  public function forceDelete(User $user, AdoptionApplication $adoptionApplication): Response
  { 
    if($adoptionApplication->user_id !== $user->id)
    {
      return Response::deny('Only application owner can delete.');
    }

    if($adoptionApplication->status === 'cancelled'){
      return Response::allow();
    }
    
    return Response::deny();
  }

  public function cancel(User $user, AdoptionApplication $adoptionApplication): bool
  {
    if($adoptionApplication->status === 'pending'){
      return $adoptionApplication->user_id === $user->id && !$user->isAdminOrStaff();
    }

    return false;
  }

  public function approve(User $user, AdoptionApplication $adoptionApplication): bool
  {
    if($adoptionApplication->status === 'under_review'){
      return $user->isAdminOrStaff();
    }

    return false;
  }

  public function reject(User $user, AdoptionApplication $adoptionApplication): bool
  {
    if (in_array($adoptionApplication->status, ['pending', 'under_review'])) {
      return $user->isAdminOrStaff();
    }

    return false;
  }
}
