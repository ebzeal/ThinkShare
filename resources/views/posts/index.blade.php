@extends('layout')

@section('content')
    @forelse ($posts as $post)
        <p>
            <h3>
                <a href="{{ route('posts.show', ['post' => $post->id] ) }}"> {{ $post->title }} </a>
            </h3>
            <p class="text-muted"> 
                Added {{ $post->created_at->diffForHumans() }} 
                by {{ $post->user->name }}
            </p>

            
            @switch($count = $post->comments_count)
                @case($count === false)
                    <p> No Comments yet</p>
                    @break
                @case($count > 1)
                    <p> {{ $count }} comments</p>
                    @break
                @case($count === 1)
                    <p> {{ $count }} comment</p>
                    @break
                @default
                    <p>No Comments yet</p>
            @endswitch

            @can('update', $post)
            <a href="{{ route('posts.edit', ['post' => $post->id]) }}" class="btn btn-primary"> Edit Post</a>
            @endcan

            @can('delete', $post)
            <form method="POST" class="fm-inline"
                action="{{ route('posts.destroy', ['post' => $post->id]) }}" >
                @csrf
                @method('DELETE')
                <input type="submit" value="Delete" class="btn btn-primary" />
            </form>
            @endcan
        </p>
    @empty
        <p> No posts here </p>
    @endforelse
@endsection('content')