<?php

namespace App\Http\Controllers;

use App\Models\Rescue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RescueController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    $rescues = Rescue::all();
    $user = Auth::user();
    return view('rescues.index', compact('rescues','user'));
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
    
    return redirect()->back()->with('success', 'Rescue profile for '. $rescue->name. ' created successfully!');
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
    
    return view('rescues.show', compact('rescue','randomImages','backContext','notEmpty','user'));
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
    $rescue = Rescue::find($rescue->id);
    $requestData = $request->all();

    if($request->hasFile('profile_image') && ($rescue->profile_image)){
      Storage::delete($rescue->profile_image);
    }
    
    if ($request->hasFile('profile_image')) {
      $profileImagePath = $request->file('profile_image')->store('images/rescues/profile_images', 'public');

      $requestData['profile_image'] = $profileImagePath;
    }

    if($request->hasFile('images')) {
      $existingImages = $rescue->images ?? [];
      $images = [];
      foreach ($request->file('images') as $image) {
        $imagePath = $image->store('images/rescues/gallery_images', 'public');
        $images[] = $imagePath;
      }
      $requestData['images'] = array_merge($existingImages, $images);
    } else {
      $requestData['images'] = $report->images ?? [];
    }

    $rescue-> update($requestData);

    return redirect()->back()->with('success','Rescue Profile for '. $rescue->name. ' has been updated successfully!');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Rescue $rescue)
  {
    $rescue = Rescue::find($rescue->id);
    $rescue->delete();

    return redirect()->route('rescues.index')->with('success', 'Rescue profile for '. $rescue->name. ' has been deleted successfully!');
  }
}
