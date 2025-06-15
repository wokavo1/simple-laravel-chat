@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Main Page</h1>
    <div class="d-flex flex-column">
        <a href="{{ route('chat.broadcast.index') }}"
            class="btn btn-dark m-1">Broadcast Chat</a>
        @auth
        <a href="{{ route('chat.private.index') }}"
            class="btn btn-dark m-1">Private Chat</a>
        @endauth
    </div>
</div>
@endsection