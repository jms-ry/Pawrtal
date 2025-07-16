<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminStaffController extends Controller
{
  public function index()
  {
    if(Gate::denies('admin-staff-access',Auth::user()))
    {
      return redirect('/')->with('error', 'You do not have authorization. Access denied!');
    }
    return view('admin-staff.dashboard');
  }
}
