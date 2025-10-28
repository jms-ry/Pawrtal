<?php

namespace App\Policies;

use App\Models\InspectionSchedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Models\AdoptionApplication;

class InspectionSchedulePolicy
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
  public function view(User $user, InspectionSchedule $inspectionSchedule): bool
   {
    return false;
  }

  /**
    * Determine whether the user can create models.
  */
  public function create(User $user, AdoptionApplication $application)
  {
    if (!$user->isAdminOrStaff()) {
      return false;
    }
    
    // Can only create inspections for pending applications
    return $application->status === 'pending';
  }

  /**
    * Determine whether the user can update the model.
  */
  public function update(User $user, InspectionSchedule $inspectionSchedule): bool
  {
    return false;
  }

  /**
    * Determine whether the user can delete the model.
  */
  public function delete(User $user, InspectionSchedule $inspectionSchedule): bool
  {
    return false;
  }

   /**
    * Determine whether the user can restore the model.
  */
  public function restore(User $user, InspectionSchedule $inspectionSchedule): bool
  {
    return false;
  }

  /**
    * Determine whether the user can permanently delete the model.
  */
  public function forceDelete(User $user, InspectionSchedule $inspectionSchedule): bool
  {
    return false;
  }
}
