@extends('layouts.app')

@section('content')
  <div class="card mt-5 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
    <div class="card-body border-0 p-2 p-md-5 mx-auto">
      <div class="card-header border-0 bg-secondary">
        <h3 class="text-center fw-bolder display-6 font-monospace mb-5 mt-3">Adopt a Rescue Today!</h3>
        <div class="row g-3 g-md-5 mb-md-4 mb-4 justify-content-end">
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
              </div>
            </fieldset>
          </div>
          <div class="col-12 col-md-6 mt-3 mt-md-auto d-flex flex-column justify-content-end" data-controller="switch-search-button">
            <div class="form-check form-switch align-self-start align-self-md-end mb-2 mb-md-3 me-md-1 ms-2 ms-md-auto ">
              <input class="form-check-input " type="checkbox" value="" id="rescueSwitch" switch data-switch-search-button-target="switch" data-action="switch-search-button#toggleFields">
              <label class="form-check-label mb-1 mb-md-0 ms-1 fw-bold font-monospace" for="rescueSwitch" id="switchLabel">Switch to AI recommendation?</label>
            </div>
            <!-- Search input for larger screens -->
            <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1 align-self-end" id="rescueSearchField" >
              <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search..." class="form-control w-100" data-switch-search-button-target="searchField">
            </div>
            <div class="d-flex justify-content-md-end justify-content-start">
              <button type="button" class="btn btn-primary fw-bold align-self-md-end align-self-start mt-auto mb-1 d-none" id="matchRescueButton" data-switch-search-button-target="matchButton">Match Me a Rescue</button>
            </div>
            <!-- Search input for smaller screens -->
            <div class="input-group w-100 d-flex d-md-none px-1" id="rescueSearchField" >
              <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search..." class="form-control " data-switch-search-button-target="searchField">
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
        <div class="g-4 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 justify-content-center">
          @foreach($adoptables as $adoptable)
            <div class="col-12 col-md-3 rounded-4 border-primary-subtle bg-warning-subtle mx-2 px-1 mt-4 mt-md-5" data-aos="zoom-in-up" data-aos-delay="400">
              <span class="text-dark fw-bolder text-uppercase fs-4 mb-3 ms-2 mt-5 p-2 font-monospace">{{ $adoptable->name }}</span>
              <div class="p-2 mt-1" style="height: 300px;">
                <img src="{{ asset($adoptable->image) }}" alt="{{ $adoptable->name }}" class="w-100 h-100 object-fit-cover rounded-4">
              </div>
              <div class="row g-2 p-2">
                <div class="col-12 col-md-6 text-center mx-auto">
                  <a href="" class="btn btn-sm btn-info rounded-pill w-100 fw-bold">About Me</a>
                </div>
                <div class="col-12 col-md-6">
                  <a href="" class="btn btn-sm btn-success rounded-pill w-100 fw-bold">Adopt Me!</a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="d-flex justify-content-end mt-4 mt-md-5">
          <div class="btn-group" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-primary"><span class="align-items-center"><i class="bi bi-chevron-double-left"></i> Prev</span></button>
            <button type="button" class="btn btn-primary"><span class="align-items-center">Next <i class="bi bi-chevron-double-right"></i></span></button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection