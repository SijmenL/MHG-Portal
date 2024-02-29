@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Prikbord beheer</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin') }}">Administratie</a></li>
                <li class="breadcrumb-item active" aria-current="page">Prikbord beheer</li>
            </ol>
        </nav>

        <form id="auto-submit" method="GET">
            <div class="d-flex">
                <div class="d-flex flex-row-responsive gap-2 align-items-center mb-3 w-100">
                    <div class="input-group">
                        <label for="search" class="input-group-text" id="basic-addon1">
                            <span class="material-symbols-rounded">search</span></label>
                        <input id="search" name="search" type="text" class="form-control"
                               placeholder="Zoeken op post of reactie."
                               aria-label="Zoeken" aria-describedby="basic-addon1" value="{{ $search }}" onchange="this.form.submit();">
                    </div>
                    <div class="input-group">
                        <label for="user" class="input-group-text" id="basic-addon1">
                            <span class="material-symbols-rounded">person</span></label>
                        <input id="user" name="user" type="number" class="form-control"
                               placeholder="Zoeken op gebruikers id"
                               aria-label="user" aria-describedby="basic-addon1" value="{{ $search_user }}" onchange="this.form.submit();">
                    </div>
                </div>
            </div>
        </form>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.forum-management.posts', ['search' => $search, 'user' => $search_user]) }}">Posts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" aria-current="page">Reacties</a>
            </li>
        </ul>

        <div id="comments">
            @if($comments->count() > 0)
                @foreach($comments as $comment)
                    <div class="comment mt-3 p-3 rounded bg-white" data-comment-id="{{ $comment->id }}"
                         id="{{ $comment->id }}">
                        <div class="d-flex flex-row gap-2">
                            @if($comment->user->profile_picture)
                                <img alt="profielfoto" class="profle-picture forum-profile-picture"
                                     src="{{ asset('/profile_pictures/' . $comment->user->profile_picture) }}">
                            @else
                                <img alt="profielfoto" class="profle-picture forum-profile-picture"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="w-100 p-1" style=" width: 100%; overflow: auto">
                                <div class="d-flex flex-row-responsive" style="margin-bottom: -15px;">
                                    <p><span
                                            class="fw-bold">
                                                {{ $comment->user->name.' '.$comment->user->infix.' '.$comment->user->last_name }}
                                            </span>
                                    </p>
                                    <p class="ms-1 comment-time">
                                        <span>{{ $comment->created_at }}</span>
                                        @if ($comment->updated_at->format('Y-m-d H:i:s') !== $comment->created_at->format('Y-m-d H:i:s'))
                                            <span> (bewerkt : {{ $comment->updated_at }})</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="content mt-1">
                                    <a class="text-decoration-none" style="color: unset"
                                       href="{{ route('admin.forum-management.post', $comment->post_id) . '#comments' }}">
                                    <div class="comment-content">{!! $comment->content !!}</div>
                                    </a>
                                    <div class="forum-buttons">
                                        <a title="@foreach($comment->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                           class="btn d-flex align-items-center" style="cursor: not-allowed">
                                            {{ $comment->likes->count() }} <span
                                                class="material-symbols-rounded">favorite</span></a>
                                        <a class="delete-button btn d-flex align-items-center"
                                           data-id="{{ $comment->id }}"
                                           data-name="deze reactie"
                                           data-link="{{ route('admin.forum-management.comment.delete', ['id' => $comment->id, 'postId' => $comment->post_id]) }}">
                                            <span class="material-symbols-rounded">delete</span>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                    <div class="mt-4">
                        {{ $comments->links() }}
                    </div>
            @else
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                    <span class="material-symbols-rounded me-2">comments_disabled</span>Geen reacties gevonden...
                </div>
            @endif
        </div>

    </div>
@endsection
