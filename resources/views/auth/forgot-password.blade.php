@extends('layouts.app')
@section('content')
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 450px; width: 100%;">
      <h4 class="fw-bold mb-2">Forgot Password?</h4>
      <p class="text-muted mb-4">No worries! Enter your email and we'll send you a reset link.</p>
      <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
        @csrf
        <div class="form-floating mb-3">
          <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
          <label for="email">Email Address</label>
          @error('email')
            <small class="invalid-feedback fw-bold">{{ $message }}</small>
          @enderror
        </div>
        <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-bold">
          Send Reset Link
        </button>
      </form>
      <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none" id="backLink">
          Back to Sign In
        </a>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function() {
      const btn = document.getElementById('submitBtn')
      const backLink = document.getElementById('backLink')

      backLink.classList.add('pointer-events-none', 'text-muted', 'opacity-50')
      
      btn.disabled = true
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...`
    })
  </script>
@endsection