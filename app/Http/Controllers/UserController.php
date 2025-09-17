<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
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
      $previousUrl = url()->previous();
      $ableToBack = str_contains($previousUrl,'/users/') ? false : true;
      
      $path = parse_url($previousUrl, PHP_URL_PATH);
      $segments = collect(explode('/', trim($path, '/')));
      $urlText = '';
    
      if ($segments->isNotEmpty()) {
        $urlText = is_numeric($segments->last()) ? '' : "to " . ucfirst($segments->last());
      }

      $user->load(['address', 'household']);
      
      return Inertia::render('User/Show',[
        'user'=>$user,
        'previousUrl'=>$previousUrl,
        'urlText'=>$urlText,
        'ableToBack' => $ableToBack,
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
    $user = Auth::user();
    $search = $request->get('search');
    $typeFilter = $request->get('type');;
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;

    $reports = $user->reports()
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
      ->paginate(5)
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
    ]);
  }

  public function myDonations()
  {

    return Inertia::render('User/MyDonations');
  }

  public function myAdoptionApplications()
  {

    return Inertia::render('User/MyAdoptionApp');
  }
}
