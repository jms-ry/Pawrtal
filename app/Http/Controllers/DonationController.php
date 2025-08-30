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
    $requestData = $request->all();

    if($request->hasFile('donation_image')){
      $donationImagePath = $request->file('donation_image')->store('images/donation/donation_images','public');

      $requestData['donation_image'] = $donationImagePath;
    }

    $donation = Donation::create($requestData);

    return redirect()->back()->with('success',$donation->getDonationTypeFormatted(). ' Donation created successfully!');
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
