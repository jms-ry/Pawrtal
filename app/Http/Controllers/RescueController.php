<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRescueRequest;
use App\Http\Requests\UpdateRescueRequest;
use App\Models\Rescue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RescueController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index(Request $request)
  {
    $search = $request->get('search');
    $sexFilter = $request->get('sex');
    $sizeFilter = $request->get('size');
    $statusFilter = $request->get('status');
    
    $user = Auth::user();
    $user = $user?->load('address', 'household');

    $rescues = Rescue::query()
      ->visibleTo($user)
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

    

    return Inertia::render('Rescues/Index', [
      'rescues' => $rescues,
      'user' => $user ? [
        'id' => $user->id,
        'full_name' => $user->fullName(),
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'canAdopt' => $user->canAdopt(),
        'address' => $user->address,
        'household' => $user->household,
      ] : null,
      'filters' => [
        'search' => $search,
        'sex' => $sexFilter,
        'size' => $sizeFilter,
        'status' => $statusFilter,
      ],
    ]);
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
  public function store(StoreRescueRequest $request)
  {
    $requestData = $request->validated();

    if ($request->hasFile('profile_image')) {
      $profileImagePath = $request->file('profile_image')->store('images/rescues/profile_images', 'public');

      $requestData['profile_image'] = $profileImagePath;
    }

    if($request->hasFile('images')) {
      $images = [];
      foreach ($request->file('images') as $image) {
        $imagePath = $image->store('images/rescues/gallery_images', 'public');
        $images[] = $imagePath;
      }
      $requestData['images'] = $images;
    } else {
      $requestData['images'] = [];
    }

    $rescue = Rescue::create($requestData);
    
    return redirect()->back()->with('success', 'Rescue profile for '. $rescue->name. ' has been created!');
  }

  /**
    * Display the specified resource.
  */
  public function show(Rescue $rescue)
  { 
    $user = Auth::user();
    
    if ($rescue->trashed() && !$user?->isAdminOrStaff()) {
      abort(404);
    }
    $randomImages = collect($rescue->images_url)->shuffle()->take(3);
    $notEmpty = $randomImages->isNotEmpty();
    
    $previousUrl = url()->previous();
    $backContext = null;
    $path = parse_url($previousUrl, PHP_URL_PATH);
    $segments = collect(explode('/', trim($path, '/')));
    $urlText = '';
    
    if ($segments->isNotEmpty()) {
      $urlText = is_numeric($segments->last()) ? '' : "to " . ucfirst($segments->last());
    }
    $user = $user?->load('address', 'household');
    $rescue->loadCount('adoptionApplications');
    
    return Inertia::render('Rescues/Show',[
      'user' => $user ? [
        'id' => $user->id,
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'isNonAdminOrStaff' => $user->isNonAdminOrStaff(),
        'canAdopt' => $user->canAdopt(),
        'address' => $user->address,
        'household' => $user->household,
      ] : null,
      'backContext' => $backContext,
      'notEmpty' => $notEmpty,
      'rescue' => $rescue,
      'randomImages' => $randomImages,
      'previousUrl' => $previousUrl,
      'urlText' => $urlText,

    ]);
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(Rescue $rescue)
  {
    //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(UpdateRescueRequest $request, Rescue $rescue)
  { 
    $requestData = $request->all();

    if($request->hasFile('profile_image')){
      if($rescue->profile_image){
        Storage::delete($rescue->profile_image);
      }

      $profileImagePath = $request->file('profile_image')->store('images/rescues/profile_images', 'public');
      $requestData['profile_image'] = $profileImagePath;
    }else{
      unset($requestData['profile_image']);
    }

    if($request->hasFile('images')) {
      $existingImages = $rescue->images ?? [];
      $images = [];
      foreach ($request->file('images') as $image) {
        $imagePath = $image->store('images/rescues/gallery_images', 'public');
        $images[] = $imagePath;
      }
      $requestData['images'] = array_merge($existingImages, $images);
    }else{
      unset($requestData['images']);
    }
    
    $rescue-> update($requestData);

    return redirect()->route('rescues.show',$rescue->id)->with('info','Rescue Profile for '. $rescue->name. ' has been updated!');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Rescue $rescue)
  {
    $rescue = Rescue::find($rescue->id);
    $rescue->delete();

    return redirect()->back()->with('warning', 'Rescue profile for '. $rescue->name. ' has been archived!');
  }
}
