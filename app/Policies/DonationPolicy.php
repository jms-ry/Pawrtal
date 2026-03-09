<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DonationPolicy
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
  public function view(User $user, Donation $donation): bool
  {
    return false;
  }

  /**
    * Determine whether the user can create models.
  */
  public function create(User $user): bool
  {
    return false;
  }

  /**
    * Determine whether the user can update the model.
  */
  public function update(User $user, Donation $donation): bool
  {
    return $donation->user_id === $user->id;
  }

  /**
    * Determine whether the user can delete the model.
  */
  public function delete(User $user, Donation $donation): bool
  {
    if($donation->trashed())
    {
      return false;
    }
    return $donation->user_id === $user->id || $user->isAdminOrStaff();
  }

  /**
    * Determine whether the user can restore the model.
  */
  public function restore(User $user, Donation $donation): bool
  {
    if(!$donation->trashed())
    {
      return false;
    }
    return $donation->user_id === $user->id || $user->isAdminOrStaff();
  }

  /**
    * Determine whether the user can permanently delete the model.
  */
  public function forceDelete(User $user, Donation $donation): Response
  {
    // Only donation owner can delete their own donations
    if ($donation->user_id !== $user->id) {
      return Response::deny('Only donation owner can permanently delete their donations.');
    }
    
    // Can delete anything except accepted donations
    if (in_array($donation->status, ['rejected', 'cancelled'])) {
      return Response::allow();
    }
    
    // Accepted donations are permanent records for transparency
    return Response::deny(
      'Only rejected or cancelled donations can be permanently deleted. ' .
      'Pending donations must be cancelled first, and accepted donations are kept for transparency.'
    );
  }

  public function cancel(User $user, Donation $donation): bool
  {
    return $donation->user_id === $user->id && $donation->status === 'pending';
  }

  public function accept(User $user, Donation $donation): bool
  {
    return $user->isAdminOrStaff() && $donation->status === 'pending';
  }

  public function reject(User $user, Donation $donation): bool
  {
    return $user->isAdminOrStaff() && $donation->status === 'pending';
  }
}
