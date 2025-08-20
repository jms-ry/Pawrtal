<?php

namespace App\Http\Controllers;

use App\Models\Rescue;
use Illuminate\Http\Request;

class RescueController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    $rescues = Rescue::all();
    return view('rescues.index', compact('rescues'));
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
    $requestData = $request->all();

    if ($request->hasFile('profile_image')) {
      $profileImagePath = $request->file('profile_image')->store('images/rescues/profile_images', 'public');

      $actualProfileImagePath = str_replace('public/', '', $profileImagePath);

      $requestData['profile_image'] = $actualProfileImagePath;
    }

    if($request->hasFile('images')) {
      $images = [];
      foreach ($request->file('images') as $image) {
        $imagePath = $image->store('images/rescues/gallery_images', 'public');
        $actualImagePath = str_replace('public/', '', $imagePath);
        $images[] = $actualImagePath;
      }
      $requestData['images'] = $images;
    } else {
      $requestData['images'] = [];
    }

    $rescue = Rescue::create($requestData);
    
    return redirect()->back()->with('success', 'Rescue profile for '. $rescue->name. ' created successfully!');
  }

  /**
    * Display the specified resource.
  */
  public function show(Rescue $rescue)
  {
    $randomImages = collect($rescue->images_url)->shuffle()->take(3);
    $notEmpty = $randomImages->isNotEmpty();

    $previousUrl = url()->previous();
    $backContext = null;
    if (str_contains($previousUrl, '/rescues')) {
      $backContext = 'rescues';
    }elseif (str_contains($previousUrl, '/adoption')) {
      $backContext = 'adoption';
    }
    
    return view('rescues.show', compact('rescue','randomImages','backContext','notEmpty'));
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
    public function update(Request $request, Rescue $rescue)
  {
    //
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Rescue $rescue)
  {
    //
  }
}
