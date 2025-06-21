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
              <a href="#how-you-can-help" class="btn btn-primary btn-lg fw-bolder mt-2">I WANT TO HELP</a>
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
        <a href="#how-you-can-help" class="btn btn-primary btn-lg fw-bolder mt-2">I WANT TO HELP</a>
      </div>
    </div>
  </div>
  <!-- About Us Section -->
  <div class="card mt-5 mt-md-4 border-0">
    <div class="card-body p-3 p-md-5">
      <div class="container me-2 me-md-5 ms-2 ms-md-5">
        <h3 class="text-center">About Us</h3>
        <p data-aos="fade-left" data-aos-duration="500" class="fs-5 mt-5 lh-lg" style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>
        <div class="d-flex flex-column justify-content-center align-items-center mt-5">
          <a href="{{ url('/adoption') }}" class="btn btn-primary btn-lg fw-bolder w-md-75 px-3">ADOPT A RESCUE TODAY</a>
          <a data-bs-toggle="modal" data-bs-target="#adoptionTermsAndConditionsModal" class="text-decoration-none mt-4 text-danger fw-bolder">Terms and Conditions for Adopting a Rescue</a>
        </div>
        <!--Adoption Terms and Conditions Modal-->
        <div class="modal fade" id="adoptionTermsAndConditionsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="adoptionTermsAndConditionsModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header border-0">
                <h5 class="modal-title fs-5 fw-bolder mt-2">Adoption Terms and Conditions</h5>
              </div>
              <div class="modal-body border-0">
                <p class="lead fs-6">The following are the terms and conditions to be complied during adoption process:</p>
                <ol class="ms-2">
                  <li class="fs-6" style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
                  <li class="fs-6" style="text-align: justify;">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </li>
                  <li class="fs-6" style="text-align: justify;">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</li>
                  <li class="fs-6" style="text-align: justify;">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</li>
                  <li class="fs-6" style="text-align: justify;">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</li>
                  <li class="fs-6" style="text-align: justify;">Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li>
                  <li class="fs-6" style="text-align: justify;">Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</li>
                  <li class="fs-6" style="text-align: justify;">Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</li>
                  <li class="fs-6" style="text-align: justify;">Nullam tincidunt tempor dui in pharetra. </li>
                </ol>
              </div>
              <div class="modal-footer border-0 mt-2">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">I understand</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--End About Us Section-->
  <!--Rescue Statistics Section-->
  <div class="card mt-4 border-0 bg-warning">
    <div class="card-body border-0 p-3 p-md-5">
      <div class="container-fluid">
        <div class="row g-0 d-flex align-items-center justify-content-center text-center p-3 p-md-5">
          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span class="display-5 fw-bolder" id="sheltered-count">0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Sheltered</h6>
          </div>
          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span class="display-5 fw-bolder" id="spayed-count">0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Spay/Neutred</h6>
          </div>
          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span class="display-5 fw-bolder" id="vaccinated-count">0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Vaccinated</h6>
          </div>
          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span class="display-5 fw-bolder" id="adopted-count">0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Adopted/Rehomed</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--End Rescue Statistics Section-->
  <!--How You Can Help Section-->
  <div id="how-you-can-help" class="card mt-5 mt-md-3 border-0">
    <div class="card-body border-0 p-3 p-md-5">
      <div class="container-fluid p3 p-md-5">
        <h3 class="text-center fw-bolder display-6">How You Can Help</h3>
        <div class="row g-3 mt-5">
          <div class="col-12 col-md-4" data-aos="zoom-in-right" data-aos-delay="200">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">Donate</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 px-md-5 lh-lg"style="text-align: justify;">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>
                <a href="{{ url('/donate') }}" class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto">Donate</a>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4" data-aos="zoom-in-up" data-aos-delay="400">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">Adopt</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 px-md-5 lh-lg"style="text-align: justify;">Suspendisse varius mollis convallis. Nulla eget mauris non urna gravida interdum sit amet malesuada erat. Morbi quis faucibus lacus. Sed risus justo, fringilla porttitor urna maximus, dictum pellentesque magna. Sed at quam laoreet, imperdiet neque a, bibendum arcu. In at magna a est posuere euismod.</p>
                <a href="{{ url('/adoption') }}" class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto">Adopt a Rescue</a>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4" data-aos="zoom-in-left" data-aos-delay="600">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">Report</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 px-md-5 lh-lg"style="text-align: justify;">Praesent eu eros hendrerit, maximus nisl et, cursus lacus. Maecenas sit amet lectus neque. Cras tincidunt tellus lectus, in mattis tortor lacinia eu. Ut ut malesuada ligula. Nam nulla lorem, sollicitudin vel neque vel, ornare eleifend neque. Fusce aliquet nisi non nisl porttitor, ac tincidunt metus ornare. </p>
                <a href="{{ url('/reports') }}" class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto">File a Report</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
  
