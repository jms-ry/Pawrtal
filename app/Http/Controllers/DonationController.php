<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\PayMongoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
      /** Do monetary donation creation here! */
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
    $requestData = $request->validated();

    //if the request status is "cancelled", update the status to "cancelled"
    if($request->status === 'cancelled'){
      $this->authorize('cancel', $donation);
      $donation->update($requestData);
      return redirect()->back()->with('warning','Donation has been cancelled.');
    }

    if($request->status === 'accepted'){
      $this->authorize('accept', $donation);
      $donation->update($requestData);
      return redirect()->back()->with('success','Donation has been accepted.');
    }

    if($request->status === 'rejected'){
      $this->authorize('reject', $donation);
      $donation->update($requestData);
      return redirect()->back()->with('error','Donation has been rejected.');
    }

    $this->authorize('update', $donation);

    if ($request->hasFile('donation_image')) {
      if($donation->donation_image && Storage::disk('public')->exists($donation->donation_image)){
        Storage::disk('public')->delete($donation->donation_image);
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
  public function destroy(Donation $donation)
  {
    $this->authorize('delete', $donation);
    
    if ($donation->status === 'pending') {
      return redirect()->back()->with('error', 'Pending donations cannot be deleted.');
    }

    $donation->delete();

    return redirect()->back()->with('warning', 'Donation has been archived!');
  }

  //Restore or unarchive a donation

  public function restore(Donation $donation)
  {
    $this->authorize('restore', $donation);

    $donation->restore();
    
    return redirect()->back()->with('success',  'Donation has been restored!');
  }

  /**
   * Create PayMongo payment source for monetary donation
   * 
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
  */
  public function createPayment(Request $request)
  {
    // Validate request
    $validated = $request->validate([
      'amount' => 'required|numeric|min:1',
      'payment_method' => 'required|in:gcash,maya,card',
    ]);

    $user = Auth::user();

    // Convert amount to centavos (₱100.00 = 10000 centavos)
    $amountInCentavos = (int) ($validated['amount'] * 100);

    // Prepare billing information
    $billing = [
      'name' => trim($user->first_name . ' ' . $user->last_name),
      'email' => $user->email,
      'phone' => $user->contact_number ?? '09000000000',
    ];

    try {
      DB::beginTransaction();

      // Create PayMongo source
      $paymongoService = new PayMongoService();
      $source = $paymongoService->createSource(
        $amountInCentavos,
        $validated['payment_method'], // 'gcash' or 'maya'
        'Donation to Pawrtal',
        $billing
      );

      if (!$source) {
        throw new \Exception('Failed to create payment source');
      }

      // Save donation record
      $donation = Donation::create([
        'user_id' => $user->id,
        'donation_type' => 'monetary',
        'amount' => $validated['amount'],
        'status' => 'pending',
        'payment_method' => $validated['payment_method'],
        'payment_intent_id' => $source['id'],
        'payment_status' => 'pending',
        'transaction_reference' => null,
        'paid_at' => null,
      ]);

      DB::commit();

      return response()->json([
        'success' => true,
        'donation_id' => $donation->id,
        'checkout_url' => $source['attributes']['redirect']['checkout_url'],
      ]);

    } catch (\Exception $e) {
      DB::rollBack();
          
      Log::error('Payment creation failed', [
        'user_id' => $user->id,
        'amount' => $validated['amount'],
        'payment_method' => $validated['payment_method'],
        'error' => $e->getMessage(),
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Failed to create payment. Please try again.',
      ], 500);
    }
  }
}
