<?php

namespace App\Policies;

use App\Models\Rescue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RescuePolicy
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
  public function view(User $user, Rescue $rescue): bool
  {
    if (!$rescue->trashed()) {
      return true;
    }

    return $user && $user->isAdminOrStaff();
  }

  /**
    * Determine whether the user can create models.
  */
  public function create(User $user): bool
  {
    return $user->isAdminOrStaff();
  }

  /**
    * Determine whether the user can update the model.
  */
  public function update(User $user, Rescue $rescue): bool
  {
    return $user->isAdminOrStaff();
  }

  /**
    * Determine whether the user can delete the model.
  */
  public function delete(User $user, Rescue $rescue): bool
  {
    if($rescue->trashed()) {
      return false;
    }
    return $user->isAdminOrStaff();
  }

  /**
    * Determine whether the user can restore the model.
  */
  public function restore(User $user, Rescue $rescue): bool
  {
    if(!$rescue->trashed()) {
      return false; // Can't restore what's not deleted
    }
    return $user->isAdminOrStaff();
  }

  /**
    * Determine whether the user can permanently delete the model.
  */
  public function forceDelete(User $user, Rescue $rescue): Response
  {
    if (!$user->isAdminOrStaff()) {
      return Response::deny('Only administrators and staff can permanently delete rescues.');
    }
    
    $activeApplicationsCount = $rescue->adoptionApplications()
      ->whereIn('status', ['pending', 'under_review', 'approved'])
    ->count();
    
    if ($activeApplicationsCount > 0) {
      return Response::deny(
        "Cannot permanently delete this rescue. It has {$activeApplicationsCount} active or approved adoption application(s). " .
        "Approved applications must be retained for record-keeping."
      );
    }
    
    return Response::allow();
  }
}