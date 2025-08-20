@extends('layouts.app')

@section('content')
  <div class="card mt-2 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
    <div class="card-body border-0 p-2 p-md-5 mx-auto">
      <div class="card-header border-0 bg-secondary">
        <h3 class="text-center fw-bolder display-6 font-monospace mb-4 mt-3">Meet Our Rescues!</h3>
        <div class="row g-3 g-md-5 mb-4 justify-content-end">
          <div class="col-12 col-md-6">
            <fieldset class="p-1">
              <legend class="fs-6 fw-bold mx-2 font-monospace" id="filter-legend">Filter by</legend>
              <div class="row g-2 mt-0">
                <div class="col-12 col-md-4">
                  <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                    <option selected hidden disabled>Sex</option>
                    <option>Male</option>
                    <option>Female</option>
                  </select>
                </div>
                <div class="col-12 col-md-4">
                  <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                    <option selected hidden disabled>Size</option>
                    <option>Small</option>
                    <option>Medium</option>
                    <option>Large</option>
                  </select>
                </div>
                <div class="col-12 col-md-4">
                  <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                    <option selected hidden disabled>Adoption Status</option>
                    <option>Available</option>
                    <option>Unavailable</option>
                    <option>Adopted</option>
                  </select>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-12 col-md-6 mt-3 mt-md-auto mt-0 d-flex flex-column justify-content-end" data-controller="rescue-switch">
            @include('modals.rescue.create-rescue-profile')
            <div class="form-check form-switch align-self-start align-self-md-end mb-1 mb-md-3 me-md-1 ms-2 ms-md-auto">
              <input class="form-check-input " type="checkbox" value="" id="rescueSwitch" switch data-rescue-switch-target="switch" data-action="rescue-switch#toggle">
              <label class="form-check-label mb-1 mb-md-0 ms-1 fw-bold font-monospace" for="rescueSwitch" id="switchLabel">Switch to create new rescue profile!</label>
            </div>
            <!-- Search input for larger screens -->
            <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1 align-self-end">
              <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search Rescues" class="form-control" data-rescue-switch-target="searchField">
            </div>
            <div class="d-flex justify-content-md-end justify-content-start">
              <button type="button" class="btn btn-primary fw-bold align-self-md-end align-self-start mt-auto mb-1 d-none" id="createRescueProfileButton" data-rescue-switch-target="createButton" data-bs-toggle="modal" data-bs-target="#createRescueProfileModal">Create Rescue Profile</button>
            </div>
            <!-- Search input for smaller screens -->
            <div class="input-group w-100 d-flex d-md-none px-1">
              <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search Rescues" class="form-control" data-rescue-switch-target="searchField">
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
        <div class="g-4 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 justify-content-center">
          @foreach($rescues as $rescue)
            <div class="col-12 col-md-3 rounded-4 border-primary-subtle bg-warning-subtle mx-2 px-1 mt-4 mt-md-5" data-aos="zoom-in-up" data-aos-delay="200">
              <div class="my-2">
                <span class="text-dark fw-bolder text-uppercase fs-4 mb-3 ms-2 mt-5 p-2 font-monospace">{{ $rescue->name }}</span>
                @if ($rescue->isAdopted())
                  <span class="badge border-0 position-absolute top-0 end-0 m-2 px-2 py-2 bg-warning bg-opacity-75 text-dark fw-bold rounded"><i class="bi bi-heart-fill"></i></span>
                @endif
              </div>
              <div class="p-2 mt-1 rescue-card border-0 rounded-4 overflow-hidden shadow-lg position-relative" style="height: 300px;">
                <img src="{{ $rescue->profile_image_url }}" alt="{{ $rescue->name }}" class="w-100 h-100 object-fit-cover rounded-4">
                <div class="position-absolute bottom-0 start-0 end-0 bg-warning-subtle bg-opacity-0 text-dark p-2 text-center">
                  <strong>{{ $rescue->tagLabel() }}</strong>
                </div>
                
              </div>
              <div class="row g-2 p-2 mt-1 mb-1">
                @if ($rescue->isAdopted() || $rescue->isUnavailable())
                  <div class="col-12 text-center mx-auto">
                    <a href="{{ url("/rescues/{$rescue->id}")}}" class="btn btn-sm btn-light w-50">View Profile</a>
                  </div>
                @else
                  <div class="col-12 text-center mx-auto d-flex gap-2 flex-row">
                    <a href="{{ url("/rescues/{$rescue->id}")}}" class="btn btn-sm btn-light w-100">View Profile</a>
                    <a href="{{ route('adoption-applications.create') }}" class="btn btn-sm btn-primary w-100 fw-bolder">Adopt Me!</a>
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
        <div class="d-flex justify-content-end mt-4 mt-md-5">
          <div class="btn-group" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-info"><span class="align-items-center"><i class="bi bi-chevron-double-left"></i> Prev</span></button>
            <button type="button" class="btn btn-info"><span class="align-items-center">Next <i class="bi bi-chevron-double-right"></i></span></button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection