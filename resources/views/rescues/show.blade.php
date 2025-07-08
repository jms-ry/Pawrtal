@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="card border-0 p-md-5">
      <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary">
        @if($backContext == 'rescues')
          <h4 class="fs-6 ms-md-5 mb-md-4 mb-2 mt-3 mt-md-0 me-5"><a href="{{ url('/rescues') }}" class="text-decoration-none text-danger"><i class="bi bi-chevron-left"></i><span class="ms-0">Back to Rescues</span></a></h4>
        @elseif($backContext === 'adoption')
          <h4 class="fs-6 ms-md-5 mb-md-4 mb-2 mt-3 mt-md-0 me-5"><a href="{{ url('/adoption') }}" class="text-decoration-none text-danger"><i class="bi bi-chevron-left"></i><span class="ms-0">Back to Adoption</span></a></h4>
        @endif
        <h3 class="fw-bolder display-6 font-monospace mb-2 mt-3 ms-md-5 ms-3">{{ $rescue->name }}</h3>
      </div>
      <div class="card-body border-0 px-md-5 mx-md-5">
        <div class="container px-md-5">
          <hr class="mt-0 mb-4">
          <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
            <div class="row g-3">
              <div class="col-12 col-md-6 mt-md-5 py-md-5 px-2 px-md-0">
                <div class="card border-0 rounded-4 overflow-hidden m-md-5 m-2 mt-md-5" style="height: 350px;">
                  <img src="{{ asset($rescue->profile_image) }}" alt="{{ $rescue->name }}" class="w-100 h-100 object-fit-cover rounded-4">
                </div>
              </div>
              <div class="col-12 col-md-6 mt-md-5">
                <div class="card border-0 mt-md-5 bg-warning-subtle ">
                  <div class="flex flex-row mb-md-3 mx-md-5 mx-2">
                    <div class="align-items-center bg-secondary p-md-2 mb-2 rounded-4">
                      <h3 class="text-center fw-bold display-6 font-monospace text-uppercase">{{ $rescue->name }}</h3>
                    </div>
                    <div class="bg-secondary p-4 p-md-2 mt-3 rounded-4">
                      <div class="flex flex-row align-items-start ms-md-3 ms-1 mt-2 mt-md-3 me-1 me-md-3 mb-3">
                        <p class="fs-5 lead">
                          <span class="fw-bold">Gender:</span>
                          <span> {{ $rescue->sex_formatted }}</span>
                        </p>
                        <p class="fs-5 lead">
                          <span class="fw-bold">Age:</span>
                          <span> {{ $rescue->age_formatted  }} </span>
                        </p>
                        <p class="fs-5 lead">
                          <span class="fw-bold">Color:</span>
                          <span> {{ $rescue->color_formatted }}</span>
                        </p>
                        <p class="fs-5 lead">
                          <span class="fw-bold">Description:</span>
                          <span> {{ $rescue->description_formatted }}</span>
                        </p>
                        <p class="fs-5 lead">
                          <span class="fw-bold">Size:</span>
                          <span> {{ $rescue->size_formatted }}</span>
                        </p>
                        <p class="fs-5 lead">
                          <span class="fw-bold">Distinctive Features:</span>
                          <span> {{ $rescue->distinctiveFeatures() }}</span>
                        </p>
                        <p class="fs-5 lead">
                          <span class="fw-bold">Vaccination Status:</span>
                          <span>{{ $rescue->vaccination_status_formatted }}</span>
                        </p>
                        <p class="fs-5 lead">
                          <span class="fw-bold">Spayed/Neutered:</span>
                          <span>{{ $rescue->spayed_neutered_formatted }}</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @if ($rescue->isAvailable())
              <div class="d-flex justify-content-center">
                <a href="{{ route('adoption-applications.create') }}" class="btn btn-lg btn-primary fw-bold mt-4 mt-md-0 mb-2 mb-md-0">Adopt Me!</a>
              </div>
            @elseif($rescue->isAdopted())
              <div class="d-flex justify-content-center mb-4">
                <p class="alert alert-primary  fs-6 fw-lighter mt-4 mt-md-0 mb-2 mb-md-0 font-monospace fst-italic text-center p-md-2 py-2 px-md-2" role="alert"><i class="bi bi-house-check-fill"></i> I'm already adopted!</p>
              </div>
            @else
              <div class="d-flex justify-content-center mb-4">
                <p class="alert alert-danger fs-6 fw-lighter mt-4 mt-md-0 mb-2 mb-md-0 font-monospace fst-italic text-center p-md-2 py-2 px-md-2" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> I'm not yet available for adoption!</p>
              </div>
            @endif
          </div>
        </div>
        <div class="container px-md-5 mb-5">
          <hr class="mt-3">
          <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
            <h3 class="text-center fw-bold font-monospace mt-3 mt-md-0 p-2 p-md-0">Gallery</h3>
            @if($notEmpty)
              <div class="row g-3 mt-3 justify-content-center">
                @foreach($randomImages as $image)
                  <div class="col-12 col-md-4 shadow-sm rounded-4 bg-warning-subtle">
                    <div class="ratio ratio-4x3 p-0 p-md-2 mt-1 rescue-gallery">
                      <img src="{{ asset($image) }}" alt="Gallery image" class="w-100 h-100 object-fit-cover rounded-4">
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="mt-1 mt-md-5">
                <div class="d-flex justify-content-center text-center mx-auto">
                  <p class="badge bg-warning-subtle fs-5 fw-lighter text-muted fst-italic font-monospace border-0 mb-5 mt-3 text-center mx-auto"> <i class="bi bi-image text-dark-subtle"></i> No Images Available...</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection