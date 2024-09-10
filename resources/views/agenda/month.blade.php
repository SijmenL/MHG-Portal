@extends('layouts.app')

@include('partials.editor')

@vite('resources/js/calendar.js')

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
                <h1 class="">Mijn Agenda</h1>
                <p>Welkom in jouw MHG Agenda! Hier vind je de komende activiteiten die voor jouw relevant zijn!</p>
                <div id="nav">
                    <ul class="nav nav-tabs flex-row-reverse mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"><span class="material-symbols-rounded" style="transform: translateY(5px)">calendar_view_month</span> Maand</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('agenda.schedule', ['month' => $monthOffset, 'day' => $dayOffset]) }}#nav"><span class="material-symbols-rounded" style="transform: translateY(5px)">calendar_today</span> Planning</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="agenda">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex flex-row gap-0">
                    <a href="{{ route('agenda.month', ['month' => $monthOffset - 1, 'day' => $dayOffset]) }}#agenda"
                       class="btn d-flex align-items-center justify-content-center">
                        <span class="material-symbols-rounded">arrow_back_ios</span>
                    </a>
                    <a href="{{ route('agenda.month', ['month' => 0, 'day' => 0]) }}#agenda"
                       class="btn d-flex align-items-center justify-content-center">
                        <span class="material-symbols-rounded">home</span>
                    </a>
                    <a href="{{ route('agenda.month', ['month' => $monthOffset + 1, 'day' => $dayOffset]) }}#agenda"
                       class="btn d-flex align-items-center justify-content-center">
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </a>

                </div>
                <div>
                    <h2>{{$monthName}} {{$year}}</h2>
                </div>
            </div>
            <div class="calendar-grid">
                <div class="calendar-day">MA</div>
                <div class="calendar-day">DI</div>
                <div class="calendar-day">WO</div>
                <div class="calendar-day">DO</div>
                <div class="calendar-day">VR</div>
                <div class="calendar-day">ZA</div>
                <div class="calendar-day">ZO</div>

                @php
                    $weekEventCounts = [];
                    $currentWeek = 0;

                    // Step 1: Calculate the maximum number of events per week
                    for ($i = 1 - $firstDayOfWeek; $i <= $daysInMonth; $i++) {
                        if ($i > 0) {
                            $today = Carbon::create($year, $month, $i)->startOfDay();
                            $eventsForDay = $events->filter(function ($event) use ($today) {
                                $start = Carbon::parse($event->date_start)->startOfDay();
                                $end = Carbon::parse($event->date_end)->endOfDay();
                                return $today->between($start, $end);
                            });

                            $weekEventCounts[$currentWeek] = max($weekEventCounts[$currentWeek] ?? 0, $eventsForDay->count());
                        }

                        if (($i + $firstDayOfWeek) % 7 === 0) {
                            $currentWeek++;
                        }
                    }

                    $currentWeek = 0;
                @endphp

                @for ($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="calendar-cell empty"></div>
                @endfor

                @php
                    $eventPositions = []; // Initialize an array to keep track of event positions
                @endphp

                @for ($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $today = Carbon::create($year, $month, $i)->startOfDay();
                        $eventsForDay = $events->filter(function ($event) use ($today) {
                            $start = Carbon::parse($event->date_start)->startOfDay();
                            $end = Carbon::parse($event->date_end)->endOfDay();
                            return $today->between($start, $end);
                        });

                        $maxEventsInWeek = $weekEventCounts[$currentWeek] ?? 0;
                        $baseHeight = 100;
                        $additionalHeight = 25 * $maxEventsInWeek;
                        $totalHeight = $baseHeight + $additionalHeight;

                        $isMonday = $today->isMonday();
                    @endphp

                    <div
                        class="calendar-cell {{ $i == $currentDay && $month == $currentMonth && $year == $currentYear ? 'highlight' : '' }}"
                        style="height: {{ $totalHeight }}px;">
                        <p class="calendar-cell-text">{{ $i }}</p>

                        @if ($eventsForDay->isNotEmpty())
                            @foreach ($eventsForDay as $event)
                                @php
                                    $start = Carbon::parse($event->date_start)->startOfDay();
                                    $end = Carbon::parse($event->date_end)->endOfDay();
                                    $isFirstDay = $today->isSameDay($start);
                                    $isLastDay = $today->isSameDay($end);

                                    // Find the position of the event in the eventPositions array
                                    if (!isset($eventPositions[$event->id])) {
                                        $position = 0;
                                        foreach ($eventPositions as $eventId => $pos) {
                                            $stackEnd = Carbon::parse($events->find($eventId)->date_end)->endOfDay();
                                            if ($today->gt($stackEnd)) {
                                                $position = $pos;
                                                break;
                                            } else {
                                                $position++;
                                            }
                                        }
                                        $eventPositions[$event->id] = $position;
                                    } else {
                                        $position = $eventPositions[$event->id];
                                    }

                                    $eventClass = 'calendar-event';
                                    if ($isFirstDay && $isLastDay) {
                                        $eventClass .= ' calendar-event-single';
                                    } else {
                                        if ($isFirstDay) {
                                            $eventClass .= ' calendar-event-first';
                                        }
                                        if ($isLastDay) {
                                            $eventClass .= ' calendar-event-last';
                                        }
                                        if ($isMonday) {
                                            $eventClass .= ' calendar-event-monday';
                                        }
                                    }

                                    $eventImage = $event->image;
                                    $eventContent = $event->content;
                                    $eventTitle = $event->title;

                                    $eventStart = Carbon::parse($event->date_start);
                                    $eventEnd = Carbon::parse($event->date_end);

                                    if ($eventStart->isSameDay($eventEnd)) {
                                        $formattedStart = $eventStart->format('H:i');
                                        $formattedEnd = $eventEnd->format('H:i');
                                    } else {
                                        $formattedStart = $eventStart->format('d-m H:i');
                                        $formattedEnd = $eventEnd->format('d-m H:i');
                                    }
                                @endphp
                                <div
                                    data-event-id="{{ $event->id }}"
                                    data-event-start="{{ $formattedStart }}"
                                    data-event-end="{{ $formattedEnd }}"
                                    @if(isset($eventImage))
                                        data-image="{{ asset('files/agenda/agenda_images/'.$eventImage) }}"
                                    @endif
                                    data-content="{{ \Str::limit(strip_tags(html_entity_decode($eventContent)), 200, '...') }}"
                                    data-title="{{ $eventTitle }}"
                                    class="{{ $eventClass }}"
                                    style="top: {{ 40 + $position * 35 }}px;"
                                >
                                    @if ($isFirstDay || ($isMonday && !$isLastDay))
                                        <div class="calendar-event-title">
                                            {{ $eventTitle }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>

                    @php
                        if (($i + $firstDayOfWeek) % 7 === 0) {
                            $currentWeek++;
                        }

                        // After processing the day's events, remove events that have ended on this day
                        foreach ($eventPositions as $eventId => $pos) {
                            $eventItem = $events->find($eventId);
                            if ($eventItem && Carbon::parse($eventItem->date_end)->isSameDay($today)) {
                                unset($eventPositions[$eventId]);
                            }
                        }
                    @endphp
                @endfor

                @while(($daysInMonth + $firstDayOfWeek) % 7 != 0)
                    <div class="calendar-cell empty"></div>
                    @php($daysInMonth++)
                @endwhile
            </div>

            <div id="popup-overlay" class="popup"></div>
            <div id="event-popup">
                <p><span id="date-start"></span> - <span id="date-end"></span></p>
                <h3 id="popup-title"></h3>
                <div id="popup-content"></div>
                <img id="popup-image" src="" alt="Agenda Afbeelding">
            </div>

        </div>
    </div>
@endsection
