<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdoptionApplicationRequest;
use App\Http\Requests\UpdateAdoptionApplicationRequest;
use App\Notifications\AdoptionApplicationRestoredNotification;
use Illuminate\Http\Request;
use App\Models\AdoptionApplication;
use App\Notifications\AdoptionApplicationArchivedNotification;
use App\Notifications\AdoptionApplicationForceDeleteNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Rescue;
class AdoptionApplicationController extends Controller
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
  public function create(Request $request)
  {

  }

  /**
    * Store a newly created resource in storage.
  */
  public function store(StoreAdoptionApplicationRequest $request)
  {
    $this->authorize('create', AdoptionApplication::class);
    
    $requestData = $request->validated();

    if ($request->hasFile('valid_id')) {
      $file = $request->file('valid_id');
      $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
      $validIdPath = $file->storeAs('images/adoption_applications/valid_ids', $filename, 'public');
      $requestData['valid_id'] = $validIdPath;
    }

    if ($request->hasFile('supporting_documents')) {
      $supporting_documents = [];

      foreach ($request->file('supporting_documents') as $supporting_document) {
        $filename = Str::uuid() . '.' . $supporting_document->getClientOriginalExtension();
        $supportingDocumentPath = $supporting_document->storeAs(
          'images/adoption_applications/supporting_documents',
          $filename,
          'public'
        );
        $supporting_documents[] = $supportingDocumentPath;
      }
      $requestData['supporting_documents'] = $supporting_documents;
    } else {
      $requestData['supporting_documents'] = [];
    }

    $adoption_application = AdoptionApplication::create($requestData);

    return redirect()->route('users.myAdoptionApplications')->with('success', 'Adoption application for ' . $adoption_application->rescue->name . ' was submitted!');
  }

  /**
    * Display the specified resource.
  */
  public function show(AdoptionApplication $adoptionApplication)
  {
    //
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(AdoptionApplication $adoptionApplication)
  {
    //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(UpdateAdoptionApplicationRequest $request, AdoptionApplication $adoptionApplication)
  {
    $requestData = $request->validated();

    //if the request status is "cancelled", update the status to "cancelled"
    if($request->status === 'cancelled'){
      $this->authorize('cancel',$adoptionApplication);
      $adoptionApplication->update($requestData);
      return redirect()->back()->with('warning','Adoption application for '. $adoptionApplication->rescue->name. ' has been cancelled.');
    }
    //approve adoption applicaiton
    if ($request->status === 'approved') {
      $this->authorize('approve', $adoptionApplication);

      $alreadyProcessed = false;

      DB::transaction(function () use ($adoptionApplication, $requestData, &$alreadyProcessed) {
        $lockedApplication = AdoptionApplication::lockForUpdate()->find($adoptionApplication->id);

        // Re-check policy condition on freshly locked row
        if ($lockedApplication->status !== 'under_review') {
          $alreadyProcessed = true;
          return;
        }

        // Also lock the rescue to prevent double-adoption
        $rescue = Rescue::lockForUpdate()->find($lockedApplication->rescue_id);

        if ($rescue && $rescue->adoption_status === 'adopted') {
          $alreadyProcessed = true;
          return;
        }

        $lockedApplication->update($requestData);

        if ($lockedApplication->rescue_id) {
          $rescue->update(['adoption_status' => 'adopted']);
        }

        AdoptionApplication::where('rescue_id', $lockedApplication->rescue_id)
          ->where('id', '!=', $lockedApplication->id)
          ->whereIn('status', ['pending', 'under_review'])
          ->each(function ($otherApp) use ($lockedApplication) {
            $otherApp->update([
              'status' => 'rejected',
              'review_notes' => "Automatically rejected because another applicant was approved for {$lockedApplication->rescue->name}.",
              'reviewed_by' => 'System',
              'review_date' => now(),
            ]);
          });
      });

      if ($alreadyProcessed) {
        return redirect()->back()->with('error', 'This application has already been processed.');
      }

      return redirect()->back()->with('success', 'Adoption application for ' . $adoptionApplication->rescue->name . ' has been approved.');
    }

    //reject adoption application 
    if ($request->status === 'rejected') {
      $this->authorize('reject', $adoptionApplication);

      DB::transaction(function () use ($adoptionApplication, $requestData) {
        $lockedApplication = AdoptionApplication::lockForUpdate()->find($adoptionApplication->id);

        if (!in_array($lockedApplication->status, ['pending', 'under_review'])) {
          abort(403);
        }

        $lockedApplication->update($requestData);
      });

      return redirect()->back()->with('error', 'Adoption application for ' . $adoptionApplication->rescue->name . ' has been rejected.');
    }

    $this->authorize('update', $adoptionApplication);

    if ($request->hasFile('valid_id')) {
      if ($adoptionApplication->valid_id && Storage::disk('public')->exists($adoptionApplication->valid_id)) {
        Storage::disk('public')->delete($adoptionApplication->valid_id);
      }

      $file = $request->file('valid_id');
      $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
      $validIdPath = $file->storeAs('images/adoption_applications/valid_ids', $filename, 'public');
      $requestData['valid_id'] = $validIdPath;
    } else {
      unset($requestData['valid_id']);
    }

    if ($request->hasFile('supporting_documents')) {
      $existingDocuments = $adoptionApplication->supporting_documents ?? [];
      $documents = [];

      foreach ($request->file('supporting_documents') as $document) {
        $filename = Str::uuid() . '.' . $document->getClientOriginalExtension();
        $documentPath = $document->storeAs(
          'images/adoption_applications/supporting_documents',
          $filename,
          'public'
        );
        $documents[] = $documentPath;
      }

      $requestData['supporting_documents'] = array_merge($existingDocuments, $documents);
    } else {
      unset($requestData['supporting_documents']);
    }

    $adoptionApplication->update($requestData);

    return redirect()->back()->with('info', 'Adoption application for ' . $adoptionApplication->rescue->name . ' has been updated.');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(AdoptionApplication $adoptionApplication)
  {
    $this->authorize('delete', $adoptionApplication);

    $archiveBy = Auth::user();

    $adoptionApplication->delete();

    $adoptionApplication->user->notify(new AdoptionApplicationArchivedNotification($adoptionApplication,$archiveBy));

    return redirect()->back()->with('warning','Adoption application for '. $adoptionApplication->rescue->name. ' has been archived.');
  }

  public function restore(AdoptionApplication $adoptionApplication)
  {
    $this->authorize('restore', $adoptionApplication);

    $restoredBy = Auth::user();

    $adoptionApplication->restore();

    $adoptionApplication->user->notify(new AdoptionApplicationRestoredNotification($adoptionApplication,$restoredBy));
    
    return redirect()->back()->with('success','Adoption application for '. $adoptionApplication->rescue->name. ' has been restored.');
  }

  public function forceDelete(AdoptionApplication $adoptionApplication)
  {
    $this->authorize('forceDelete',$adoptionApplication);

    $adoptionApplication->forceDelete();

    $adoptionApplication->user->notify(new AdoptionApplicationForceDeleteNotification($adoptionApplication));

    return redirect()->route('users.myAdoptionApplications')->with('success', 'Adoption application permanently deleted successfully.');
  }
}
