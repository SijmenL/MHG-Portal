@extends('layouts.leiding')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">
        <h1>Post</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('leiding') }}">Leiding</a></li>
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
                class="@if($post->user->id === Auth::id()) bg-secondary-subtle @else bg-info @endif d-flex flex-row-responsive justify-content-center forum-post rounded">
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
                        <p class="text-center">{{ Carbon::parse($post->created_at)->diffForHumans() }} geplaatst</p>
                        @if ($post->updated_at->format('Y-m-d H:i:s') !== $post->created_at->format('Y-m-d H:i:s'))
                            <p class="text-center">{{ Carbon::parse($post->updated_at)->diffForHumans() }} bewerkt</p>
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
                   class="btn d-flex align-items-center like-button {{ $post->likes->contains('user_id', auth()->id()) ? ' liked' : '' }}"
                   data-post-id="{{ $post->id }}" data-post-type="0">{{ $post->likes->count() }} <span
                        class="material-symbols-rounded">favorite</span></a>
                <a href="#comments" class="btn d-flex align-items-center comment">{{ $post->comments->count() }} <span
                        class="material-symbols-rounded">chat</span></a>
            </div>

            <div class="d-flex flex-row">
                @if($post->user->id === Auth::id())
                    <a href="{{ route('leiding.post.edit', $post->id) }}"
                       class="btn d-flex align-items-center edit"><span
                            class="material-symbols-rounded">edit</span></a>
                @endif
                @if((auth()->user()->roles->contains('role', 'Administratie')))
                    <a class="delete-button btn d-flex align-items-center"
                       data-id="{{ $post->id }}"

                       @if($post->user->id === Auth::id())
                           data-name="je post en alle reacties eronder"
                       @else
                           data-name="deze post en alle reacties eronder"
                       @endif
                       data-link="{{ route('leiding.post.delete', $post->id) }}"><span
                            class="material-symbols-rounded">delete</span></a>
                @endif
            </div>
        </div>

        <div id="comments" class="p-3 comment-section">
            <h1>{{ $post->comments->count() }} @if($post->comments->count() !== 1)
                    reacties
                @else
                    reactie
                @endif</h1>
            <p>Plaats een reactie onder de post van
                {{ $post->user->name.' '.$post->user->infix.' '.$post->user->last_name }}.
            </p>
            <div class="bg-light rounded-2 p-3">
                <div class="container">
                    <form method="POST" action="{{ route('leiding.comment-post', $post->id) }}">
                        @csrf
                        <div class="d-flex flex-row-responsive gap-2 comment-input">
                            <div class="text-input w-100" id="text-input" style="min-height: 75px"
                                 contenteditable="true">{!! old('content') !!}</div>
                            <div>
                                <button type="submit" class="btn d-flex align-items-center"><span
                                        class="save-button material-symbols-rounded" style="font-size: 30pt">send</span>
                                </button>
                                <small id="characters"></small>
                            </div>
                        </div>


                        @error('content')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror

                        <input id="content" name="content" type="text" class="d-none">
                    </form>
                </div>
            </div>


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
                                        <span>{{ Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                        @if ($comment->updated_at->format('Y-m-d H:i:s') !== $comment->created_at->format('Y-m-d H:i:s'))
                                            <span> (bewerkt)</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="content mt-1">
                                    <div class="comment-content">{!! $comment->content !!}</div>
                                    <div class="forum-buttons">
                                        <a title="@foreach($comment->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                           class="btn d-flex align-items-center like-button {{ $comment->likes->contains('user_id', auth()->id()) ? ' liked' : '' }}"
                                           data-post-id="{{ $comment->id }}"
                                           data-post-type="1">{{ $comment->likes->count() }} <span
                                                class="material-symbols-rounded">favorite</span></a>
                                        <a title="beantwoorden"
                                           class="btn d-flex align-items-center add-comment"><span
                                                class="material-symbols-rounded">add_comment</span></a>
                                        @if($comment->user->id === Auth::id())
                                            <a
                                                class="edit-button btn d-flex align-items-center edit"><span
                                                    class="material-symbols-rounded">edit</span></a>
                                        @endif
                                        @if($comment->user->id === Auth::id() || auth()->user()->roles->contains('role', 'Administratie'))
                                        <a class="delete-button btn d-flex align-items-center"
                                           data-id="{{ $post->id }}"

                                           @if($comment->user->id === Auth::id())
                                               data-name="je reactie"
                                           @else
                                               data-name="deze reactie"
                                           @endif

                                           data-link="{{ route('leiding.comment.delete', ['id' => $comment->id, 'postId' => $post->id]) }}">
                                            <span class="material-symbols-rounded">delete</span>
                                        </a>

                                        @endif
                                    </div>


                                    <form method="POST" class="comment-form" data-post-id="{{ $post->id }}"
                                          data-comment-id="{{ $comment->id }}" style="display: none"
                                          action="{{ route('leiding.reaction-post', ['id' => $post->id, 'commentId' => $comment->id]) }}">
                                        @csrf
                                        <div class="d-flex flex-row-responsive gap-2 comment-input">
                                            <div class="text-input w-100" id="text-input" style="min-height: 75px"
                                                 contenteditable="true"></div>
                                            <button type="submit" class="btn d-flex align-items-center submit-button">
                                                <span class="save-button material-symbols-rounded"
                                                      style="font-size: 30pt">send</span>
                                            </button>
                                        </div>

                                        @error('content')
                                        <span class="invalid-feedback"
                                              role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror

                                        <input name="content" type="text" class="content-input d-none">
                                    </form>
                                </div>

                                @if($comment->user->id === Auth::id())
                                    <form class="editable-content mt-2" style="display: none"
                                          onsubmit="e.preventDefault()">
                                        @csrf <!-- Add CSRF token field if not already present -->
                                        @method('PUT') <!-- Add method field if not already present -->
                                        <div class="text-input" style="min-height: 75px" id="text-input"
                                             contenteditable="true">{!! $comment->content !!}</div>
                                        <small id="characters"></small>

                                        @error('content')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror

                                        <div class="forum-buttons">
                                            <button type="button" class="btn d-flex align-items-center"><span
                                                    class="save-button material-symbols-rounded">save</span></button>

                                            <a id="cancel-edit"
                                               class="cancel-button btn d-flex align-items-center"><span
                                                    class="material-symbols-rounded">cancel</span></a>
                                        </div>
                                    </form>
                                @endif

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
                                                    <span>{{ Carbon::parse($reaction->created_at)->diffForHumans() }}</span>
                                                    @if ($reaction->updated_at->format('Y-m-d H:i:s') !== $reaction->created_at->format('Y-m-d H:i:s'))
                                                        <span> (bewerkt)</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="content mt-1">
                                                <div class="comment-content">{!! $reaction->content !!}</div>
                                                <div class="forum-buttons">
                                                    <a title="@foreach($reaction->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                                       class="btn d-flex align-items-center like-button {{ $reaction->likes->contains('user_id', auth()->id()) ? ' liked' : '' }}"
                                                       data-post-id="{{ $reaction->id }}"
                                                       data-post-type="1">{{ $reaction->likes->count() }} <span
                                                            class="material-symbols-rounded">favorite</span></a>
                                                    @if($reaction->user->id === Auth::id())
                                                        <a
                                                            class="edit-button btn d-flex align-items-center edit"><span
                                                                class="material-symbols-rounded">edit</span></a>
                                                    @endif
                                                    @if($reaction->user->id === Auth::id() || auth()->user()->roles->contains('role', 'Administratie'))
                                                    <a class="delete-button btn d-flex align-items-center"
                                                       data-id="{{ $post->id }}"

                                                       @if($reaction->user->id === Auth::id())
                                                           data-name="je reactie"
                                                       @else
                                                           data-name="deze reactie"
                                                       @endif

                                                       data-link="{{ route('leiding.comment.delete', ['id' => $reaction->id, 'postId' => $post->id]) }}">
                                                        <span class="material-symbols-rounded">delete</span>
                                                    </a>

                                                    @endif
                                                </div>
                                            </div>

                                            @if($reaction->user->id === Auth::id())
                                                <form class="editable-content mt-2" style="display: none"
                                                      onsubmit="e.preventDefault()">
                                                    @csrf <!-- Add CSRF token field if not already present -->
                                                    @method('PUT') <!-- Add method field if not already present -->
                                                    <div class="text-input" style="min-height: 75px" id="text-input"
                                                         contenteditable="true">{!! $reaction->content !!}</div>
                                                    <small id="characters"></small>

                                                    @error('content')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror

                                                    <div class="forum-buttons">
                                                        <button type="button"
                                                                class="btn d-flex align-items-center"><span
                                                                class="save-button material-symbols-rounded">save</span>
                                                        </button>

                                                        <a id="cancel-edit"
                                                           class="cancel-button btn d-flex align-items-center"><span
                                                                class="material-symbols-rounded">cancel</span></a>
                                                    </div>
                                                </form>
                                            @endif

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
