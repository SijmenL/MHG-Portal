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
                <h1 class="">Mijn agenda</h1>

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
                        <input class="form-check-input" type="checkbox" role="switch" id="show-all" @if($wantViewAll === 'true') checked @endif>
                        <label class="form-check-label" for="show-all">Laat alle agenda punten van de vereniging zien</label>
                    </div>
                @endif

                <p>Welkom in jouw MHG Agenda! Hier vind je de komende activiteiten die voor jouw relevant zijn!</p>


                <script>
                    let showAll = document.getElementById('show-all')
                    showAll.addEventListener('change', function () {
                        // Get the current URL
                        const url = new URL(window.location.href);

                        // Update the 'all' parameter based on the checkbox state
                        url.searchParams.set('all', this.checked ? 'true' : 'false');

                        // Optionally redirect to the new URL
                        window.location.href = url.toString();

                        // Log the new URL (for debugging purposes)
                        console.log("New URL:", url.toString());
                    });
                </script>

                <div id="nav">
                    <ul class="nav nav-tabs flex-row-reverse mb-4">
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('agenda.month', ['month' => $monthOffset, 'all' => $wantViewAll]) }}#nav">
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
                <a href="{{ route('agenda.schedule', ['month' => $monthOffset - 1, 'all' => $wantViewAll]) }}#agenda"
                   class="btn d-flex align-items-center justify-content-center">
                    <span class="material-symbols-rounded">arrow_back_ios</span>
                </a>
                <a href="{{ route('agenda.schedule', ['month' => 0, 'all' => $wantViewAll]) }}#agenda"
                   class="btn d-flex align-items-center justify-content-center">
                    <span class="material-symbols-rounded">home</span>
                </a>
                <a href="{{ route('agenda.schedule', ['month' => $monthOffset + 1, 'all' => $wantViewAll]) }}#agenda"
                   class="btn d-flex align-items-center justify-content-center">
                    <span class="material-symbols-rounded">arrow_forward_ios</span>
                </a>
            </div>
            <div>
                <h2>{{ $monthName }} {{ $year }}</h2>
            </div>
        </div>

        @if(count($activities) > 0)
            @php
                $currentMonth = null;
            @endphp

            @foreach ($activities as $activity)
                @php
                    $activitiesStart = Carbon::parse($activity->date_start);
                    $activityEnd = Carbon::parse($activity->date_end);

                    $activityMonth = $activitiesStart->translatedFormat('F');
                @endphp

                @if($currentMonth !== $activityMonth)
                    @php
                        $currentMonth = $activityMonth
                    @endphp

                    <div class="d-flex flex-row w-100 align-items-center mt-4 mb-2">
                        <h4 class="month-devider">{{ $activitiesStart->translatedFormat('F') }}</h4>
                        <div class="month-devider-line"></div>
                    </div>
                @endif

                <a href="{{ route('agenda.activity', ['month' => $monthOffset, 'all' => $wantViewAll, 'view' => 'schedule', $activity->id]) }}" class="text-decoration-none"
                   style="color: unset">
                    <div class="d-flex flex-row">
                        <div style="width: 50px"
                             class="d-flex flex-column gap-0 align-items-center justify-content-center">
                            <p class="day-name">{{ mb_substr($activitiesStart->translatedFormat('l'), 0, 2) }}</p>
                            <p class="day-number">{{ $activitiesStart->format('j') }}</p>
                        </div>
                        <div class="event mt-2 w-100 d-flex flex-row-responsive-reverse justify-content-between">
                            <div class="d-flex flex-column justify-content-between">
                                <div>
                                    @if($activitiesStart->isSameDay($activityEnd))
                                        <p>{{ $activitiesStart->format('j') }} {{ $activitiesStart->translatedFormat('F') }}
                                            @ {{ $activitiesStart->format('H:i') }}
                                            - {{ $activityEnd->format('H:i') }}</p>
                                    @else
                                        <p>{{ $activitiesStart->format('d-m-Y') }}
                                            tot {{ $activityEnd->format('d-m-Y') }}</p>
                                    @endif
                                    <h3>{{ $activity->title }}</h3>
                                    <p><strong>{{ $activity->location }}</strong></p>
                                    <p>{{ \Str::limit(strip_tags(html_entity_decode($activity->content)), 300, '...') }}</p>
                                </div>
                                <div>
                                    @if(isset($activity->price))
                                        @if($activity->price !== '0')
                                            <p><strong>{{ $activity->price }}</strong></p>
                                        @else
                                            <p><strong>gratis</strong></p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @if($activity->image)
                                <div class="d-flex align-items-center justify-content-center p-2">
                                    <img class="event-image" alt="Activiteit Afbeelding"
                                         src="{{ asset('files/agenda/agenda_images/'.$activity->image) }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <div class="alert alert-warning d-flex align-items-center mt-4" role="alert">
                <span class="material-symbols-rounded me-2">event_busy</span>Geen activiteiten gevonden...
            </div>
        @endif

    </div>
@endsection
