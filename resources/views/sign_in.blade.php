@extends('layouts.app')

@section('content')
  <div class="container-fluid mt-5 mb-5">
    <div class="row g-4 ms-md-4 me-md-4">
      <div class="col-12 col-md-6 mt-5">
        <div class="ms-md-5 text-center text-md-start">
          <h1 class="fw-bolder mt-0 mt-md-3 fs-1">Welcome Back</h1>
        </div>
        <p class="ms-2 ms-md-5 mt-3 fs-5">Don't have an account yet? <a href="{{ url('/register') }}" class="fw-bold text-decoration-underline fw-bolder">Create Account</a></p>
        <form action="" class="mt-4">
          <div class="row g-0 me-2 me-md-0">
            <div class="col-12 col-md-8 w-md-75 ms-2 ms-md-5">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Email">
            </div>
          </div>
          <div class="row g-0 me-2 me-md-0 mt-3">
            <div class="col-12 col-md-8 w-md-75 ms-2 ms-md-5">
              <label for="password" class="form-label">Password</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="Password" aria-label="Password">
            </div>
          </div>
          <div class="row g-0 me-2 me-md-0 mt-4">
            <div class="col-12 col-md-8 ms-2 ms-md-5">
              <button type="button" class="btn btn-success w-100 fw-bolder">Sign in</button>
              <p class="text-center text-decoration-underline fw-bold mt-2">Forgot your password?</p>
            </div>
          </div>
        </form>
      </div>
      <div class="col-6 d-flex align-items-center justify-content-center d-none d-lg-block">
        <div class="carousel slide" id="signInPetCarousel" data-bs-ride="carousel" data-bs-pause="false">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="3000">
              <img src="{{ asset('images/sign-in/aspin-4.jpg') }}" alt="Happy pet" class="img-fluid border border-0 rounded-4">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/sign-in/aspin-2.jpg') }}" alt="Happy pet" class="img-fluid border border-0 rounded-4">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
              <img src="{{ asset('images/sign-in/aspin-1.png') }}" alt="Happy pet" class="img-fluid border border-0 rounded-4">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection