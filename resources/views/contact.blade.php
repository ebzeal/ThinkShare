@extends('layout')

@section('content')
    <h1>Contact Page </h1>
    <p>This is contact</p>

    @can('home.secret')
    <p>
    <a href="{{ route('secret') }}">
    Go to Special Contact Details
    </a>
    </p>
    @endcan
@endsection