@extends('layout')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Chat Sessions</h2>
            <a href="{{ route('chat.form') }}" class="btn btn-primary">New Chat</a>
        </div>
        <ul class="list-group">
            @foreach($allChats as $session)
                <li class="list-group-item">{{ $session->name }}</li>
            @endforeach
        </ul>
    </div>
@endsection
