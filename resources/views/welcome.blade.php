@extends('layouts.app')

@section('content')
  <!--Large Screen-->
  <div class="card bg-primary border-0 d-none d-md-block">
    <div class="card-body p-0">
      <div class="carousel slide custom-carousel carousel-fade" id="welcomePetCarousel">
        <div class="carousel-inner">
          <div class="carousel-item active" style="height: 90vh;">
            <img src="{{ asset('images/welcome/dog-3.jpg') }}" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0" >
            <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
              <h1 class="display-5 fw-bold">Saving lives, one paw at a time.</h1>
              <p class="lead fs-4">Together, we’re rewriting their stories.</p>
              <button type="button" class="btn btn-primary btn-lg fw-bolder mt-2">I WANT TO HELP</button>
            </div>
          </div>
          <div class="carousel-item" style="height: 90vh;">
            <img src="{{ asset('images/welcome/dog-2.png') }}" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0">
            <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
              <h1 class="display-5 fw-bold">You can’t buy love, but you’ll find it in a rescue.</h1>
              <button type="button" class="btn btn-primary btn-lg fw-bolder mt-2">ADOPT FROM PAWRTAL</button>
            </div>
          </div>
          <div class="carousel-item" style="height: 90vh;">
            <img src="{{ asset('images/welcome/dog-1.png') }}" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0">
            <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
              <h1 class="display-5 fw-bold">Lost a Pet? Found One?</h1>
              <p class="lead fs-4">Seen a wandering animal or lost your furry friend? Report it here so we can take action.</p>
              <button type="button" class="btn btn-primary btn-lg fw-bolder mt-2">SUBMIT REPORT</button>
            </div>
          </div>
          <div class="carousel-item" style="height: 90vh;">
            <img src="{{ asset('images/welcome/dog-4.jpg') }}" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0">
            <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
              <h1 class="display-5 fw-bold">Every Life Matters — Let's Save One Today.</h1>
              <p class="lead fs-4">Even small acts of kindness can change lives — theirs and yours.</p>
              <button type="button" class="btn btn-primary btn-lg fw-bolder mt-2">HELP US HELP THEM</button>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#welcomePetCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#welcomePetCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </div>
  <!-- Small Screen -->
  <div class="card bg-primary border-0 d-lg-none">
    <div class="card-body p-0" style="height: 90vh;">
      <img src="{{ asset('images/welcome/dog-3.jpg') }}" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0">
      <div class="position-absolute top-50 start-50 translate-middle text-center text-white w-100 px-3">
        <h6 class="display-6 fw-bold">Saving lives, one paw at a time.</h6>
        <p class="lead fs-5">Together, we’re rewriting their stories.</p>
        <button type="button" class="btn btn-primary btn-lg fw-bolder mt-2">I WANT TO HELP</button>
      </div>
    </div>
  </div>
  <!-- About Us Section -->
  <div class="card mt-4 border-0">
    <div class="card-body p-3 p-md-5">
      <div class="container me-2 me-md-5 ms-2 ms-md-5">
        <h3 class="text-center">About Us</h3>
        <p data-aos="fade-left" data-aos-duration="500" class="fs-5 mt-5" style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        <p data-aos="fade-right" data-aos-duration="500" class="fs-5" style="text-align: justify;">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>
        <div class="d-flex flex-column justify-content-center align-items-center mt-5">
          <a href="{{ url('/adoption') }}" class="btn btn-primary btn-lg fw-bolder w-md-75 px-3">ADOPT A RESCUE TODAY</a>
          <a href="#" class="text-decoration-none mt-4 text-danger fw-bolder">Terms and Conditions for Adopting a Rescue</a>
        </div>
      </div>
    </div>
  </div>

@endsection
  
