@extends('layouts.app')

@vite('resources/js/home.js')

@section('content')
    <div class="header" style="background-image: url({{ asset('files/lessons/banner.jpg') }})">
        <div>
            <p class="header-title">Lessen</p>
            <p class="header-text">Welkom op de MHG Leeromgeving!</p>
        </div>
    </div>
    <div class="container col-md-11">
        <div class="d-flex flex-row-responsive mb-2 justify-content-between align-items-center">
            <div>
                <h1>Lessen</h1>
                <p>Kies een les uit waarvan je het materiaal wilt bekijken</p>
            </div>
            <div>
                @if($user->roles->contains('role', 'Praktijkbegeleider'))
                    <a href="{{ route('lessons.new') }}"
                       class="d-flex flex-row align-items-center justify-content-center btn btn-info">
                            <span
                                class="material-symbols-rounded me-2">add</span>
                        <span>Nieuwe lesomgeving</span></a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(count($lessons) > 0)

        <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">

            @foreach($lessons as $lesson)
                <a class="text-decoration-none" href="{{ route('lessons.environment.lesson', $lesson->id) }}">
                <div class="card">
                    @if($lesson->image)
                        <img alt="foto les" class="card-img-top"
                             src="{{ asset('files/lessons/lesson-images/' . $lesson->image) }}">
                    @else
                        <div class="card-img-top bg-info"></div>
                    @endif
                    <div class="card-body">
                        <h2 class="card-title">{{ $lesson->title }}</h2>
                        <p>{{ $lesson->description }}</p>
                    </div>
                </div>
                </a>
            @endforeach
        </div>
        @else
            <div class="alert alert-warning d-flex align-items-center mt-4" role="alert">
                <span class="material-symbols-rounded me-2">school</span>Nog geen lessen gevonden, waarschijnlijk ben je nog niet toegevoegd!
            </div>
        @endif
    </div>
@endsection
