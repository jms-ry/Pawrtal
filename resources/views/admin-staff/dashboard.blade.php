@extends('layouts.app')

@section('content')
<h6>Welcome!! <strong>{{ Auth::user()?->fullName() }}</strong> </h6>
<p>Your role is <strong>{{ Auth::user()?->getRole() }}</strong> </p>
@endsection