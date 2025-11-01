<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    //
  }

  /**
    * Show the form for creating a new resource.
  */
  public function create()
  {
    //
  }

  /**
    * Store a newly created resource in storage.
  */
  public function store(Request $request)
  {
    //
  }

  /**
    * Display the specified resource.
  */
  public function show(User $user)
  {
    $this->authorize('view', $user);
    
    $previousUrl = url()->previous();
      
    $user->load(['address', 'household']);
      
    return Inertia::render('User/Show',[
      'user'=>$user,
      'previousUrl'=>$previousUrl,
    ]);
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(User $user)
    {
        //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(UpdateUserRequest $request, User $user)
    {
      $requestData = $request->all();

      if (!empty($requestData['password'])) {
        $user->password = $requestData['password'];
      }

      $user->update($requestData);

      return redirect()->back()->with('info', 'User profile has been updated!');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(User $user)
  {
    //
  }

  /**My Reports function */
  public function myReports(Request $request)
  { 
    $previousUrl = url()->previous();
    $user = Auth::user();
    $search = $request->get('search');
    $typeFilter = $request->get('type');;
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;

    $reports = $user->reports()
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
      ->paginate(10)
    ->withQueryString();
    
    return Inertia::render('User/MyReports',[
      'user' => $user ? ['fullName' => $user->fullName(),'id' => $user->id,] : null,
      'reports' => $reports,
      'filters' => [
        'search' => $search,
        'type' => $typeFilter,
        'status' => $statusFilter,
        'sort' => $sortOrder,
      ],
      'previousUrl' => $previousUrl,
    ]);
  }

  public function myDonations(Request $request)
  {
    $previousUrl = url()->previous();
    $user = Auth::user();
    $search = $request->get('search');
    $typeFilter = $request->get('donation_type');;
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;

    $donations = $user->donations()
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
      ->paginate(10)
    ->withQueryString();

    return Inertia::render('User/MyDonations',[
      'user' => $user ? ['fullName' => $user->fullName(),'id' => $user->id,] : null,
      'donations' => $donations,
      'filters' => [
        'search' => $search,
        'donation_type' => $typeFilter,
        'status' => $statusFilter,
        'sort' => $sortOrder,
      ],
      'previousUrl' => $previousUrl,
    ]);
  }

  public function myAdoptionApplications(Request $request)
  {
    $previousUrl = url()->previous();
    $user = Auth::user();

    if($user->isAdminOrStaff()){
      return redirect()->back()->with('error', 'You are not authorized to access this page.');
    }
    
    $search = $request->get('search');
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;
    $adoptionApplications = $user->adoptionApplications()
      ->withTrashed()
      ->withCount('inspectionSchedule')
      ->with(['user','rescue'])
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
      ->paginate(10)
    ->withQueryString();
    return Inertia::render('User/MyAdoptionApp',[
      'user' => $user ? ['fullName' => $user->fullName(),'id' => $user->id,'role' =>$user->role] : null,
      'adoptionApplications' => $adoptionApplications,
      'filters' => [
        'search' => $search,
        'status' => $statusFilter,
        'sort' => $sortOrder,
      ],
      'previousUrl' => $previousUrl,
    ]);
  }

  public function mySchedules()
  {
    $previousUrl = url()->previous();
    $user = Auth::user();

    if(!$user->isAdminOrStaff()){
      return redirect()->back()->with('error', 'You are not authorized to access this page.');
    }

    $schedules = $user->inspectionSchedules()
      ->with(['user','adoptionApplication'])
      ->get()->map(function ($schedule) {
      return [
        'id' => $schedule->id,
        'inspector_name' => $schedule->inspectorName(),
        'inspection_location' => $schedule->inspectionLocation(),
        'inspection_date' => $schedule->inspection_date,
        'status' => $schedule->status
      ];
    });
    
    return Inertia::render('User/MySchedules',[
      'user' => $user ? ['fullName' => $user->fullName(),'id' => $user->id,'role' =>$user->role] : null,
      'previousUrl' => $previousUrl,
      'schedules' => $schedules
    ]);
  }

  public function myNotifications(Request $request)
  {
    $previousUrl = url()->previous();
    $user = Auth::user();
    $search = $request->get('search');
    $readAtFilter = $request->get('read_at');

    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;

    $notifications = $user->notifications()
      ->when($search, function ($query, $search) {
        $keywords = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);

        return $query->where(function ($q) use ($keywords) {
          foreach ($keywords as $word) {
            $q->orWhereRaw("LOWER((data::jsonb->>'message')) LIKE LOWER(?)", ['%' . $word . '%']);
          }
        });
      })
      ->when($readAtFilter === 'unread', function ($query) {
        return $query->whereNull('read_at'); 
      })
      ->when($readAtFilter === 'read', function ($query) {
        return $query->whereNotNull('read_at'); 
      })
      ->when($sortOrder, function ($query, $sortOrder) {
        return $query->reorder()->orderBy('created_at', $sortOrder);
      })
      ->orderBy('read_at','desc')
      ->paginate(10)
    ->withQueryString();

    return Inertia::render('User/MyNotifications',[
      'user' => $user ? ['fullName' => $user->fullName(),'id' => $user->id,'role' =>$user->role] : null,
      'previousUrl' => $previousUrl,
      'notifications' => $notifications,
      'filters' => [
        'search' => $search,
        'sort' => $sortOrder,
        'read_at' => $readAtFilter,
      ],
    ]);
  }

  public function markNotificationAsRead(Request $request, $id)
  {
    $notification = DatabaseNotification::find($id);

    if (!$notification) {
      return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    if ($notification->notifiable_id !== $request->user()->id) {
      return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
    }
    if ($notification->read_at !== null) {
      return response()->json(['success' => false, 'message' => 'Notification already read'], 400);
    }

    $notification->markAsRead();
    
    return response()->json(['success' => true]);
  }

  public function markAllNotificationsAsRead(Request $request)
  {
    $request->user()
      ->unreadNotifications
    ->markAsRead();

    return response()->json(['success' => true]);
  }
}
