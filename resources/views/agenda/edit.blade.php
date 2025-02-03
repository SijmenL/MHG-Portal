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
                @if(!isset($lesson))
                    <h1 class="">Activiteiten bewerken</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('agenda') }}">Agenda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Activiteiten bewerken</li>
                        </ol>
                    </nav>

                @if($user->roles->contains('role', 'Administratie'))
                        <p>Bewerk de activiteiten die door de vereniging zijn aangemaakt. Vanwege je administratie rol kun je alles bewerken.</p>
                    @else
                        <p>Bewerk de activiteiten die je hebt aangemaakt.</p>
                @endif

                @else
                    <h1 class="">Agendapunten bewerken</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('lessons.environment.lesson.planning', $lesson->id) }}">Planning</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Agendapunten bewerken</li>
                    </ol>
                @endif
                    <form id="auto-submit" method="GET" class="user-select-forum-submit">
                        <div class="d-flex">
                            <div class="d-flex flex-row-responsive gap-2 align-items-center mb-3 w-100">
                                <div class="input-group">
                                    <label for="search" class="input-group-text" id="basic-addon1">
                                        <span class="material-symbols-rounded">search</span></label>
                                    <input id="search" name="search" type="text" class="form-control"
                                           placeholder="Zoeken op activiteiten"
                                           aria-label="Zoeken" aria-describedby="basic-addon1" value="{{ $search }}"
                                           onchange="this.form.submit();">
                                </div>
                            </div>
                            @if(isset($lesson))
                                <input type="hidden" name="lessonId" value="{{$lesson->id}}">
                            @endif
                        </div>
                    </form>


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

                        <a href="@if(!isset($lesson)){{ route('agenda.edit.activity', [$activity->id]) }} @else {{ route('agenda.edit.activity', [$activity->id, 'lessonId' => $lesson->id]) }} @endif"
                           class="text-decoration-none"
                           style="color: unset">
                            <div class="d-flex flex-row">
                                <div style="width: 50px"
                                     class="d-flex flex-column gap-0 align-items-center justify-content-center">
                                    <p class="day-name">{{ mb_substr($activitiesStart->translatedFormat('l'), 0, 2) }}</p>
                                    <p class="day-number">{{ $activitiesStart->format('j') }}</p>
                                </div>
                                <div
                                    class="event mt-2 w-100 d-flex flex-row-responsive-reverse justify-content-between">
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

                    {{ $activities->links() }}
                @else
                    <div class="alert alert-warning d-flex align-items-center mt-4" role="alert">
                        <span class="material-symbols-rounded me-2">event_busy</span>Geen activiteiten gevonden die je
                        hebt aangemaakt...
                    </div>
                @endif

            </div>
        </div>
@endsection
