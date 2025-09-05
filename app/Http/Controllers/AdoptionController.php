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

    //return view('adoption.index',compact('adoptables','user'));

    return Inertia::render('Adoption/Index',[
      'adoptables' => $adoptables,
      'user' => $user ? [
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'id' => $user->id,
        'canAdopt' => $user->canAdopt(),
      ] : null,
    ]);
  }
}
