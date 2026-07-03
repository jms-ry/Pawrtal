@extends('layouts.app')
@section('content')
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 450px; width: 100%;">
      <h4 class="fw-bold mb-2">Reset Password</h4>
      <p class="text-muted mb-4">Enter your new password below.</p>

      <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-floating mb-3">
          <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email', $email) }}" required>
          <label for="email">Email Address</label>
          @error('email')
            <small class="invalid-feedback fw-bold">{{ $message }}</small>
          @enderror
        </div>

        <div class="form-floating mb-3">
          <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="New Password" required>
          <label for="password">New Password</label>
          @error('password')
            <small class="invalid-feedback fw-bold">{{ $message }}</small>
          @enderror
        </div>

        <div class="form-floating mb-3">
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" required>
          <label for="password_confirmation">Confirm New Password</label>
          <small class="invalid-feedback fw-bold" id="confirmFeedback"></small>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-bold">
          Reset Password
        </button>
      </form>
    </div>
  </div>

  <script>
    const form = document.getElementById('resetPasswordForm')
    const password = document.getElementById('password')
    const confirmation = document.getElementById('password_confirmation')
    const confirmFeedback = document.getElementById('confirmFeedback')
    const submitBtn = document.getElementById('submitBtn')

    confirmation.addEventListener('blur', function() {
      if (confirmation.value && confirmation.value !== password.value) {
        confirmation.classList.add('is-invalid')
        confirmFeedback.textContent = 'Passwords do not match.'
      } else {
        confirmation.classList.remove('is-invalid')
        confirmFeedback.textContent = ''
      }
    })

    password.addEventListener('input', function() {
      if (confirmation.value && confirmation.value !== password.value) {
        confirmation.classList.add('is-invalid')
        confirmFeedback.textContent = 'Passwords do not match.'
      } else {
        confirmation.classList.remove('is-invalid')
        confirmFeedback.textContent = ''
      }
    })

    form.addEventListener('submit', function(e) {
      if (confirmation.value !== password.value) {
        e.preventDefault()
        confirmation.classList.add('is-invalid')
        confirmFeedback.textContent = 'Passwords do not match.'
        return
      }

      submitBtn.disabled = true
      submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Resetting...`
    })
  </script>
@endsection