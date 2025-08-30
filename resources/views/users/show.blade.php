@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="card border-0 p-md-5" data-controller="delete-address-household">
      <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary">
        <h5 class="fw-bolder fs-3 font-monospace mb-2 mt-4 ms-md-5 ms-3"><span class="ms-md-5"></span><span class="ms-md-5"></span><strong class="text-start ms-md-5">Personal Information</strong> </h5>
      </div>
      <div class="card-body border-0 px-md-5 mx-md-5">
        <div class="container px-md-5">
          <hr class="mt-0 mb-4">
          <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
            <div class="container-fluid justify-content-center">
              <form action="{{ route('users.update', $user?->id) }}" method="POST" class="">
                @csrf
                @method('PUT')
                <div class="card bg-warning-subtle border-0 p-3 p-md-5">
                  <div class="row g-2">
                    <div class="col-12 col-md-6 form-floating">
                      <input type="text" name="first_name" class="form-control" placeholder="First name" aria-label="First name" id="floating_first_name" value="{{ $user?->first_name }}">
                      <label for="floating_first_name" class="form-label">First Name</label>
                    </div>
                    <div class="col-12 col-md-6 form-floating">
                      <input type="text" name="last_name" class="form-control" placeholder="Last name" aria-label="Last name" id="floating_last_name" value="{{ $user?->last_name }}">
                      <label for="floating_last_name" class="form-label">Last Name</label>
                    </div>
                  </div>
                  <div class="row g-2 mt-2">
                    <div class="col-12 col-md-6 form-floating">
                      <input type="tel" id="floating_contact_number" name="contact_number" class="form-control" placeholder="Contact Number" aria-label="Contact Number" value="{{ $user?->contact_number }}">
                      <label for="floating_contact_number" class="form-label">Contact Number</label>
                    </div>
                    <div class="col-12 col-md-6 form-floating">
                      <input type="email" id="floating_email" name="email" class="form-control" placeholder="Email" aria-label="Email" value="{{ $user?->email }}">
                      <label for="floating_email" class="form-label">Email</label>
                    </div>
                  </div>
                </div>
                <div class="card-footer border-0 bg-warning-subtle">
                  <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
                    <button type="submit" class="btn btn-success fw-bolder">Update Information</button>
                  </div>
                  <div class="d-md-none">
                    <button type="submit" class="btn btn-success w-100 fw-bolder">Update Information</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      @include('modals.users/delete-address-modal')
      <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary mt-2">
        <h5 class="fw-bolder fs-3 font-monospace mb-2 mt-4 ms-md-5 ms-3"><span class="ms-md-5"></span><span class="ms-md-5"></span><strong class="text-start ms-md-5">Address Information</strong> </h5>
      </div>
      <div class="card-body border-0 px-md-5 mx-md-5">
        <div class="container px-md-5">
          <hr class="mt-0 mb-4">
          <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
            <div class="container-fluid justify-content-center">
              @if ($user?->address)
                <form action="{{ route('addresses.update', $address->id) }}" method="POST" class="">
                  @csrf
                  @method('PUT')
                  <div class="card bg-warning-subtle border-0 p-3 p-md-5">
                    <div class="row g-2">
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" name="barangay" class="form-control" placeholder="Barangay" aria-label="Barangay" id="floating_barangay" value="{{ $address->barangay }}">
                        <label for="floating_barangay" class="form-label">Barangay</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" name="municipality" class="form-control" placeholder="Municipality" aria-label="Municipality" id="floating_municipality" value="{{ $address->municipality }}">
                        <label for="floating_municipality" class="form-label">Municipality</label>
                      </div>
                    </div>
                    <div class="row g-2 mt-2">
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" id="floating_province" name="province" class="form-control" placeholder="Province" aria-label="Province" value="{{ $address->province }}">
                        <label for="floating_province" class="form-label">Province</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" id="floating_zip_code" name="zip_code" class="form-control" placeholder="Zip Code" aria-label="Zip Code" value="{{ $address->zip_code }}">
                        <label for="floating_zip_code" class="form-label">Zip Code</label>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer border-0 bg-warning-subtle">
                    <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
                      <button type="submit" class="btn btn-success fw-bolder me-2">Update Address</button>
                      <button type="button" data-bs-toggle="modal" data-bs-target="#deleteAddressModal" class="btn btn-danger fw-bolder"
                        data-address-id="{{ $address->id }}">Delete Address
                      </button>
                    </div>
                    <div class="d-md-none">
                      <button type="submit" class="btn btn-success w-100 fw-bolder">Update Address</button>
                      <button type="button" data-bs-toggle="modal" data-bs-target="#deleteAddressModal" class="btn btn-danger w-100 fw-bolder mt-2"
                        data-address-id="{{ $address->id }}">Delete Address
                      </button>
                    </div>
                  </div>
                </form>
              @else
                <form action="{{ route('addresses.store') }}" method="POST">
                  @csrf
                  <div class="card bg-warning-subtle border-0 p-3 p-md-5">
                    <div class="row g-2">
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" name="barangay" class="form-control" placeholder="Barangay" aria-label="Barangay" id="floating_barangay">
                        <label for="floating_barangay" class="form-label">Barangay</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" name="municipality" class="form-control" placeholder="Municipality" aria-label="Municipality" id="floating_municipality">
                        <label for="floating_municipality" class="form-label">Municipality</label>
                      </div>
                    </div>
                    <div class="row g-2 mt-2">
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" id="floating_province" name="province" class="form-control" placeholder="Province" aria-label="Province">
                        <label for="floating_province" class="form-label">Province</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" id="floating_zip_code" name="zip_code" class="form-control" placeholder="Zip Code" aria-label="Zip Code">
                        <label for="floating_zip_code" class="form-label">Zip Code</label>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer border-0 bg-warning-subtle">
                    <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
                      <button type="submit" class="btn btn-success fw-bolder">Create Address</button>
                    </div>
                    <div class="d-md-none">
                      <button type="submit" class="btn btn-success w-100 fw-bolder">Create Address</button>
                    </div>
                  </div>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary mt-2">
        <h5 class="fw-bolder fs-3 font-monospace mb-2 mt-4 ms-md-5 ms-3"><span class="ms-md-5"></span><span class="ms-md-5"></span><strong class="text-start ms-md-5">Household Information</strong> </h5>
      </div>
      <div class="card-body border-0 px-md-5 mx-md-5" data-controller="household">
        <div class="container px-md-5">
          <hr class="mt-0 mb-4">
          <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
            <div class="container-fluid justify-content-center">
              @if ($user?->household)
                <form action="{{ route('households.update', $household->id) }}" method="POST" class="">
                  @csrf
                  @method('PUT')
                  <div class="card bg-warning-subtle border-0 p-3 p-md-5">
                    <div class="row g-2">
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" name="house_structure" class="form-control" placeholder="House Structure" aria-label="House Structure" id="floating_house_structure" value="{{ $household->house_structure }}">
                        <label for="floating_house_structure" class="form-label">House Structure (e.g Apartment, Tiny House, Cabin, etc.)</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating">
                        <input type="number" min="1" name="household_members" class="form-control" placeholder="House Members" aria-label="House Members" id="floating_house_members" value="{{ $household->household_members }}">
                        <label for="floating_house_members" class="form-label">House members (including yourself)</label>
                      </div>
                    </div>
                    <div class="row g-2 mt-2">
                      <div class="col-12 col-md-6 form-floating">
                        <select name="have_children" id="floating_have_children" class="form-select" aria-label="children-select" data-action="change->household#toggleNumberOfChildrenField" required>
                          <option hidden >Are there children in the house?</option>
                          <option value="true" @selected($household->have_children)>Yes</option>
                          <option value="false" @selected(!$household->have_children) >No</option>
                        </select>
                        <label for="floating_have_children" class="form-label">Are there children in the house?</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating d-none" id="number_of_children_div">
                        <input type="number" min="1" name="number_of_children" class="form-control" placeholder="Number of Children" aria-label="Number of Children" id="floating_number_of_children" value="{{ $household->number_of_children }}">
                        <label for="floating_number_of_children" class="form-label">Number of Children</label>
                      </div>
                    </div>
                    <div class="row g-2 mt-2">
                      <div class="col-12 col-md-4 form-floating">
                        <select name="has_other_pets" id="floating_has_other_pets" class="form-select" aria-label="other-pet-select" data-action="change->household#togglePetsFields" required>
                          <option hidden >Do you have other pets?</option>
                          <option value="true" @selected($household->has_other_pets)>Yes</option>
                          <option value="false" @selected(!$household->has_other_pets) >No</option>
                        </select>
                        <label for="floating_has_other_pets" class="form-label">Do you have other pets?</label>
                      </div>
                      <div class="col-12 col-md-4 form-floating d-none" id="current_pets_div">
                        <input type="text" id="floating_current_pets" name="current_pets" class="form-control" placeholder="Current Pet/s" aria-label="Current Pet/s" value="{{ $household->current_pets }}">
                        <label for="floating_current_pets" class="form-label">Current Pet/s (Separate with , if more than one) </label>
                      </div>
                      <div class="col-12 col-md-4 form-floating d-none" id="number_of_current_pets_div">
                        <input type="number" min="1" id="floating_number_of_current_pets" name="number_of_current_pets" class="form-control" placeholder="Number of Current Pet/s" aria-label="Number of Current Pet/s" value="{{ $household->number_of_current_pets }}">
                        <label for="floating_number_of_current_pets" class="form-label">Number of Current Pet/s</label>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer border-0 bg-warning-subtle">
                    <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
                      <button type="submit" class="btn btn-success fw-bolder me-2">Update Household</button>
                      <button type="button" class="btn btn-danger fw-bolder">Delete Household</button>
                    </div>
                    <div class="d-md-none">
                      <button type="submit" class="btn btn-success w-100 fw-bolder">Update Household</button>
                      <button type="button" class="btn btn-danger w-100 fw-bolder mt-2">Delete Household</button>
                    </div>
                  </div>
                </form>
              @else
                <form action="{{ route('households.store') }}" method="POST">
                  @csrf
                  <div class="card bg-warning-subtle border-0 p-3 p-md-5">
                    <div class="row g-2">
                      <div class="col-12 col-md-6 form-floating">
                        <input type="text" name="house_structure" class="form-control" placeholder="House Structure" aria-label="House Structure" id="floating_house_structure" >
                        <label for="floating_house_structure" class="form-label">House Structure (e.g Apartment, Tiny House, Cabin, etc.)</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating">
                        <input type="number" min="1" name="household_members" class="form-control" placeholder="House Members" aria-label="House Members" id="floating_house_members">
                        <label for="floating_house_members" class="form-label">House members (including yourself)</label>
                      </div>
                    </div>
                    <div class="row g-2 mt-2">
                      <div class="col-12 col-md-6 form-floating">
                        <select name="have_children" id="floating_have_children" class="form-select" aria-label="children-select" data-action="change->household#toggleNumberOfChildrenField" required>
                          <option hidden >Are there children in the house?</option>
                          <option value="true" >Yes</option>
                          <option value="false" >No</option>
                        </select>
                        <label for="floating_have_children" class="form-label">Are there children in the house?</label>
                      </div>
                      <div class="col-12 col-md-6 form-floating d-none" id="number_of_children_div">
                        <input type="number" min="1" name="number_of_children" class="form-control" placeholder="Number of Children" aria-label="Number of Children" id="floating_number_of_children">
                        <label for="floating_number_of_children" class="form-label">Number of Children</label>
                      </div>
                    </div>
                    <div class="row g-2 mt-2">
                      <div class="col-12 col-md-4 form-floating">
                        <select name="has_other_pets" id="floating_has_other_pets" class="form-select" aria-label="other-pet-select" data-action="change->household#togglePetsFields" required>
                          <option hidden >Do you have other pets?</option>
                          <option value="true" >Yes</option>
                          <option value="false" >No</option>
                        </select>
                        <label for="floating_has_other_pets" class="form-label">Do you have other pets?</label>
                      </div>
                      <div class="col-12 col-md-4 form-floating d-none" id="current_pets_div">
                        <input type="text" id="floating_current_pets" name="current_pets" class="form-control" placeholder="Current Pet/s" aria-label="Current Pet/s">
                        <label for="floating_current_pets" class="form-label">Current Pet/s (Separate with , if more than one) </label>
                      </div>
                      <div class="col-12 col-md-4 form-floating d-none" id="number_of_current_pets_div">
                        <input type="number" min="1" id="floating_number_of_current_pets" name="number_of_current_pets" class="form-control" placeholder="Number of Current Pet/s" aria-label="Number of Current Pet/s">
                        <label for="floating_number_of_current_pets" class="form-label">Number of Current Pet/s</label>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer border-0 bg-warning-subtle">
                    <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
                      <button type="submit" class="btn btn-success fw-bolder">Create Household</button>
                    </div>
                    <div class="d-md-none">
                      <button type="submit" class="btn btn-success w-100 fw-bolder">Create Household</button>
                    </div>
                  </div>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection