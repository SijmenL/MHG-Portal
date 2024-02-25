@extends('layouts.dolfijnen')
@include('partials.editor')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="header" style="background-image: url({{ asset('files/dolfijnen/kanos.webp') }})">
        <div>
            <p class="header-title">Dolfijnen</p>
            <p class="header-text">Welkom op de digitale omgeving van de Dolfijnen! </p>
        </div>
    </div>
    <div class="container col-md-11">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
            <div class="">
                <h1 class="">Opties</h1>
                <p>Voor nu zijn er een aantal opties in het portaal beschikbaar, je kunt op dit moment bijvoorbeeld de
                    leiding
                    bekijken. In de toekomst zal hier meer mogelijk zijn, zoals dingen
                    delen met je groep, je aan- of af melden voor groepdraaien & activiteiten en bijvoorbeeld de agenda
                    bekijken. Hou de omgeving dus goed in de gaten!</p>
                <div class="bg-light rounded-2 p-3">
                    <h2>Acties</h2>
                    <div class="quick-action-bar">

                        @if(auth()->user() && (auth()->user()->roles->contains('role', 'Dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')|| auth()->user()->roles->contains('role', 'Ouderraad')))
                            <a class="btn btn-info quick-action" href="{{ route('dolfijnen.groep') }}">
                                <span class="material-symbols-rounded">groups_2</span>
                                <p>Groep</p>
                            </a>
                        @endif
                        <a class="btn btn-info quick-action" href="{{ route('dolfijnen.leiding') }}">
                            <span class="material-symbols-rounded">supervisor_account</span>
                            <p>Leiding</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="">
                <img class="w-100" alt="groepsfoto" src="{{ asset('files/dolfijnen/dolfijnen.jpg') }}">
            </div>
        </div>

        <div class="mt-5">
            <h1>Prikbord</h1>
            <p>Deel iets met de Dolfijnen, hun ouders en de dolfijnenleiding!</p>

            <div class="bg-light rounded-2 p-3">
                <div class="container">
                    @yield('editor')

                    <div id="text-input" contenteditable="true">{!! old('content') !!}</div>
                    <small id="characters"></small>

                    @error('content')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror

                    <form method="POST" action="{{ route('dolfijnen.message-post') }}">
                        @csrf
                        <input id="content" name="content" type="text" class="d-none">

                        <button type="submit" class="btn btn-dark mt-3">Plaatsen</button>
                    </form>
                </div>
            </div>

            <div class="mt-5">
                @foreach($posts as $post)
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
                                <a class="text-decoration-none" style="color: unset" href="{{ route('dolfijnen.post', $post->id) }}">
                                <div style="overflow: auto"
                                     class="w-100 forum-content bg-white p-3 rounded">{!! $post->content !!}</div>
                                </a>
                                <div class="mt-1 bg-white p-2 rounded d-flex flex-row justify-content-between">
                                    <div class="d-flex flex-row">
                                        <a title="@foreach($post->likes as $like) {{ $like->user->name.' '.$like->user->infix.' '.$like->user->last_name }} @endforeach"
                                           class="btn d-flex align-items-center like-button {{ $post->likes->contains('user_id', auth()->id()) ? ' liked' : '' }}"
                                           data-post-id="{{ $post->id }}">{{ $post->likes->count() }} <span
                                                class="material-symbols-rounded">favorite</span></a>
                                        <a href="{{ route('dolfijnen.post', $post->id) }}" class="btn d-flex align-items-center comment">{{ $post->comments->count() }} <span
                                                class="material-symbols-rounded">chat</span></a>
                                    </div>

                                        <div class="d-flex flex-row">
                                            @if($post->user->id === Auth::id())
                                            <a href="{{ route('dolfijnen.post.edit', $post->id) }}" class="btn d-flex align-items-center edit"><span
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
        </div>
    </div>
@endsection
