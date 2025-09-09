<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRescueRequest;
use App\Http\Requests\UpdateRescueRequest;
use App\Models\Rescue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RescueController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    $rescues = Rescue::all();
    $user = Auth::user();

    return Inertia::render('Rescues/Index', [
      'rescues' => $rescues ,
      'user' => $user ? [
        'id' => $user->id,
        'full_name' => $user->fullName(),
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'canAdopt' => $user->canAdopt(),
      ] : null,
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
    
    return redirect()->route('rescues.index')->with('success', 'Rescue profile for '. $rescue->name. ' has been created!');
  }

  /**
    * Display the specified resource.
  */
  public function show(Rescue $rescue)
  {
    $randomImages = collect($rescue->images_url)->shuffle()->take(3);
    $notEmpty = $randomImages->isNotEmpty();
    $user = Auth::user();

    $previousUrl = url()->previous();
    $backContext = null;
    if (str_contains($previousUrl, '/rescues')) {
      $backContext = 'rescues';
    }elseif (str_contains($previousUrl, '/adoption')) {
      $backContext = 'adoption';
    }

    return Inertia::render('Rescues/Show',[
      'user' => $user ? [
        'id' => $user->id,
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'isNonAdminOrStaff' => $user->isNonAdminOrStaff(),
        'canAdopt' => $user->canAdopt(),
      ] : null,
      'backContext' => $backContext,
      'notEmpty' => $notEmpty,
      'rescue' => $rescue,
      'randomImages' => $randomImages,

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

    return redirect()->route('rescues.index')->with('warning', 'Rescue profile for '. $rescue->name. ' has been deleted!');
  }
}
