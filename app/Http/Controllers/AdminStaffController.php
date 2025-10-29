<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use App\Models\Donation;
use App\Models\InspectionSchedule;
use App\Models\Report;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Rescue;
use App\Models\User;
use Illuminate\Support\Str;
class AdminStaffController extends Controller
{
  public function index()
  {
    if(Gate::denies('admin-staff-access',Auth::user()))
    {
      return redirect('/')->with('error', 'You do not have authorization. Access denied!');
    }
    $rescues = Rescue::withTrashed()->get();
    $reports = Report::withTrashed()->get();
    $donatons = Donation::withTrashed()->get();
    $applications = AdoptionApplication::withTrashed()->get();
    $schedules = InspectionSchedule::with(['user','adoptionApplication'])->get()->map(function ($schedule) {
      return [
        'id' => $schedule->id,
        'inspector_name' => $schedule->inspectorName(),
        'inspection_location' => $schedule->inspectionLocation(),
        'inspection_date' => $schedule->inspection_date,
        'status' => $schedule->status
      ];
    });
    $previousUrl = url()->previous();
    $showBackNav = !Str::contains($previousUrl, ['/login', '/register','/dashboard']);

    return Inertia::render('AdminStaff/Dashboard',[
      'rescues' => $rescues,
      'reports' => $reports,
      'donations' => $donatons,
      'applications' => $applications,
      'previousUrl' => $previousUrl,
      'showBackNav' => $showBackNav,
      'schedules' => $schedules
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
      ->withTrashed()
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
      ->orderBy('id', 'asc')
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

  public function reports(Request $request)
  {
    if(Gate::denies('admin-staff-access',Auth::user()))
    {
      return redirect('/')->with('error', 'You do not have authorization. Access denied!');
    }

    $search = $request->get('search');
    $statusFilter = $request->get('status');
    $typeFilter = $request->get('type');;
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;

    $reports = Report::query()
      ->withTrashed()
      ->with('user')
      ->when($search, function ($query, $search) {
        $columns = ['animal_name','species', 'sex' ,'breed', 'color', 'type'];
        $keywords = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        return $query->where(function ($q) use ($keywords, $columns) {
          foreach ($keywords as $word) {
            $q->where(function ($subQ) use ($word, $columns) {
              foreach ($columns as $col) {
                $subQ->orWhereRaw("LOWER($col) LIKE LOWER(?)", ['%' . $word . '%']);
              }
            });
          }
        });
      })
      ->when($typeFilter, function ($query, $typeFilter) {
        return $query->where('type', $typeFilter);
      })
      ->when($statusFilter, function ($query, $statusFilter) {
        return $query->where('status', $statusFilter);
      })
      ->when($sortOrder, function($query,$sortOrder){
        return $query->orderBy('created_at',$sortOrder);
      })
      ->orderBy('created_at', 'desc')
      ->paginate(9)
    ->withQueryString();

    $previousUrl = url()->previous();
    $showBackNav = !Str::contains($previousUrl, ['/login', '/register','/dashboard/reports']);

    return Inertia::render('AdminStaff/Reports',[
      'reports' => $reports,
      'previousUrl' => $previousUrl,
      'showBackNav' => $showBackNav,
      'filters' => [
        'search' => $search,
        'type' => $typeFilter,
        'status' => $statusFilter,
        'sort' => $sortOrder,
      ],
    ]);
  }

  public function donations(Request $request)
  {
    if(Gate::denies('admin-staff-access',Auth::user()))
    {
      return redirect('/')->with('error', 'You do not have authorization. Access denied!');
    }

    $search = $request->get('search');
    $typeFilter = $request->get('donation_type');;
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;

    $donations = Donation::query()
      ->withTrashed()
      ->with('user')
      ->when($search, function ($query, $search) {
        $columns = ['item_description','contact_person', 'pick_up_location' ,'status', 'donation_type'];
        $keywords = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        return $query->where(function ($q) use ($keywords, $columns) {
          foreach ($keywords as $word) {
            $q->where(function ($subQ) use ($word, $columns) {
              foreach ($columns as $col) {
                $subQ->orWhereRaw("LOWER($col) LIKE LOWER(?)", ['%' . $word . '%']);
              }
            });
          }
        });
      })
      ->when($typeFilter, function ($query, $typeFilter) {
        return $query->where('donation_type', $typeFilter);
      })
      ->when($statusFilter, function ($query, $statusFilter) {
        return $query->where('status', $statusFilter);
      })
      ->when($sortOrder, function($query,$sortOrder){
        return $query->orderBy('donation_date',$sortOrder);
      })
      ->orderBy('donation_date', 'desc')
      ->paginate(9)
    ->withQueryString();

    $previousUrl = url()->previous();
    $showBackNav = !Str::contains($previousUrl, ['/login', '/register','/dashboard/donations']);

    return Inertia::render('AdminStaff/Donations',[
      'donations' => $donations,
      'previousUrl' => $previousUrl,
      'showBackNav' => $showBackNav,
      'filters' => [
        'search' => $search,
        'donation_type' => $typeFilter,
        'status' => $statusFilter,
        'sort' => $sortOrder,
      ],
    ]);
  }

  public function adoptionApplications(Request $request)
  {
    if(Gate::denies('admin-staff-access',Auth::user()))
    {
      return redirect('/')->with('error', 'You do not have authorization. Access denied!');
    }

    $search = $request->get('search');
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;

    $adoptionApplications = AdoptionApplication::query()
      ->withTrashed()
      ->with(['user','rescue','inspectionSchedule'])
      ->withCount('inspectionSchedule')
      ->when($search, function ($query, $search) {
        $keywords = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);
          
        return $query->where(function ($q) use ($keywords) {
          foreach ($keywords as $word) {
            $q->where(function ($subQ) use ($word) {
              // Search in direct columns
              $subQ->whereRaw("LOWER(reason_for_adoption) LIKE LOWER(?)", ['%' . $word . '%'])
                ->orWhereRaw("LOWER(status) LIKE LOWER(?)", ['%' . $word . '%'])
                // Search in rescue name
              ->orWhereHas('rescue', function ($rescueQ) use ($word) {
                $rescueQ->whereRaw("LOWER(name) LIKE LOWER(?)", ['%' . $word . '%']);
              })
              // Search in user first name
              ->orWhereHas('user', function ($userQ) use ($word) {
              $userQ->whereRaw("LOWER(first_name) LIKE LOWER(?)", ['%' . $word . '%'])
                ->orWhereRaw("LOWER(last_name) LIKE LOWER(?)", ['%' . $word . '%']);
              });
            });
          }
        });
      })
      ->when($statusFilter, function ($query, $statusFilter) {
        return $query->where('status', $statusFilter);
      })
      ->when($sortOrder, function($query,$sortOrder){
        return $query->orderBy('application_date',$sortOrder);
      })
      ->orderBy('application_date', 'desc')
      ->paginate(9)
    ->withQueryString();
    $user = Auth::user();
    $inspectors = User::query()->whereIn('role', ['admin', 'staff'])->get();
    $previousUrl = url()->previous();
    $showBackNav = !Str::contains($previousUrl, ['/login', '/register','/dashboard/adoption-applications']);
      
    return Inertia::render('AdminStaff/AdoptionApplications',[
      'adoptionApplications' => $adoptionApplications,
      'previousUrl' => $previousUrl,
      'showBackNav' => $showBackNav,
      'filters' => [
        'search' => $search,
        'status' => $statusFilter,
        'sort' => $sortOrder,
      ],
      'inspectors' => $inspectors,
      'user' => $user?[
        'role' => $user->role,
        'fullName' => $user->fullName()
      ]:null
    ]); 
    
  }
}
