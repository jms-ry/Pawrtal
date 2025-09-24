<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use App\Models\Donation;
use App\Models\Report;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Rescue;
use Illuminate\Support\Str;
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
    $donatons = Donation::whereNotIn('status', ['archived', 'cancelled'])->get();
    $applications = AdoptionApplication::whereNotIn('status', ['archived', 'cancelled'])->get();
    $previousUrl = url()->previous();
    $showBackNav = !Str::contains($previousUrl, ['/login', '/register','/dashboard']);

    return Inertia::render('AdminStaff/Dashboard',[
      'rescues' => $rescues,
      'reports' => $reports,
      'donations' => $donatons,
      'applications' => $applications,
      'previousUrl' => $previousUrl,
      'showBackNav' => $showBackNav,
    ]);
  }

  public function rescues(Request $request)
  {
    if(Gate::denies('admin-staff-access',Auth::user()))
    {
      return redirect('/')->with('error', 'You do not have authorization. Access denied!');
    }
    $search = $request->get('search');
    $sexFilter = $request->get('sex');
    $sizeFilter = $request->get('size');
    $statusFilter = $request->get('status');

    $rescues = Rescue::query()
      ->withCount('adoptionApplications')
      ->when($search, function ($query, $search) {
        return $query->whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $search . '%']);
      })
      ->when($sexFilter, function ($query, $sexFilter) {
        return $query->where('sex', $sexFilter);
      })
      ->when($sizeFilter, function ($query, $sizeFilter) {
        return $query->where('size', $sizeFilter);
      })
      ->when($statusFilter, function ($query, $statusFilter) {
        return $query->where('adoption_status', $statusFilter);
      })
    ->paginate(9)
    ->withQueryString();

    $previousUrl = url()->previous();
    $showBackNav = !Str::contains($previousUrl, ['/login', '/register','/dashboard/rescues']);
    $user = Auth::user();

    return Inertia::render('AdminStaff/Rescues',[
      'rescues' => $rescues,
      'previousUrl' => $previousUrl,
      'showBackNav' => $showBackNav,
      'user' => $user ? [
        'id' => $user->id,
        'full_name' => $user->fullName(),
        'isAdminOrStaff' => $user->isAdminOrStaff(),
      ] : null,
      'filters' => [
        'search' => $search,
        'sex' => $sexFilter,
        'size' => $sizeFilter,
        'status' => $statusFilter,
      ],
    ]);
  }
}
