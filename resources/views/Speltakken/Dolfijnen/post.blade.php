@extends('layouts.dolfijnen')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">
        <h1>Post</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dolfijnen') }}">Dolfijnen</a></li>
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
                class="@if(isset($post->user) && $post->user->roles->contains('role', 'Dolfijnen Leiding') && $post->user->id !== Auth::id()) bg-danger-subtle @elseif($post->user->id === Auth::id()) bg-secondary-subtle @else bg-info @endif d-flex flex-row-responsive justify-content-center forum-post rounded">
                @if(isset($post->user))
                    <div
                        class="d-flex flex-column align-items-center justify-content-center forum-user-info h-100">
                        <div class="rounded overflow-hidden">
                            @if($post->user->profile_picture)
                                <img alt="profielfoto"
                                     style="max-width: 150px; aspect-ratio: 1/1; object-fit: cover"
                                     src="{{ asset('/profile_pictures/' .$post->user->profile_picture) }}">
                            @else
                                <img alt="profielfoto"
                                     style="max-width: 150px; aspect-ratio: 1/1; object-fit: cover"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                        </div>
                        <div class="fw-bold">
                            @if($post->user->roles->contains('role', 'Dolfijnen Leiding'))
                                {{ $post->user->dolfijnen_name }}
                            @else
                                {{ $post->user->name.' '.$post->user->infix.' '.$post->user->last_name }}
                            @endif
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
                   data-post-id="{{ $post->id }}">{{ $post->likes->count() }} <span
                        class="material-symbols-rounded">favorite</span></a>
                <a href="#comments" class="btn d-flex align-items-center comment">{{ $post->comments->count() }} <span
                        class="material-symbols-rounded">chat</span></a>
            </div>

            <div class="d-flex flex-row">
                @if($post->user->id === Auth::id())
                    <a href="{{ route('dolfijnen.post.edit', $post->id) }}"
                       class="btn d-flex align-items-center edit"><span
                            class="material-symbols-rounded">edit</span></a>
                @endif
                @if((auth()->user()->roles->contains('role', 'Dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')|| auth()->user()->roles->contains('role', 'Ouderraad')))
                    <a class="delete-button btn d-flex align-items-center"
                       data-id="{{ $post->id }}"
                       data-name="je post en alle reacties eronder"
                       data-link="{{ route('dolfijnen.post.delete', $post->id) }}"><span
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
            <p>Plaats een reactie onder de post van @if($post->user->roles->contains('role', 'Dolfijnen Leiding'))
                    {{ $post->user->dolfijnen_name }}.
                @else
                    {{ $post->user->name.' '.$post->user->infix.' '.$post->user->last_name }}.
                @endif Houd de reacties netjes.</p>
            <div class="bg-light rounded-2 p-3">
                <div class="container">
                    <div id="text-input" contenteditable="true">{!! old('content') !!}</div>
                    <small id="characters"></small>

                    @error('content')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror

                    <form method="POST" action="{{ route('dolfijnen.comment-post', $post->id) }}">
                        @csrf
                        <input id="content" name="content" type="text" class="d-none">

                        <button type="submit" class="btn btn-dark mt-3">Plaats reactie</button>
                    </form>
                </div>
            </div>


            @foreach($post->comments as $comment)
                <div class="comment mt-3 p-3 rounded bg-white" data-comment-id="{{ $comment->id }}">
                    <div class="d-flex flex-row gap-2">
                        @if($comment->user->profile_picture)
                            <img alt="profielfoto" class="profle-picture"
                                 src="{{ asset('/profile_pictures/' . $comment->user->profile_picture) }}">
                        @else
                            <img alt="profielfoto" class="profle-picture"
                                 src="{{ asset('img/no_profile_picture.webp') }}">
                        @endif

                        <div class="w-100 p-1" style=" width: 100%; overflow: auto">
                            <div class="d-flex flex-row" style="margin-bottom: -15px;">
                                <p><span
                                        class="fw-bold @if($post->user_id === $comment->user_id) post-op @endif">@if($comment->user->roles->contains('role', 'Dolfijnen Leiding'))
                                            {{ $comment->user->dolfijnen_name }}
                                        @else
                                            {{ $comment->user->name.' '.$comment->user->infix.' '.$comment->user->last_name }}
                                        @endif</span>
                                    <span>{{ Carbon::parse($comment->created_at)->diffForHumans() }}</span></p>
                            </div>
                            <div class="content mt-1">
                                <div class="comment-content">{!! $comment->content !!}</div>
                                <div class="d-flex flex-row">
                                    <a title="beantwoorden"
                                       class="btn d-flex align-items-center"><span
                                            class="material-symbols-rounded">add_comment</span></a>
                                    @if($comment->user->id === Auth::id())
                                        <a
                                            class="edit-button btn d-flex align-items-center edit"><span
                                                class="material-symbols-rounded">edit</span></a>
                                    @endif
                                    @if($comment->user->id === Auth::id() || (auth()->user()->roles->contains('role', 'Dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')|| auth()->user()->roles->contains('role', 'Ouderraad')))
                                        <a class="delete-button btn d-flex align-items-center"
                                           data-id="{{ $post->id }}"
                                           data-name="je reactie"
                                           data-link="{{ route('dolfijnen.comment.delete', ['id' => $comment->id, 'postId' => $post->id]) }}">
                                            <span class="material-symbols-rounded">delete</span>
                                        </a>

                                    @endif
                                </div>
                            </div>

                            @if($comment->user->id === Auth::id())
                                    <form class="editable-content mt-2" style="display: none" onsubmit="e.preventDefault()">
                                        @csrf <!-- Add CSRF token field if not already present -->
                                        @method('PUT') <!-- Add method field if not already present -->
                                        <div class="text-input" id="text-input"
                                             contenteditable="true">{!! $comment->content !!}</div>
                                        <small id="characters"></small>

                                        @error('content')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror

                                        <div class="d-flex flex-row">
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
                </div>
            @endforeach
        </div>

    </div>
@endsection
