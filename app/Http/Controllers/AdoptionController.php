<?php

namespace App\Http\Controllers;

use App\Models\Rescue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdoptionController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->get('search');
    $sexFilter = $request->get('sex');
    $sizeFilter = $request->get('size');

    $user = Auth::user();

    $user = $user?->load('address', 'household');
    
    $adoptables = Rescue::query()
      ->where('adoption_status','available')
      ->visibleTo($user)
      ->withCount('adoptionApplications')
      ->when($search, function($query,$search){
        return $query->whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $search . '%']);
      })
      ->when($sexFilter, function ($query, $sexFilter) {
        return $query->where('sex', $sexFilter);
      })
      ->when($sizeFilter, function ($query, $sizeFilter) {
        return $query->where('size', $sizeFilter);
      })
      ->paginate(9)
    ->withQueryString();
    
    if ($user && !$user->isAdminOrStaff()) {
      $adoptables->getCollection()->transform(function ($adoptable) use ($user) {
        // Check if user has an active application for this rescue
        $activeApplication = $user->adoptionApplications()
          ->where('rescue_id', $adoptable->id)
          ->whereIn('status', ['pending', 'under_review', 'approved'])
        ->exists();
            
        $adoptable->user_has_active_application = $activeApplication;
            
        return $adoptable;
      });
    }

    return Inertia::render('Adoption/Index',[
      'adoptables' => $adoptables,
      'user' => $user ? [
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'id' => $user->id,
        'canAdopt' => $user->canAdopt(),
        'address' => $user->address,
        'household' => $user->household,
      ] : null,
      'filters' => [
        'search' => $search,
        'sex' => $sexFilter,
        'size' => $sizeFilter,
      ],
    ]);
  }
}
