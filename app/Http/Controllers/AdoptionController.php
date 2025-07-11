<?php

namespace App\Http\Controllers;

use App\Models\Rescue;

class AdoptionController extends Controller
{
  public function index()
  {
    $adoptables = Rescue::where('adoption_status','available')->get();

    return view('adoption.index',compact('adoptables'));
  }
}
