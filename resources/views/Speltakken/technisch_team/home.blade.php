@extends('layouts.technisch_team')

@include('partials.editor')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="header" style="background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.5)), url({{ asset('files/technisch_team/Image-3.jpeg') }})">
        <div>
            <p class="header-title">Technisch Team</p>
            <p class="header-text">Welkom op de digitale omgeving van het Technisch Team! </p>
        </div>
    </div>
    <div class="container col-md-11">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
            <div class="w-100">
                <h1 class="">Opties</h1>
                <p>Welkom op het Technisch Team portaal!</p>

                <div class="bg-light rounded-2 p-3">
                    <h2>Acties</h2>
                    <div class="quick-action-bar">

                            <a class="btn btn-info quick-action"
                               href="{{ route('technisch_team.files') }}">
                                <span class="material-symbols-rounded">folder_open</span>
                                <p>Bestanden</p>
                            </a>

                        @if(auth()->user() && (auth()->user()->roles->contains('role', 'Hoofd Technisch Team') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')|| auth()->user()->roles->contains('role', 'Ouderraad')))


                            <a class="btn btn-info quick-action" href="{{ route('technisch_team.groep') }}">
                                <span class="material-symbols-rounded">groups_2</span>
                                <p>Groep</p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5" id="posts">
            <h1>Prikbord</h1>
            <p>Deel iets met het Technisch Team!</p>

            <div class="bg-light rounded-2 p-3">
                <div class="container">
                    <div class="editor-parent">
                        @yield('editor')

                        <div id="text-input" contenteditable="true" class="text-input">{!! old('content') !!}</div>
                        <small id="characters"></small>
                    </div>
                    @error('content')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror

                    <form method="POST" action="{{ route('technisch_team.message-post') }}">
                        @csrf
                        <input id="content" name="content" type="text" class="d-none">

                        <button
                            onclick="function handleButtonClick(button) {
                                 button.disabled = true;
                                button.classList.add('loading');

                                // Show the spinner and hide the text
                                button.querySelector('.button-text').style.display = 'none';
                                button.querySelector('.loading-spinner').style.display = 'inline-block';
                                button.querySelector('.loading-text').style.display = 'inline-block';

                                button.closest('form').submit();
                            }
                            handleButtonClick(this)"
                            class="btn btn-dark mt-3 flex flex-row align-items-center justify-content-center">
                            <span class="button-text">Plaatsen</span>
                            <span style="display: none" class="loading-spinner spinner-border spinner-border-sm" aria-hidden="true"></span>
                            <span style="display: none" class="loading-text" role="status">Laden...</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-5">
                @if($posts->count() > 0)
                    @foreach($posts as $post)
                        <div id="{{$post->id}}" class="mt-5 rounded bg-white">
                            <div
                                class="@if(isset($post->user) && $post->user->roles->contains('role', 'Hoofd Technisch Team') && $post->user->id !== Auth::id()) bg-danger-subtle @elseif($post->user->id == Auth::id()) bg-secondary-subtle @else bg-info @endif d-flex flex-row-responsive justify-content-center forum-post rounded">
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
                                        <p class="text-center">{{ Carbon::parse($post->created_at)->diffForHumans() }}
                                            geplaatst</p>
                                        @if ($post->updated_at->format('Y-m-d H:i:s') !== $post->created_at->format('Y-m-d H:i:s'))
                                            <p class="text-center">{{ Carbon::parse($post->updated_at)->diffForHumans() }}
                                                bewerkt</p>
                                        @endif

                                    </div>

                                    <span class="message-arrow"></span>
                                @endif

                                <div class="d-flex flex-column w-100">
                                    <a class="text-decoration-none" style="color: unset"
                                       href="{{ route('technisch_team.post', $post->id) }}">
                                        <div style="overflow: auto"
                                             class="w-100 forum-content bg-white p-3 rounded">{!! $post->content !!}</div>
                                    </a>
                                    <div class="mt-1 bg-white p-2 rounded d-flex flex-row justify-content-between">
                                        <div class="d-flex flex-row">
                                            <a title="@foreach($post->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                               class="btn d-flex align-items-center like-button {{ $post->likes->contains('user_id', auth()->id()) ? ' liked' : '' }}"
                                               data-post-id="{{ $post->id }}"
                                               data-post-type="0">{{ $post->likes->count() }} <span
                                                    class="material-symbols-rounded">favorite</span></a>
                                            <a href="{{ route('technisch_team.post', $post->id) }}#comments"
                                               class="btn d-flex align-items-center comment">
                                                {{ $post->comments->count() }}
                                                <span class="material-symbols-rounded">chat</span>
                                            </a>

                                        </div>

                                        <div class="d-flex flex-row">
                                            @if($post->user->id == Auth::id())
                                                <a href="{{ route('technisch_team.post.edit', $post->id) }}"
                                                   class="btn d-flex align-items-center edit"><span
                                                        class="material-symbols-rounded">edit</span></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    @endforeach
            </div>
            <div class="mt-4">
                {{ $posts->links() }}
            </div>
            @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <span class="material-symbols-rounded me-2">post</span>Geen prikbord posts gevonden...
                </div>
            @endif
        </div>
    </div>
@endsection
