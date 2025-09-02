<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pawrtal') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @inertiaHead
  </head>
  <body class="antialiased bg-secondary text-dark d-flex flex-column min-vh-100">
    <!--Large Screen Flash Message-->
    <div class="row d-flex flex-row-reverse d-none d-md-flex">
      <div class="col-4">
        <div class="alert-container me-3">
          @include('flash-messages')
        </div>
      </div>
    </div>
    <!--end large screen flash message-->

    <!--Small Screen Flash Message-->
    <div class="d-md-none d-sm-block">
      <div class="alert-container">
        @include('flash-messages')
      </div>
    </div>
    <!--end small screen flash message-->

    <main class="flex-grow-1 " data-controller="want-to-help-btn">
      @include('layouts.navigation')
      @inertia
    </main>
    @include('layouts.footer')
  </body>
  
</html>
