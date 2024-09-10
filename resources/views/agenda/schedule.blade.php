@extends('layouts.app')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="header" style="background-image: url({{ asset('files/agenda/banner.jpg') }})">
        <div>
            <p class="header-title">Agenda</p>
        </div>
    </div>
    <div class="container col-md-11">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-row-responsive align-items-center gap-5" style="width: 100%">
            <div class="" style="width: 100%;">
                <h1 class="">Aankomende Activiteiten</h1>
                <p>Welkom in jouw MHG Agenda! Hier vind je de komende activiteiten die voor jouw relevant zijn!</p>
                <div id="nav">
                    <ul class="nav nav-tabs flex-row-reverse mb-4">
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('agenda.month', ['month' => $monthOffset, 'day' => $dayOffset]) }}#nav">
                                <span class="material-symbols-rounded" style="transform: translateY(5px)">calendar_view_month</span>
                                Maand
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page">
                                <span class="material-symbols-rounded"
                                      style="transform: translateY(5px)">calendar_today</span> Planning
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex flex-row gap-0">
                <a href="{{ route('agenda.schedule', ['month' => $monthOffset - 1]) }}#agenda"
                   class="btn d-flex align-items-center justify-content-center">
                    <span class="material-symbols-rounded">arrow_back_ios</span>
                </a>
                <a href="{{ route('agenda.schedule', ['month' => 0]) }}#agenda"
                   class="btn d-flex align-items-center justify-content-center">
                    <span class="material-symbols-rounded">home</span>
                </a>
                <a href="{{ route('agenda.schedule', ['month' => $monthOffset + 1]) }}#agenda"
                   class="btn d-flex align-items-center justify-content-center">
                    <span class="material-symbols-rounded">arrow_forward_ios</span>
                </a>
            </div>
            <div>
                <h2>{{ $monthName }} {{ $year }}</h2>
            </div>
        </div>

            @if(count($events) > 0)
        @php
            $currentMonth = null;
        @endphp

        @foreach ($events as $event)
            @php
                $eventStart = Carbon::parse($event->date_start);
                $eventEnd = Carbon::parse($event->date_end);

                $eventMonth = $eventStart->translatedFormat('F');
            @endphp

            @if($currentMonth !== $eventMonth)
                @php
                    $currentMonth = $eventMonth
                @endphp

                <div class="d-flex flex-row w-100 align-items-center mt-4 mb-2">
                    <h4 class="month-devider">{{ $eventStart->translatedFormat('F') }}</h4>
                    <div class="month-devider-line"></div>
                </div>
            @endif

            <div class="d-flex flex-row">
                <div style="width: 50px" class="d-flex flex-column gap-0 align-items-center justify-content-center">
                    <p class="day-name">{{ mb_substr($eventStart->translatedFormat('l'), 0, 2) }}</p>
                    <p class="day-number">{{ $eventStart->format('j') }}</p>
                </div>
                <div class="event mt-2 w-100 d-flex flex-row-responsive-reverse justify-content-between">
                    <div class="d-flex flex-column justify-content-between">
                        <div>
                            @if($eventStart->isSameDay($eventEnd))
                                <p>{{ $eventStart->format('j') }} {{ $eventStart->translatedFormat('F') }}
                                    @ {{ $eventStart->format('H:i') }} - {{ $eventEnd->format('H:i') }}</p>
                            @else
                                <p>{{ $eventStart->format('d-m-Y') }} tot {{ $eventEnd->format('d-m-Y') }}</p>
                            @endif
                            <h3>{{ $event->title }}</h3>
                            <p><strong>{{ $event->location }}</strong></p>
                            <p>{{ \Str::limit(strip_tags(html_entity_decode($event->content)), 300, '...') }}</p>
                        </div>
                        <div>
                            @if(isset($event->price))
                                @if($event->price !== '0')
                                    <p><strong>{{ $event->price }}</strong></p>
                                @else
                                    <p><strong>gratis</strong></p>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if($event->image)
                        <div class="d-flex align-items-center justify-content-center p-2">
                            <img class="event-image" alt="Activiteit Afbeelding"
                                 src="{{ asset('files/agenda/agenda_images/'.$event->image) }}">
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
            @else
                <div class="alert alert-warning d-flex align-items-center mt-4" role="alert">
                    <span class="material-symbols-rounded me-2">event_busy</span>Geen activiteiten gevonden...
                </div>
            @endif

    </div>
@endsection
