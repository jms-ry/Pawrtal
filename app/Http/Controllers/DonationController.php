<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\Donation;
use App\Notifications\DonationArchivedNotification;
use App\Notifications\DonationForceDeleteNotification;
use App\Notifications\DonationRestoredNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\PayMongoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
          $file = $images[$index];
          $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
          $imagePath = $file->storeAs('images/donation/donation_images', $filename, 'public');
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

      try {
        DB::transaction(function () use ($donation, $requestData) {
          $lockedDonation = Donation::lockForUpdate()->findOrFail($donation->id);

          $this->authorize('cancel', $lockedDonation);

          $lockedDonation->update($requestData);
        });
      } catch(AuthorizationException $e){
        return redirect()->back()->with('warning','This donation can no longer be cancelled because it has already been processed.');
      }

      return redirect()->back()->with('warning','Donation has been cancelled.');
    }

    if ($request->status === 'accepted') {
      $this->authorize('accept', $donation);

      try {
        DB::transaction(function () use ($donation, $requestData) {
          $lockedDonation = Donation::lockForUpdate()->findOrFail($donation->id);

          // Re-check policy on the freshly locked row
          $this->authorize('accept', $lockedDonation);

          $lockedDonation->update($requestData);
        });
      } catch (AuthorizationException $e) {
        return redirect()->back()->with('warning','This donation can no longer be accepted. It may have been cancelled or already processed.');
      }

      return redirect()->back()->with('success', 'Donation has been accepted.');
    }

    if ($request->status === 'rejected') {
      $this->authorize('reject', $donation);

      try {
        DB::transaction(function () use ($donation, $requestData) {
          $lockedDonation = Donation::lockForUpdate()->findOrFail($donation->id);

          // Re-check policy on the freshly locked row
          $this->authorize('reject', $lockedDonation);

          $lockedDonation->update($requestData);
        });
      } catch (AuthorizationException $e) {
        return redirect()->back()->with('warning','This donation can no longer be rejected. It may have been cancelled or already processed.');
      }

      return redirect()->back()->with('error', 'Donation has been rejected.');
    }

    $this->authorize('update', $donation);

    $newImagePath = null;
    $oldImagePath = null;

    try {
      DB::transaction(function () use ($donation, $request, &$requestData, &$newImagePath, &$oldImagePath) {
        $lockedDonation = Donation::lockForUpdate()->findOrFail($donation->id);

        $this->authorize('update', $lockedDonation);

        if ($request->hasFile('donation_image')) {
          $oldImagePath = $lockedDonation->donation_image;

          $file = $request->file('donation_image');
          $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
          $newImagePath = $file->storeAs('images/donation/donation_images', $filename, 'public');
          $requestData['donation_image'] = $newImagePath;
        } else {
          unset($requestData['donation_image']);
        }

        $lockedDonation->update($requestData);
      });
    } catch (AuthorizationException $e) {
      return redirect()->back()->with('warning','This donation can no longer be updated because it has already been processed.');
    } catch (\Throwable $e) {
      // Update failed after the new file may have been stored: remove the orphan.
      if ($newImagePath) {
        Storage::disk('public')->delete($newImagePath);
      }
      throw $e;
    }

    // Transaction committed: now it's safe to remove the replaced image.
    if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
      Storage::disk('public')->delete($oldImagePath);
    }

    return redirect()->back()->with('info', 'Donation has been updated.');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Donation $donation)
  {
    $this->authorize('delete', $donation);

    $archiveBy = Auth::user();

    if (in_array($donation->status, ['pending', 'cancelled'])) {
      return redirect()->back()->with('error', 'Only accepted or rejected donations can be archived.');
    }

    $donation->delete();

    $donation->user->notify(new DonationArchivedNotification($donation, $archiveBy));

    return redirect()->back()->with('warning', 'Donation has been archived!');
  }

  //Restore or unarchive a donation

  public function restore(Donation $donation)
  {
    $this->authorize('restore', $donation);

    $restoredBy = Auth::user();

    $donation->restore();
    
    $donation->user->notify(new DonationRestoredNotification($donation,$restoredBy));
    
    return redirect()->back()->with('success',  'Donation has been restored!');
  }

  public function forceDelete(Donation $donation)
  {
    $this->authorize('forceDelete', $donation);
    
    $donation->forceDelete();

    $donation->user->notify(new DonationForceDeleteNotification($donation));

    return redirect()->route('users.myDonations')->with('success', 'Donation permanently deleted successfully.');
  }

  /**
   * Create PayMongo payment source for monetary donation
   * 
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
  */
  public function createPayment(Request $request , PayMongoService $paymongoService)
  {
    // Validate request
    $validated = $request->validate([
      'amount' => 'required|numeric|min:1',
      'payment_method' => 'required|in:gcash',
    ]);

    $user = Auth::user();

    // Convert amount to centavos (₱100.00 = 10000 centavos)
    $amountInCentavos = (int) ($validated['amount'] * 100);

    try {
      DB::beginTransaction();

      
      $session = $paymongoService->createCheckoutSession(
        $amountInCentavos,
        'Donation to Ormoc Stray Oasis',
      );

      if (!$session) {
        throw new \Exception('Failed to create checkout session');
      }

      // Save donation record
      $donation = Donation::create([
        'user_id' => $user->id,
        'donation_type' => 'monetary',
        'amount' => $validated['amount'],
        'status' => 'pending',
        'payment_method' => $validated['payment_method'],
        'payment_intent_id' => $session['id'],
        'payment_status' => 'pending',
        'transaction_reference' => null,
        'paid_at' => null,
      ]);

      DB::commit();

      return response()->json([
        'success' => true,
        'donation_id' => $donation->id,
        'checkout_url' => $session['attributes']['checkout_url'],
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
