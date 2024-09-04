@extends('layouts.leiding')
@include('partials.editor')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">
        <h1>Bewerk post</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('leiding') }}">Leiding</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bewerk post</li>
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
                class="@if(isset($post->user) && $post->user->roles->contains('role', 'Afterloodsen Organisator') && $post->user->id !== Auth::id()) bg-danger-subtle @elseif($post->user->id === Auth::id()) bg-secondary-subtle @else bg-info @endif d-flex flex-row-responsive justify-content-center forum-post rounded">
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

                    <div class="d-flex w-100">
                        <div class="w-100 forum-content bg-white p-3 rounded">
                            <div class="editor-parent" style="min-height: 300px; position: relative">
                                @yield('editor')
                                <style>
                                    .options {
                                        top: 0 !important;
                                        position: sticky;
                                        z-index: 1000;
                                    }
                                </style>
                                <div id="text-input" style="min-height: 175px" contenteditable="true" class="text-input">{!! $post->content !!}</div>
                                <small id="characters"></small>

                            </div>
                            @error('content')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
            </div>
        </div>

        <div class="mt-1 bg-white p-2 rounded d-flex flex-row justify-content-between">
            <div class="d-flex flex-row">
                <form method="POST" action="{{ route('leiding.post.store', $post->id) }}">
                    @csrf
                    <input id="content" name="content" type="text" class="d-none">

                    <button type="submit" class="btn d-flex align-items-center"><span
                            class="material-symbols-rounded">save</span></button>
                </form>
                <a href="{{ route('leiding.post', $post->id) }}" class="btn d-flex align-items-center"><span
                        class="material-symbols-rounded">cancel</span></a>
            </div>

            <div class="d-flex flex-row">
                <a class="delete-button btn d-flex align-items-center"
                   data-id="{{ $post->id }}"
                   data-name="je post en alle reacties eronder"
                   data-link="{{ route('leiding.post.delete', $post->id) }}"><span
                        class="material-symbols-rounded">delete</span></a>
            </div>
        </div>

    </div>
@endsection
