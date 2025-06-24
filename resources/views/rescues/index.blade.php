@extends('layouts.app')

@section('content')
  <div class="card mt-5 mt-md-3 mb-4 mb-md-2 border-0">
    <div class="card-body border-0 p-2 p-md-5 mx-auto">
      <div class="card-header border-0 bg-secondary">
        <h3 class="text-center fw-bolder display-6 font-monospace mb-5 mt-3">Meet Our Rescues!</h3>
        <div class="row g-3 g-md-5 mb-4 justify-content-end">
          <div class="col-12 col-md-6">
            <span class="fw-bold mb-3">Filter by</span>
            <div class="row g-2 mt-1">
              <div class="col-12 col-md-4">
                <select class="form-select" aria-label="filter-select">
                  <option selected hidden disabled>Sex</option>
                  <option>Male</option>
                  <option>Female</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <select class="form-select" aria-label="filter-select">
                  <option selected>Size</option>
                  <option>Small</option>
                  <option>Medium</option>
                  <option>Large</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <select class="form-select" aria-label="filter-select">
                  <option selected>Adoption Status</option>
                  <option>Available</option>
                  <option>Unavailable</option>
                  <option>Adopted</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 d-flex justify-content-end">
            <!-- Search input for larger screens -->
            <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1">
              <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search Rescues" class="form-control">
              <span class="input-group-text no-start-border"><i class="bi bi-search"></i></span>
            </div>
            <!-- Search input for smaller screens -->
            <div class="input-group w-100 d-flex d-md-none">
              <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search Rescues" class="form-control ">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
        <div class="row g-3 mt-4 mx-auto">
          @foreach($rescues as $rescue)
            <div class="col-12 col-md-2" data-aos="zoom-in-up" data-aos-delay="400">
              <div class="rescue-card border-0 rounded-4 overflow-hidden shadow-lg position-relative" style="height: 250px;">
                <img src="{{ asset($rescue->image) }}" alt="{{ $rescue->name }}" class="w-100 h-100 object-fit-cover">
                <span class="position-absolute top-0 start-0 m-2 px-2 py-1 bg-light bg-opacity-75 text-dark fw-bold rounded">
                  {{ $rescue->name }}
                </span>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
@endsection