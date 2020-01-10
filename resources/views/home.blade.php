@extends('layout')

@section('content')
<h1>{{ __('Welcome to Authors Share!') }}</h1>
<!-- <h1>{{ __('messages.welcome') }}</h1> -->
<!-- <h1>@lang('messages.welcome')</h1> -->

<p>{{ __('messages.example_with_value', ['name' => 'John']) }}</p>

<p>{{ trans_choice('messages.plural', 0, ['a' => "please add"]) }}</p>
<p>{{ trans_choice('messages.plural', 1, ['a' => "moderate comment"]) }}</p>
<p>{{ trans_choice('messages.plural', 2, ['a' => "Gaining Traction"]) }}</p>

<p>Using JSON: {{ __('Welcome to Authors Share!') }}</p>
<p>Using JSON: {{ __('Hello :name', ['name' => 'Piotr']) }}</p>

<p>This is the content of the main page!</p>
@endsection