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

                @if($user &&
                  ($user->roles->contains('role', 'Dolfijnen Leiding') ||
                  $user->roles->contains('role', 'Zeeverkenners Leiding') ||
                  $user->roles->contains('role', 'Loodsen Stamoudste') ||
                  $user->roles->contains('role', 'Afterloodsen Organisator') ||
                  $user->roles->contains('role', 'Administratie') ||
                  $user->roles->contains('role', 'Bestuur') ||
                  $user->roles->contains('role', 'Praktijkbegeleider') ||
                  $user->roles->contains('role', 'Loodsen Mentor') ||
                  $user->roles->contains('role', 'Ouderraad'))
                  )
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('agenda') }}">Agenda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mijn agenda</li>
                        </ol>
                    </nav>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="show-all"
                               @if($wantViewAll === 'true') checked @endif>
                        <label class="form-check-label" for="show-all">Laat alle agenda punten van de vereniging zien</label>
                    </div>
                @endif

                <p>Welkom in jouw MHG Agenda! Hier vind je de komende activiteiten die voor jouw relevant zijn!</p>

                <script>
                    let showAll = document.getElementById('show-all');
                    showAll.addEventListener('change', function () {
                        const url = new URL(window.location.href);
                        url.searchParams.set('all', this.checked ? 'true' : 'false');
                        window.location.href = url.toString();
                        console.log("New URL:", url.toString());
                    });
                </script>

                <div id="nav">
                    <ul class="nav nav-tabs flex-row-reverse mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"><span class="material-symbols-rounded"
                                                                                 style="transform: translateY(5px)">calendar_view_month</span>
                                Maand</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('agenda.schedule', ['month' => $monthOffset, 'all' => $wantViewAll]) }}#nav"><span
                                    class="material-symbols-rounded"
                                    style="transform: translateY(5px)">calendar_today</span> Planning</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="agenda">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex flex-row gap-0">
                    <a href="{{ route('agenda.month', ['month' => $monthOffset - 1, 'all' => $wantViewAll]) }}#nav"
                       class="btn d-flex align-items-center justify-content-center">
                        <span class="material-symbols-rounded">arrow_back_ios</span>
                    </a>
                    <a href="{{ route('agenda.month', ['month' => 0, 'all' => $wantViewAll]) }}#nav"
                       class="btn d-flex align-items-center justify-content-center">
                        <span class="material-symbols-rounded">home</span>
                    </a>
                    <a href="{{ route('agenda.month', ['month' => $monthOffset + 1, 'all' => $wantViewAll]) }}#nav"
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
                    $globalRowTracker = [];
                    $weekEventCounts = [];
                    $currentWeek = 0;

                    for ($i = 1 - $firstDayOfWeek; $i <= $daysInMonth; $i++) {
                        if ($i > 0) {
                            $today = Carbon::create($year, $month, $i)->startOfDay();
                            $activitiesForDay = $activities->filter(function ($activity) use ($today) {
                                $start = Carbon::parse($activity->date_start)->startOfDay();
                                $end = Carbon::parse($activity->date_end)->endOfDay();
                                return $today->between($start, $end);
                            });

                            $weekEventCounts[$currentWeek] = max($weekEventCounts[$currentWeek] ?? 0, $activitiesForDay->count());
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
                    $rowPositions = [];
                @endphp

                @for ($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $today = Carbon::create($year, $month, $i)->startOfDay();
                        $activitiesForDay = $activities->filter(function ($activity) use ($today) {
                            $start = Carbon::parse($activity->date_start)->startOfDay();
                            $end = Carbon::parse($activity->date_end)->endOfDay();
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

                        @if ($activitiesForDay->isNotEmpty())
                            @foreach ($activitiesForDay as $activity)
                                @php
                                    $start = Carbon::parse($activity->date_start)->startOfDay();
                                    $end = Carbon::parse($activity->date_end)->endOfDay();
                                    $isFirstDay = $today->isSameDay($start);
                                    $isLastDay = $today->isSameDay($end);

                                    $activityClass = 'calendar-event';
                                    if ($isFirstDay && $isLastDay) {
                                        $activityClass .= ' calendar-event-single';
                                    } else {
                                        if ($isFirstDay) {
                                            $activityClass .= ' calendar-event-first';
                                        }
                                        if ($isLastDay) {
                                            $activityClass .= ' calendar-event-last';
                                        }
                                        if ($isMonday) {
                                            $activityClass .= ' calendar-event-monday';
                                        }
                                    }

                                    if ($activity->should_highlight) {
                                        $activityClass .= ' calendar-event-highlight';
                                    }

                                    $activityImage = $activity->image;
                                    $activityContent = $activity->content;
                                    $activityTitle = $activity->title;

                                    $activitiestart = Carbon::parse($activity->date_start);
                                    $activityEnd = Carbon::parse($activity->date_end);

                                    if ($activitiestart->isSameDay($activityEnd)) {
                                        $formattedStart = $activitiestart->format('H:i');
                                        $formattedEnd = $activityEnd->format('H:i');
                                    } else {
                                        $formattedStart = $activitiestart->format('d-m H:i');
                                        $formattedEnd = $activityEnd->format('d-m H:i');
                                    }
                                @endphp

                                <a href="{{ route('agenda.activity', ['month' => $monthOffset, 'all' => $wantViewAll, 'view' => 'month', $activity->id]) }}"
                                   style="top: {{ 40 + ($activityPositions[$activity->id] ?? 0) * 35 }}px;"

                                   data-event-id="{{ $activity->id }}"
                                   data-event-start="{{ $formattedStart }}"
                                   data-event-end="{{ $formattedEnd }}"
                                   @if(isset($activityImage))
                                       data-image="{{ asset('files/agenda/agenda_images/'.$activityImage) }}"
                                   @endif
                                   data-content="{{ \Str::limit(strip_tags(html_entity_decode($activityContent)), 200, '...') }}"
                                   data-title="{{ $activityTitle }}"
                                   class="{{ $activityClass }}"
                                >
                                    @if ($isFirstDay || ($isMonday && !$isLastDay))
                                        <div class="calendar-event-title">
                                            <p>{{ $activityTitle }} </p>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        @endif
                    </div>

                    @php
                        if (($i + $firstDayOfWeek) % 7 === 0) {
                            $currentWeek++;
                        }
                    @endphp
                @endfor

                @while(($daysInMonth + $firstDayOfWeek) % 7 != 0)
                    <div class="calendar-cell empty"></div>
                    @php($daysInMonth++)
                @endwhile
            </div>

            <div id="event-popup">
                <p><span id="date-start"></span> - <span id="date-end"></span></p>
                <h3 id="popup-title"></h3>
                <div id="popup-content"></div>
                <img id="popup-image" src="" alt="Agenda Afbeelding">
            </div>

        </div>
    </div>
@endsection
