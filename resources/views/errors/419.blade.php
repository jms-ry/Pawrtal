@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Session Expired
                    </h4>
                </div>
                <div class="card-body">
                    <h5>Your session has expired</h5>
                    <p class="text-muted">For your security, your session has timed out.</p>
                    
                    <div class="alert alert-info mt-3">
                        <strong>Why did this happen?</strong>
                        <ul class="mb-0 mt-2">
                            <li>You left the page open for too long</li>
                            <li>Your internet connection was interrupted</li>
                            <li>You logged out in another tab</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-secondary">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i> Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection