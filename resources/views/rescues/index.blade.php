@extends('layouts.app')

@section('content')
  <div class="card mt-5 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
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
                  <option selected hidden disabled>Size</option>
                  <option>Small</option>
                  <option>Medium</option>
                  <option>Large</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <select class="form-select" aria-label="filter-select">
                  <option selected hidden disabled>Adoption Status</option>
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
        <div class="g-4 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 justify-content-center">
          @foreach($rescues as $rescue)
            <div class="col-12 col-md-3 rounded-4 border-primary-subtle bg-warning-subtle mx-2 px-1 mt-4 mt-md-5" data-aos="zoom-in-up" data-aos-delay="400">
              <span class="text-dark fw-bold text-uppercase fs-4 mb-3 ms-2 mt-5 p-2 font-monospace">{{ $rescue->name }}</span>
              <div class="p-2 mt-1" style="height: 300px;">
                <img src="{{ asset($rescue->image) }}" alt="{{ $rescue->name }}" class="w-100 h-100 object-fit-cover rounded-4">
                @if ($rescue->adoption_status === 'adopted')
                  <span class="badge border-0 position-absolute top-0 end-0 m-2 px-2 py-1 bg-warning bg-opacity-75 text-dark fw-bold rounded"><i class="bi bi-heart-fill"></i></span>
                @endif
              </div>
              <div class="row g-2 p-2">
                @if ($rescue->adoption_status === 'adopted' || $rescue->adoption_status === 'unavailable')
                  <div class="col-12 text-center mx-auto">
                    <a href="" class="btn btn-sm btn-info rounded-pill w-50">About Me</a>
                  </div>
                @else
                  <div class="col-12 col-md-6 text-center mx-auto">
                    <a href="" class="btn btn-sm btn-info rounded-pill w-100">About Me</a>
                  </div>
                  <div class="col-12 col-md-6">
                    <a href="" class="btn btn-sm btn-primary rounded-pill w-100">Adopt Me!</a>
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
@endsection