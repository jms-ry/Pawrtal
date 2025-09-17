<nav class="navbar navbar-expand-lg bg-success pe-2 ps-2 border-0" data-bs-theme="light">
  <div class="container-fluid ms-lg-5">
    <div class="d-flex align-items-center">
      <img src="{{ asset('images/pets.png') }}" alt="Logo" height="40" class="me-1 fw-bolder">
      <a class="navbar-brand fs-1 fw-bolder" href="{{ url('/') }}">Pawrtal</a>
    </div>
    <button class="navbar-toggler border border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <!--Small Screen-->
        <ul class="navbar-nav me-auto d-lg-none border-top border-1 border-white pt-2 mt-3">
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
            <a class="nav-link fw-bold fs-5" href="/donate">Donate</a>
          </li>
          @guest
            <li class="d-lg-none">
              <a class="nav-link fw-bold fs-5" href="/register">Register</a>
            </li>
            <li class="d-lg-none">
              <a class="nav-link fw-bold fs-5" href="/login">Log in</a>
            </li>
          @endguest
          @auth
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold fs-5" data-bs-toggle="dropdown" aria-expanded="false"><span class="align-items-center">{{ Auth::user()->fullName() }}<i class="bi bi-caret-down-fill ms-2"></i></span></a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('users.show',Auth::user()->id) }}">Profile</a></li>
                @if(Auth::user()->isAdminOrStaff())
                  <li><a class="dropdown-item" href="#">Manage</a></li>
                @else
                  <li><a class="dropdown-item" href="{{ route('users.myAdoptionApplications') }}">Adoption Applications</a></li>
                @endif
                <li><a class="dropdown-item" href="{{ route('users.myDonations') }}">My Donation History</a></li>
                <li><a class="dropdown-item" href="{{ route('users.myReports') }}">My Reports</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item border-0" type="submit">Logout</button>
                  </form>
                </li>
              </ul>
            </li>
          @endauth
        </ul>
      <!---->
      <div class="d-none d-sm-flex flex-column flex-sm-row">
        <a href="{{ url('/rescues') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Rescues</a>
        <a href="{{ url('/reports') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Lost-and-Found Reports</a>
        <a href="{{ url('/adoption') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Adopt a Rescue</a>
        <a href="{{ url('/donate') }}" class="btn btn-outline-success me-md-1 me-0 ms-1 border border-0 fw-bold fs-5">Donate</a>
      </div>
      <div class="d-none d-sm-flex flex-column flex-sm-row justify-content-end ms-auto">
        @guest
          @if((!request()->is('register')) && (!request()->is('sign-in')))
            <a href="{{ url('/register') }}" class="btn btn-warning me-md-1 me-0 d-flex align-items-center fw-bolder" style="background-color: #FFF44F;">
              <i class="bi bi-person me-1 fs-5 fw-bolder"></i> Register
            </a>
            <a href="{{ url('/login') }}" class="btn btn-info d-flex align-items-center fw-bolder" style="background-color: #82EEFD;">
              <i class="bi bi-box-arrow-in-right me-1 fs-5 fw-bolder"></i>Log in
            </a>
          @endif
        @endguest
        @auth
          <div class="dropdown-center me-5">
            <button class="btn btn-outline-success border-0 dropdown-toggle fw-bolder" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              {{ Auth::user()->fullName() }}
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('users.show',Auth::user()->id) }}">Profile</a></li>
              @if(Auth::user()->isAdminOrStaff())
                <li><a class="dropdown-item" href="#">Manage</a></li>
              @else
                <li><a class="dropdown-item" href="{{ route('users.myAdoptionApplications') }}">Adoption Applications</a></li>
              @endif
              <li><a class="dropdown-item" href="{{ route('users.myDonations') }}">My Donation History</a></li>
              <li><a class="dropdown-item" href="{{ route('users.myReports') }}">My Reports</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button class="dropdown-item border-0" type="submit">Logout</button>
                </form>
              </li>
            </ul>
          </div>
        @endauth
      </div>
    </div>
  </div>
</nav>