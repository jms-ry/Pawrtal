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
      <div class="col-12 col-md-6 mt-5" data-controller="form-validation">
        <h1 class="text-center fw-bold mt-md-5">Create a <a class="fw-bolder link-opacity-100">Pawrtal</a> account Today!</h1>
        <form action="{{ route('register') }}" method="POST" class="mt-5" data-action="submit->form-validation#validateForm ">
          @csrf
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="text" name="first_name" class="form-control" placeholder="First name" aria-label="First name" id="floating_first_name" autofocus data-form-validation-target="firstNameInput" data-action="blur->form-validation#validateFirstName">
              <label for="floating_first_name" class="form-label">First Name</label>
              <small class="invalid-feedback fw-bold" data-form-validation-target="firstNameFeedback"></small>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="text" name="last_name" class="form-control" placeholder="Last name" aria-label="Last name" id="floating_last_name" data-form-validation-target="lastNameInput" data-action="blur->form-validation#validateLastName">
              <label for="floating_last_name" class="form-label">Last Name</label>
              <small class="invalid-feedback fw-bold" data-form-validation-target="lastNameFeedback"></small>
            </div>
          </div>
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="email" id="floating_email" name="email" class="form-control" placeholder="Email" aria-label="Email" data-form-validation-target="emailInput" data-action="blur->form-validation#validateEmail">
              <label for="floating_email" class="form-label">Email</label>
              <small class="invalid-feedback fw-bold" data-form-validation-target="emailFeedback"></small>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="tel" id="floating_contact_number" name="contact_number" class="form-control" placeholder="Contact Number" aria-label="Contact Number" data-form-validation-target="contactNumberInput" data-action="blur->form-validation#validateContactNumber">
              <label for="floating_contact_number" class="form-label">Contact Number</label>
              <small class="invalid-feedback fw-bold" data-form-validation-target="contactNumberFeedback"></small>
            </div>
          </div>
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="password" id="floating_password" name="password" class="form-control" placeholder="Password" aria-label="Password" data-form-validation-target="passwordInput" data-action="blur->form-validation#validatePassword">
              <label for="floating_password" class="form-label">Password</label>
              <small class="invalid-feedback fw-bold" data-form-validation-target="passwordFeedback"></small>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="password" id="floating_password_confirmation" name="password_confirmation" class="form-control" placeholder="Password Confirmation" aria-label="Password Confirmation" data-form-validation-target="passwordConfirmationInput" data-action="blur->form-validation#validatePasswordConfirmation">
              <label for="floating_password_confirmation" class="form-label">Password Confirmation</label>
              <small class="invalid-feedback fw-bold" data-form-validation-target="passwordConfirmationFeedback"></small>
            </div>
          </div>
          <div class="d-flex flex-column mt-4 mb-3 align-items-center me-0 me-md-2 ms-0 ms-md-2 mt-2">
            <button type="submit" class="btn btn-success w-100 fw-bolder">Create an Account</button>
            <small class="invalid-feedback fw-bold" data-form-validation-target="formFeedback"></small>
            <div class="align-items-center text-center">
              <p class="mt-3">Already have an account? <a href="{{ url('/login') }}" class="fw-bolder text-success">Log in here</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection