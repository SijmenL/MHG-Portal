@extends('layouts.contact')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <script>
        function sendHeight() {
            const height = document.body.getBoundingClientRect().height;
            console.log('Sending height:', height);
            window.parent.postMessage(height, 'https://waterscoutingmhg.nl'); // Ensure this matches the parent origin
        }

        window.addEventListener('message', (event) => {
            console.log('Message received from:', event.origin);
            if (event.origin === 'https://waterscoutingmhg.nl' && event.data === 'getHeight') {
                console.log('Valid request for height');
                sendHeight();
            }
        });


        if (window.top === window.self) {
            // Redirect to the parent page if the child page is accessed directly
            window.location.href = `https://waterscoutingmhg.nl/over-onze-club/activiteiten`;
        } else {
            sendHeight();  // Send height on load in case of initial message issue
        }

    </script>

    <script>
        function breakOut(id) {
            window.parent.location.href = `https://waterscoutingmhg.nl/over-onze-club/activiteit?id=${id}&view=schedule`;
        }
    </script>

    @if($limit === null)
        <div>
            @else
                <div class="bg-info">
                    @endif
                    <div class="d-flex flex-row-responsive align-items-center gap-5" style="width: 100%">
                        <div class="" style="width: 100%;">


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

                            @if($limit === null)
                                <div id="nav">
                                    <ul class="nav nav-tabs flex-row-reverse mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link"
                                               href="{{ route('agenda.public.month', ['month' => $monthOffset]) }}#nav">
                                                <span class="material-symbols-rounded"
                                                      style="transform: translateY(5px)">calendar_view_month</span>
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
                            @endif
                        </div>
                    </div>

                    @if($limit === null)
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-row gap-0">
                                <a href="{{ route('agenda.public.schedule', ['month' => $monthOffset - 1]) }}#agenda"
                                   class="btn d-flex align-items-center justify-content-center">
                                    <span class="material-symbols-rounded">arrow_back_ios</span>
                                </a>
                                <a href="{{ route('agenda.public.schedule', ['month' => 0]) }}#agenda"
                                   class="btn d-flex align-items-center justify-content-center">
                                    <span class="material-symbols-rounded">home</span>
                                </a>
                                <a href="{{ route('agenda.public.schedule', ['month' => $monthOffset + 1]) }}#agenda"
                                   class="btn d-flex align-items-center justify-content-center">
                                    <span class="material-symbols-rounded">arrow_forward_ios</span>
                                </a>
                            </div>
                            <div>
                                <h2>{{ $monthName }} {{ $year }}</h2>
                            </div>
                        </div>
                    @endif

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

                            <a onclick="breakOut({{ $activity->id }})"
                               class="text-decoration-none"
                               style="color: unset; cursor: pointer">
                                <div class="d-flex flex-row">
                                    <div style="width: 50px"
                                         class="d-flex flex-column gap-0 align-items-center justify-content-center">
                                        <p class="day-name">{{ mb_substr($activitiesStart->translatedFormat('l'), 0, 2) }}</p>
                                        <p class="day-number">{{ $activitiesStart->format('j') }}</p>
                                    </div>
                                    <div
                                        class="event @if($limit === null) bg-light @else bg-white @endif mt-2 w-100 d-flex flex-row-responsive-reverse justify-content-between">
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
