@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Post</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin') }}">Administratie</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.forum-management.posts') }}">Prikbord beheer</a></li>
                <li class="breadcrumb-item active" aria-current="page">Post</li>
            </ol>
        </nav>

        @if(Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if(Session::has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

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
                        <p class="text-center">{{ $post->created_at }} geplaatst</p>
                        @if ($post->updated_at->format('Y-m-d H:i:s') !== $post->created_at->format('Y-m-d H:i:s'))
                            <p class="text-center">{{ $post->updated_at}} bewerkt</p>
                        @endif

                        @if($post-> location === 0)
                            <a class="btn btn-dark mb-4 d-flex align-items-center gap-2" target="_blank" href="{{ route('dolfijnen.post', $post->id) }}"><span
                                    class="material-symbols-rounded">open_in_new</span><b> Open origineel</b></a>
                        @endif
                        @if($post-> location === 1)
                            <a class="btn btn-dark mb-4 d-flex align-items-center gap-2" target="_blank" href="{{ route('zeeverkenners.post', $post->id) }}"><span
                                        class="material-symbols-rounded">open_in_new</span><b> Open origineel</b></a>
                        @endif
                        @if($post-> location === 2)
                            <a class="btn btn-dark mb-4 d-flex align-items-center gap-2" target="_blank" href="{{ route('loodsen.post', $post->id) }}"><span
                                    class="material-symbols-rounded">open_in_new</span><b> Open origineel</b></a>
                        @endif
                        @if($post-> location === 3)
                            <a class="btn btn-dark mb-4 d-flex align-items-center gap-2" target="_blank" href="{{ route('afterloodsen.post', $post->id) }}"><span
                                    class="material-symbols-rounded">open_in_new</span><b> Open origineel</b></a>
                        @endif
                        @if($post-> location === 4)
                            <a class="btn btn-dark mb-4 d-flex align-items-center gap-2" target="_blank" href="{{ route('dolfijnen.post', $post->id) }}"><span
                                    class="material-symbols-rounded">open_in_new</span><b> Open origineel</b></a>
                        @endif
                    </div>
                    <span class="message-arrow"></span>
                @endif

                <div class="d-flex flex-column w-100">
                    <div style="overflow: auto;"
                         class="w-100 forum-content bg-white p-3 rounded">{!! $post->content !!}</div>
                </div>
            </div>
        </div>

        <div class="mt-1 bg-white p-2 rounded d-flex flex-row justify-content-between">
            <div class="d-flex flex-row">
                <a title="@foreach($post->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                   class="btn d-flex align-items-center" style="cursor: not-allowed">
                    {{ $post->likes->count() }} <span
                        class="material-symbols-rounded">favorite</span></a>
                <a href="#comments" class="btn d-flex align-items-center comment">{{ $post->comments->count() }} <span
                        class="material-symbols-rounded">chat</span></a>
            </div>

            <div class="d-flex flex-row">
                    <a class="delete-button btn d-flex align-items-center"
                       data-id="{{ $post->id }}"
                           data-name="deze post en alle reacties eronder"
                       data-link="{{ route('admin.forum-management.post.delete', $post->id) }}"><span
                            class="material-symbols-rounded">delete</span></a>

            </div>
        </div>

        <div id="comments" class="p-3 comment-section">
            <h1>{{ $post->comments->count() }} @if($post->comments->count() !== 1)
                    reacties
                @else
                    reactie
                @endif</h1>

            @foreach($post->comments as $comment)
                @if ($comment->comment_id === null)
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
                                            class="fw-bold @if($post->user_id === $comment->user_id) post-op @endif">
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
                                    <div class="comment-content">{!! $comment->content !!}</div>
                                    <div class="forum-buttons">
                                        <a title="@foreach($comment->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                           class="btn d-flex align-items-center" style="cursor: not-allowed">
                                            {{ $comment->likes->count() }} <span
                                                class="material-symbols-rounded">favorite</span></a>
                                            <a class="delete-button btn d-flex align-items-center"
                                               data-id="{{ $post->id }}"
                                                   data-name="deze reactie"
                                               data-link="{{ route('admin.forum-management.comment.delete', ['id' => $comment->id, 'postId' => $post->id]) }}">
                                                <span class="material-symbols-rounded">delete</span>
                                            </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reactions">
                            @foreach($comment->comments as $reaction)
                                <div class="comment p-3 rounded bg-white" data-comment-id="{{ $reaction->id }}"
                                     id="comment-{{ $reaction->id }}">
                                    <div class="d-flex flex-row gap-2">
                                        @if($reaction->user->profile_picture)
                                            <img alt="profielfoto" class="profle-picture forum-profile-picture"
                                                 src="{{ asset('/profile_pictures/' . $reaction->user->profile_picture) }}">
                                        @else
                                            <img alt="profielfoto" class="profle-picture forum-profile-picture"
                                                 src="{{ asset('img/no_profile_picture.webp') }}">
                                        @endif

                                        <div class="w-100 p-1" style=" width: 100%; overflow: auto">
                                            <div class="d-flex flex-row-responsive" style="margin-bottom: -15px;">
                                                <p><span
                                                        class="fw-bold @if($post->user_id === $reaction->user_id) post-op @endif">
                                                            {{ $reaction->user->name.' '.$reaction->user->infix.' '.$reaction->user->last_name }}
                                                        </span>
                                                </p>
                                                <p class="ms-1 comment-time">
                                                    <span>{{ $reaction->created_at }}</span>
                                                    @if ($reaction->updated_at->format('Y-m-d H:i:s') !== $reaction->created_at->format('Y-m-d H:i:s'))
                                                        <span> (bewerkt: {{ $comment->updated_at }})</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="content mt-1">
                                                <div class="comment-content">{!! $reaction->content !!}</div>
                                                <div class="forum-buttons">
                                                    <a title="@foreach($reaction->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                                       class="btn d-flex align-items-center" style="cursor: not-allowed">{{ $reaction->likes->count() }}<span
                                                            class="material-symbols-rounded">favorite</span></a>
                                                        <a class="delete-button btn d-flex align-items-center"
                                                           data-id="{{ $post->id }}"
                                                               data-name="deze reactie"
                                                           data-link="{{ route('admin.forum-management.comment.delete', ['id' => $reaction->id, 'postId' => $post->id]) }}">
                                                            <span class="material-symbols-rounded">delete</span>
                                                        </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

        </div>

    </div>
@endsection
