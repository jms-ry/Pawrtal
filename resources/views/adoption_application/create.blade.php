@extends('layouts.app')

@section('content')
  <div class="container-fluid" data-controller="adoption-application">
    <div class="card border-0 p-md-5">
      <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary">
        @if($backContext == 'rescues')
          <h4 class="fs-6 ms-md-5 mb-md-4 mb-2 mt-3 mt-md-0 me-5"><a href="{{ url('/rescues') }}" class="text-decoration-none text-danger"><i class="bi bi-chevron-left"></i><span class="ms-0">Back to Rescues</span></a></h4>
        @elseif($backContext === 'adoption')
          <h4 class="fs-6 ms-md-5 mb-md-4 mb-2 mt-3 mt-md-0 me-5"><a href="{{ url('/adoption') }}" class="text-decoration-none text-danger"><i class="bi bi-chevron-left"></i><span class="ms-0">Back to Adoption</span></a></h4>
        @endif
        <h3 class="fw-bolder fs-1 font-monospace mb-2 mt-4 ms-md-5 ms-3 text-center">Adoption Application for <strong class="text-decoration-underline">Rescue Name</strong> </h3>
      </div>
      <div class="card-body border-0 px-md-5 mx-md-5">
        <div class="container px-md-5">
          <hr class="mt-0 mb-4">
          <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
            <div class="container-fluid justify-content-center">
              <form action="">
                <div class="align-items-center mb-1">
                  <label for="reasonForAdoptionTextInput" class="form-label font-monospace">Reason for Adoption:</label>
                  <textarea name="reasonForAdoptionTextInput" id="reasonForAdoptionTextInput" class="form-control"></textarea>
                </div>
                <div class="row g-2 mt-4 mb-2">
                  <div class="col-12 col-md-6">
                    <label for="preferredInspectionStartDate" class="form-label font-monospace">Preffered Inspection Start Date:</label>
                    <input type="date" name="prefferedInspectionStartDate" id="prefferedInspectionStartDate" class="form-control">
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="preferredInspectionEndDate" class="form-label font-monospace">Preffered Inspection End Date:</label>
                    <input type="date" name="prefferedInspectionEndDate" id="prefferedInspectionEndDate" class="form-control">
                  </div>
                </div>
                <div class="row g-2 mt-4 mb-2">
                  <div class="col-12 col-md-6">
                  <label for="validId" class="form-label font-monospace">Valid Id:</label>
                    <input type="file" name="validId" id="validId" class="form-control">
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="supportingDocuments" class="form-label font-monospace">Supporting Documents:</label>
                    <input type="file" name="supportingDocuments" id="supportingDocuments" class="form-control" multiple>
                  </div>
                </div>
                <div class="d-flex justify-content-center mt-5">
                  <button type="button" class="btn btn-lg btn-primary fw-bold">Submit Application</button>
                </div>
              </form>
            </div>
          </div>
          <hr class="mt-4 mb-4">
        </div>
      </div>
    </div>
  </div>
@endsection