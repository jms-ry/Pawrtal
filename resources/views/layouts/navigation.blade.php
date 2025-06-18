<nav class="navbar navbar-expand-lg bg-success pe-2 ps-2" data-bs-theme="light">
  <div class="container-fluid ms-lg-5">
    <div class="d-flex align-items-center">
      <img src="{{ asset('images/pets.png') }}" alt="Logo" height="40" class="me-1 fw-bolder">
      <a class="navbar-brand fs-1 fw-bolder" href="{{ url('/') }}">Pawrtal</a>
    </div>
    
    <button class="navbar-toggler border border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon "></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">

      <!--Small Screen-->
        <ul class="navbar-nav me-auto d-lg-none">
          <li class="nav-item ">
            <a class="nav-link fw-bold fs-5" href="/rescues">Rescues</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold fs-5" href="/reports">Lost-and-Found Reports</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold fs-5" href="/adoption">Adopt a Rescue</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold fs-5" href="donate">Donate</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold fs-5" href="#">About Us</a>
          </li>
          <li class="d-lg-none">
            <a class="nav-link fw-bold fs-5" href="/register">Create an Account</a>
          </li>
          <li class="d-lg-none">
            <a class="nav-link fw-bold fs-5" href="/sign-in">Sign in</a>
          </li>
        </ul>
      <!---->
      <div class="d-none d-sm-flex flex-column flex-sm-row">
        <a href="{{ url('/rescues') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Rescues</a>
        <a href="{{ url('/reports') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Lost-and-Found Reports</a>
        <a href="{{ url('/adoption') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Adopt a Rescue</a>
        <a href="{{ url('/donate') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Donate</a>
        <a href="#" class="btn btn-outline-success me-1 border border-0 fw-bold fs-5">About Us</a>
      </div>
      <div class="d-none d-sm-flex flex-column flex-sm-row justify-content-end ms-auto">
        @if((!request()->is('register')) && (!request()->is('sign-in')))
          <a href="{{ url('/register') }}" class="btn btn-warning me-md-1 me-0 d-flex align-items-center" style="background-color: #FFF44F;">
            <i class="bi bi-person me-md-2 me-0 fs-5"></i> Create an Account
          </a>
          <a href="{{ url('/sign-in') }}" class="btn btn-info d-flex align-items-center" style="background-color: #82EEFD;">
            <i class="bi bi-box-arrow-in-right me-md-2 me-0 fs-5"></i>Sign in
          </a>
        @endif
      </div>
    </div>
  </div>
</nav>