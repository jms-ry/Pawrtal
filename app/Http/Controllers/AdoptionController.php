<?php

namespace App\Http\Controllers;

use App\Models\Rescue;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdoptionController extends Controller
{
  public function index()
  {
    $adoptables = Rescue::where('adoption_status','available')->get();
    $user = Auth::user();

    $user = $user?->load('address', 'household');

    return Inertia::render('Adoption/Index',[
      'adoptables' => $adoptables,
      'user' => $user ? [
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'id' => $user->id,
        'canAdopt' => $user->canAdopt(),
        'address' => $user->address,
        'household' => $user->household,
      ] : null,
    ]);
  }
}
