<?php

namespace App\Http\Controllers;


use App\Models\Rescue;
use Inertia\Inertia;

class WelcomeController extends Controller
{
  public function index()
  {
    $shelteredCount = Rescue::count();
    $spayedNeuteredCount = Rescue::where('spayed_neutered', true)->count();
    $vaccinatedCount = Rescue::where('vaccination_status', 'vaccinated')->count();
    $adoptedCount = Rescue::where('adoption_status', 'adopted')->count();
    $rescues = Rescue::orderBy('name', 'desc')->take(4)->get();
    
    return Inertia::render('Welcome/Index',[
      'shelteredCount' => $shelteredCount,
      'spayedNeuteredCount' => $spayedNeuteredCount,
      'vaccinatedCount' => $vaccinatedCount,
      'adoptedCount' => $adoptedCount,
      'rescues' => $rescues
    ]);
  }
}
