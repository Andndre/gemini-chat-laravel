@extends('layout')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Chat Sessions</h2>
            <a class="btn btn-primary" data-bs-toggle="modal" href="#createNewChatModal" role="button">New Chat</a>
        </div>
        <div class="row">
            @foreach ($allChats as $session)
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">{{ $session->name }}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="modal fade" id="createNewChatModal" aria-hidden="true" aria-labelledby="createNewChatModalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewChatModalLabel">
                        Select Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- display $allChatTemplates --}}
                    <div class="row">
                        @foreach ($allChatTemplates as $template)
                            <div class="col-12 mb-3">
                                <a href="{{ route('chat.create', ['template' => $template->id]) }}" class="text-decoration-none">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="card-title {{ $template->name == 'Default' ? 'text-primary' : '' }}">
                                                {{ $template->name }}
                                            </h3>
                                            <p class="card-text">{{ $template->description }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
