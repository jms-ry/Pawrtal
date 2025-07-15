@if ($message = Session::get('success'))
<div class="alert alert-success success alert-dismissible fade show" role="alert" data-controller="flash-message">
  <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger danger border-0 alert-dismissible fade show" role="alert" data-controller="flash-message">
  <strong>{{ $message }}</strong>
</div>
@endif  