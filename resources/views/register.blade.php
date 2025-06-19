@extends('layouts.app')

@section('content')
  <div class="container-fluid mt-5 mb-5">
    <div class="row g-4">
      <div class="col-6 d-flex align-items-center justify-content-center d-none d-lg-block">
        <div class="carousel slide carousel-fade" id="registerPetCarousel" data-bs-ride="carousel" data-bs-pause="false">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="3000">
              <img src="{{ asset('images/register/register-3.jpg') }}" alt="Happy pet" class="img-fluid">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/register/register-1.jpg') }}" alt="Happy pet" class="img-fluid">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/register/register.jpg') }}" alt="Happy pet" class="img-fluid">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/register/register-2.jpg') }}" alt="Happy pet" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 mt-5">
        <h1 class="text-center fw-bold">Create a <a class="fw-bolder link-opacity-100">Pawrtal</a> account Today!</h1>
        <form action="" class="mt-5">
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label for="first_name" class="form-label">First Name</label>
              <input type="text" id="first_name"  name="first_name" class="form-control" placeholder="First name" aria-label="First name">
            </div>
            <div class="col-12 col-md-6">
              <label for="last_name" class="form-label">Last Name</label>
              <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Last name" aria-label="Last name">
            </div>
          </div>
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="Email" aria-label="Email">
            </div>
            <div class="col-12 col-md-6">
              <label for="contact_number" class="form-label">Contact Number</label>
              <input type="tel" id="contact_number" name="contact_number" class="form-control" placeholder="Contact Number" aria-label="Contact Number">
            </div>
          </div>
          <div class="row g-2 mt-1">
            <div class="col-12 col-md-6">
              <label for="password" class="form-label mt-2">Password</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="Password" aria-label="Password">
            </div>
            <div class="col-12 col-md-6">
              <label for="password_confirmation" class="form-label mt-2">Password Confirmation</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Password Confirmation" aria-label="Password Confirmation">
            </div>
          </div>
          <div class="d-flex flex-column mt-4 mb-3 align-items-center me-0 me-md-2 ms-0 ms-md-2">
            <button type="button" class="btn btn-success w-100 ">Create an Account</button>
            <div class="align-items-center text-center">
              <p class="mt-3">Already have an account? <a href="{{ url('/sign-in') }}" class="fw-bold link-opacity-100">Sign in here</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection