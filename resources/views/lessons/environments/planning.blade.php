@extends('layouts.app')

@include('partials.editor')

@vite('resources/js/calendar.js')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-row-responsive align-items-center gap-5" style="width: 100%">
            <div class="" style="width: 100%;">
                <h1>Planning</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Planning</li>
                    </ol>
                </nav>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <p>Welkom in de les planning! Bekijk de volledige planning of maak een nieuw agendapunt aan!</p>

                <div class="bg-light rounded-2 p-3">
                    <h2>Acties</h2>
                    <div class="quick-action-bar">
                        <a class="btn btn-info quick-action" href="{{ route('agenda.month', ['lessonId' => $lesson->id]) }}">
                            <span class="material-symbols-rounded">event_upcoming</span>
                            <p>Bekijk de planning</p>
                        </a>
                        <a class="btn btn-info quick-action" href="{{ route('agenda.new', ['lessonId' => $lesson->id]) }}">
                            <span class="material-symbols-rounded">calendar_add_on</span>
                            <p>Nieuw agendapunt</p>
                        </a>
                        <a class="btn btn-secondary quick-action" href="{{ route('agenda.edit', ['lessonId' => $lesson->id]) }}">
                            <span class="material-symbols-rounded">edit_calendar</span>
                            <p>Agendapunten bewerken</p>
                        </a>
                        <a class="btn btn-secondary quick-action" href="{{ route('agenda.presence', ['lessonId' => $lesson->id]) }}">
                            <span class="material-symbols-rounded">perm_contact_calendar</span>
                            <p>Aanwezigheid</p>
                        </a>
                    </div>
                </div>

            </div>
        </div>
@endsection
