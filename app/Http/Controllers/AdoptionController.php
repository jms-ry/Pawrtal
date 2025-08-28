<?php

namespace App\Http\Controllers;

use App\Models\Rescue;
use Illuminate\Support\Facades\Auth;

class AdoptionController extends Controller
{
  public function index()
  {
    $adoptables = Rescue::where('adoption_status','available')->get();
    $user = Auth::user();

    return view('adoption.index',compact('adoptables','user'));
  }
}
