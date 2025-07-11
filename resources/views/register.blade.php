@extends('layouts.app')

@section('content')
  <div class="container-fluid mt-5 mb-5">
    <div class="row g-4">
      <div class="col-6 d-flex align-items-center justify-content-center d-none d-lg-block">
        <div class="carousel slide" id="registerPetCarousel" data-bs-ride="carousel" data-bs-pause="false">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="3000">
              <img src="{{ asset('images/register/register-3.jpg') }}" alt="Happy pet" class="img-fluid border border-0 rounded-4">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/register/register-1.jpg') }}" alt="Happy pet" class="img-fluid border border-0 rounded-4">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/register/register.jpg') }}" alt="Happy pet" class="img-fluid border border-0 rounded-4">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/register/register-2.jpg') }}" alt="Happy pet" class="img-fluid border border-0 rounded-4">
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 mt-5">
        <h1 class="text-center fw-bold">Create a <a class="fw-bolder link-opacity-100">Pawrtal</a> account Today!</h1>
        <form action="" class="mt-5">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="email" class="form-control" placeholder="First name" aria-label="First name" id="floating_first_name" autofocus>
              <label for="floating_first_name" class="form-label">First Name</label>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="text" name="last_name" class="form-control" placeholder="Last name" aria-label="Last name" id="floating_last_name">
              <label for="floating_last_name" class="form-label">Last Name</label>
            </div>
          </div>
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="email" id="floating_email" name="email" class="form-control" placeholder="Email" aria-label="Email">
              <label for="floating_email" class="form-label">Email</label>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="tel" id="floating_contact_number" name="contact_number" class="form-control" placeholder="Contact Number" aria-label="Contact Number">
              <label for="floating_contact_number" class="form-label">Contact Number</label>
            </div>
          </div>
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="password" id="floating_password" name="password" class="form-control" placeholder="Password" aria-label="Password">
              <label for="floating_password" class="form-label">Password</label>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="password" id="floating_password_confirmation" name="password_confirmation" class="form-control" placeholder="Password Confirmation" aria-label="Password Confirmation">
              <label for="floating_password_confirmation" class="form-label">Password Confirmation</label>
            </div>
          </div>
          <div class="d-flex flex-column mt-4 mb-3 align-items-center me-0 me-md-2 ms-0 ms-md-2 mt-2">
            <button type="button" class="btn btn-success w-100 fw-bolder">Create an Account</button>
            <div class="align-items-center text-center">
              <p class="mt-3">Already have an account? <a href="{{ url('/sign-in') }}" class="fw-bolder text-success">Sign in here</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection