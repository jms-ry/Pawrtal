<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAdoptionApplicationRequest;
use Illuminate\Http\Request;
use App\Models\AdoptionApplication;
use Illuminate\Support\Facades\Storage;
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
  public function store(Request $request)
  {
    $requestData = $request->all();

    if($request->hasFile('valid_id')){
      $validIdPath = $request->file('valid_id')->store('images/adoption_applications/valid_ids','public');

      $requestData['valid_id'] = $validIdPath;
    }

    if($request->hasFile('supporting_documents')){
      $supporting_documents = [];

      foreach ($request->file('supporting_documents') as $supporting_document){
        $supportingDocumentPath = $supporting_document->store('images/adoption_applications/supporting_documents','public');
        $supporting_documents [] = $supportingDocumentPath;
      }
      $requestData['supporting_documents'] = $supporting_documents;
    }else{
      $requestData['supporting_documents'] = [];
    }

    $adoption_application = AdoptionApplication::create($requestData);

    return redirect()->route('users.myAdoptionApplications')->with('success', 'Adoption application for '. $adoption_application->rescue->name. ' was submitted!');
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
      $adoptionApplication->update($requestData);
      return redirect()->back()->with('warning','Adoption application for '. $adoptionApplication->rescue->name. ' has been cancelled.');
    }

    if($request->hasFile('valid_id')){
      if($adoptionApplication->valid_id){
        Storage::delete($adoptionApplication->valid_id);
      }

      $validIdPath = $request->file('valid_id')->store('images/adoption_applications/valid_ids', 'public');
      $requestData['valid_id'] = $validIdPath;
    }else{
      unset($requestData['valid_id']);
    }

    if($request->hasFile('supporting_documents')) {
      $existingDocuments = $adoptionApplication->supporting_documents ?? [];
      $documents = [];
      foreach ($request->file('supporting_documents') as $document) {
        $documentPath = $document->store('images/adoption_applications/supporting_documents', 'public');
        $documents[] = $documentPath;
      }
      $requestData['supporting_documents'] = array_merge($existingDocuments, $documents);
    }else{
      unset($requestData['supporting_documents']);
    }
    
    $adoptionApplication-> update($requestData);
    
    return redirect()->back()->with('info','Adoption application for '. $adoptionApplication->rescue->name. ' has been updated.');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(AdoptionApplication $adoptionApplication)
  {
    $adoptionApplication->delete();

    return redirect()->back()->with('warning','Adoption application for '. $adoptionApplication->rescue->name. ' has been archived.');
  }

  public function restore(AdoptionApplication $adoptionApplication)
  {
    $adoptionApplication->restore();

    return redirect()->back()->with('success','Adoption application for '. $adoptionApplication->rescue->name. ' has been restored.');
  }
}
