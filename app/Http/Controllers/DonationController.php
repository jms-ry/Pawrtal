<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
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
    return view('donation.create');
  }

  /**
    * Store a newly created resource in storage.
  */
  public function store(StoreDonationRequest $request)
  {
    $userId = $request->input('user_id');
    $donationType = $request->input('donation_type');
    $status = $request->input('status');

    // If it's in-kind donation with multiple items
    if ($donationType === 'in-kind') {
      $descriptions = $request->item_description;
      $quantities   = $request->item_quantity;
      $locations    = $request->pick_up_location;
      $contacts     = $request->contact_person;
      $images       = $request->file('donation_image');

      foreach ($descriptions as $index => $desc) {
        $imagePath = null;
        if (isset($images[$index])) {
          $imagePath = $images[$index]->store('images/donation/donation_images', 'public');
        }

        Donation::create([
          'user_id'          => $userId,
          'donation_type'    => $donationType,
          'status'           => $status,
          'item_description' => $desc,
          'item_quantity'    => $quantities[$index] ?? 1,
          'pick_up_location' => $locations[$index] ?? null,
          'contact_person'   => $contacts[$index] ?? null,
          'donation_image'   => $imagePath,
        ]);
      }
    } else {
      // monetary donation (single record for now)
      Donation::create($request->all());
    }

    return redirect()->back()->with('success', ucfirst($donationType).' Donation created successfully!');
  }


  /**
    * Display the specified resource.
  */
  public function show(string $id)
  {
    //
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(string $id)
  {
    //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(string $id)
  {
    //
  }
}
