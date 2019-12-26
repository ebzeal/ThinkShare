@extends('layout')

@section('content')
    {!! $welcome !!}
    <br/>
    {{ $data['title'] }}
@endsection('content')