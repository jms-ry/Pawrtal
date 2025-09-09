@if ($message = Session::get('success'))
  <div class="alert alert-primary primary alert-dismissible fade show mt-2" role="alert">
    <strong> <i class="bi bi-check-circle-fill me-1"></i> {{ $message }}</strong>
  </div>
@endif

@if ($message = Session::get('error'))
  <div class="alert alert-danger danger border-0 alert-dismissible fade show mt-2" role="alert">
    <strong> <i class="bi bi-x-circle-fill me-1"></i> {{ $message }}</strong>
  </div>
@endif  

@if ($message = Session::get('warning'))
  <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
    <strong> <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}</strong>
  </div>
@endif

@if ($message = Session::get('info'))
  <div class="alert alert-info alert-dismissible fade show mt-2" role="alert">
    <strong> <i class="bi bi-info-circle-fill me-1"></i> {{ $message }}</strong>
  </div>
@endif