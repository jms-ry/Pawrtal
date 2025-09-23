<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Report;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Rescue;
class AdminStaffController extends Controller
{
  public function index()
  {
    if(Gate::denies('admin-staff-access',Auth::user()))
    {
      return redirect('/')->with('error', 'You do not have authorization. Access denied!');
    }
    $rescues = Rescue::all();
    $reports = Report::all();
    $donatons = Donation::all();

    return Inertia::render('AdminStaff/Dashboard',[
      'rescues' => $rescues,
      'reports' => $reports,
      'donations' => $donatons

    ]);
  }
}
