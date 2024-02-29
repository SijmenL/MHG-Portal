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
                <a class="nav-link active" aria-current="page">Posts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.forum-management.comments', ['search' => $search, 'user' => $search_user]) }}"">Reacties</a>
            </li>
        </ul>

        <div class="tab-pane show active" id="posts" role="tabpanel" aria-labelledby="posts-tab" tabindex="0">
            @if($posts->count() > 0)
                @foreach($posts as $post)
                    <div id="{{$post->id}}" class="mt-5 rounded bg-white">
                        <div
                            class="bg-info d-flex flex-row-responsive justify-content-center forum-post rounded">
                            @if(isset($post->user))
                                <div
                                    class="d-flex flex-column align-items-center justify-content-center forum-user-info h-100">
                                    <div class="rounded overflow-hidden">
                                        @if($post->user->profile_picture)
                                            <img alt="profielfoto" class="user-forum-picture"
                                                 src="{{ asset('/profile_pictures/' .$post->user->profile_picture) }}">
                                        @else
                                            <img alt="profielfoto" class="user-forum-picture"
                                                 src="{{ asset('img/no_profile_picture.webp') }}">
                                        @endif
                                    </div>
                                    <div class="fw-bold">
                                        {{ $post->user->name.' '.$post->user->infix.' '.$post->user->last_name }}
                                    </div>
                                    <p class="text-center">{{ $post->created_at }}
                                        geplaatst</p>
                                    @if ($post->updated_at->format('Y-m-d H:i:s') !== $post->created_at->format('Y-m-d H:i:s'))
                                        <p class="text-center">{{ $post->updated_at }} bewerkt
                                        </p>
                                    @endif

                                    @if($post-> location === 0)
                                        <p><b>Dolfijnen</b></p>
                                    @endif
                                    @if($post-> location === 1)
                                        <p><b>Zeeverkenners</b></p>
                                    @endif
                                    @if($post-> location === 2)
                                        <p><b>Loodsen</b></p>
                                    @endif
                                    @if($post-> location === 3)
                                        <p><b>Afterloodsen</b></p>
                                    @endif
                                    @if($post-> location === 4)
                                        <p><b>Leiding</b></p>
                                    @endif

                                </div>

                                <span class="message-arrow"></span>
                            @endif

                            <div class="d-flex flex-column w-100">
                                <a class="text-decoration-none" style="color: unset"
                                   href="{{ route('admin.forum-management.post', $post->id) }}">
                                    <div style="overflow: auto"
                                         class="w-100 forum-content bg-white p-3 rounded">{!! $post->content !!}</div>
                                </a>
                                <div class="mt-1 bg-white p-2 rounded d-flex flex-row justify-content-between">
                                    <div class="d-flex flex-row">
                                        <a title="@foreach($post->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                           class="btn d-flex align-items-center" style="cursor: not-allowed">
                                            {{ $post->likes->count() }} <span
                                                class="material-symbols-rounded">favorite</span></a>
                                        <a href="{{ route('admin.forum-management.post', $post->id) }}#comments"
                                           class="btn d-flex align-items-center comment">
                                            {{ $post->comments->count() }}
                                            <span class="material-symbols-rounded">chat</span>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-row">
                                        <a class="delete-button btn d-flex align-items-center"
                                           data-id="{{ $post->id }}"
                                           data-name="deze post en alle reacties eronder"
                                           data-link="{{ route('admin.forum-management.post.delete', $post->id) }}"><span
                                                class="material-symbols-rounded">delete</span></a>

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                @endforeach
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
            @else
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                    <span class="material-symbols-rounded me-2">post</span>Geen posts gevonden...
                </div>
            @endif
        </div>
    </div>
@endsection
