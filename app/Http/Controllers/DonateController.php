<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class DonateController extends Controller
{
  public function index()
  {
    $user = Auth::user();

    return Inertia::render('Donate/Index',['user' => $user]);
  }
}
