<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    $createdCount = 0;

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

        $createdCount++;
      }
    } else {
      Donation::create($request->all());
       $createdCount = 1;
    }
    $message = $createdCount > 1 ? ucfirst($donationType)." Donations ($createdCount items) have been submitted!" : ucfirst($donationType)." Donation has been submitted!";

    return redirect()->route('users.myDonations')->with('success', $message);
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
  public function update(UpdateDonationRequest $request, Donation $donation)
  {
    $requestData = $request->all();

    //if the request status is "cancelled", update the status to "cancelled"
    if($request->status === 'cancelled'){
      $donation->update($requestData);
      return redirect()->back()->with('warning','Donation has been cancelled.');
    }

    if($request->status === 'archived'){
      $donation->update($requestData);
      return redirect()->back()->with('warning','Donation has been archived.');
    }

    if($request->status === 'accepted'){
      $donation->update($requestData);
      return redirect()->back()->with('success','Donation has been accepted.');
    }

    if($request->status === 'rejected'){
      $donation->update($requestData);
      return redirect()->back()->with('error','Donation has been rejected.');
    }

    if ($request->hasFile('donation_image')) {
      if($donation->donation_image){
        Storage::delete($donation->donation_image);
      }
      $imagePath = $request->file('donation_image')->store('images/donation/donation_images', 'public');
      $requestData['donation_image'] = $imagePath;
    }else{
      unset($requestData['donation_image']);
    }

    $donation->update($requestData);

    return redirect()->back()->with('info','Donation has been updated.');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(string $id)
  {
    //
  }
}
