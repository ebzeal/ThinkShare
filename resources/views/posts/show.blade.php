@extends('layout')

@section('content')
    <h1> {{ $post -> title }} </h1>
    <p> {{ $post->content }} </p>
    <p> Added {{ $post->created_at->diffForHumans() }} </p>

    <!-- @if ($post->id === 1)
        Post one!
    @elseif ($post->id === 2)
        Post two!
    @else
        Post {{ $post->id }}
    @endif -->

    <h1>Comments</h1>

    @forelse($post->comments as $comment)
    <p>
        {{ $comment->content }}, 
    </p>
    <p class='text-muted'>
    added {{ $comment->created_at->diffForHumans() }}
    </p>

    @empty
        <p>No Comments yet</p>
    @endforelse

@endsection('content')
