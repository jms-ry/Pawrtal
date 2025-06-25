<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
  public function index()
  {
    $shelteredCount = Animal::count();
    $spayedNeuteredCount = Animal::where('spayed_neutered', true)->count();
    $vaccinatedCount = Animal::where('vaccination_status', 'vaccinated')->count();
    $adoptedCount = Animal::where('adoption_status', 'adopted')->count();
    $rescues = Animal::orderBy('name', 'desc')->take(4)->get();
    
    return view('welcome', compact('shelteredCount','spayedNeuteredCount','vaccinatedCount','adoptedCount','rescues'));
  }
}
