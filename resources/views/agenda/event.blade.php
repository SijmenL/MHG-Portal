@extends('layouts.app')
@include('partials.editor')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@php
    $eventStart = Carbon::parse($activity->date_start);
    $eventEnd = Carbon::parse($activity->date_end);

    $eventMonth = $eventStart->translatedFormat('F');
@endphp

@section('content')
    <div class="container col-md-11">
        <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="d-flex flex-column gap-3">
                @if(isset($lesson))
                    <h1>Gepland agendapunt</h1>
                @else
                    <h1>Geplande activiteit</h1>
                @endif

            </div>
            <div>
                @if($activity->user_id === \Illuminate\Support\Facades\Auth::id() || (isset($lesson) && $isTeacher))
                    <a href="@if(!isset($lesson)){{ route('agenda.edit.activity', $activity->id) }} @else{{ route('agenda.edit.activity', [$activity->id, 'lessonId' => $lesson->id]) }} @endif"
                       class="d-flex flex-row align-items-center justify-content-center btn btn-info">
                            <span
                                class="material-symbols-rounded me-2">edit</span>
                        <span>Bewerk activiteit</span></a>
                @endif
            </div>
        </div>
        @if(isset($lesson))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a>
                    </li>
                    @if($isTeacher)
                        <li class="breadcrumb-item"><a
                                href="{{ route('lessons.environment.lesson.planning', $lesson->id) }}">Planning</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item"><a
                            @if($view === 'month') href="{{ route('agenda.month', ['month' => $month, 'all' => $wantViewAll, 'lessonId' => $lesson->id]) }}"
                            @else href="{{ route('agenda.schedule', ['month' => $month, 'all' => $wantViewAll, 'lessonId' => $lesson->id]) }}" @endif>Les
                            planning</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gepland agendapunt</li>
                </ol>
            </nav>
        @else
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @if($user &&
                      ($user->roles->contains('role', 'Dolfijnen Leiding') ||
                      $user->roles->contains('role', 'Zeeverkenners Leiding') ||
                      $user->roles->contains('role', 'Loodsen Stamoudste') ||
                      $user->roles->contains('role', 'Loods') ||
                      $user->roles->contains('role', 'Afterloods') ||
                      $user->roles->contains('role', 'Afterloodsen Organisator') ||
                      $user->roles->contains('role', 'Administratie') ||
                      $user->roles->contains('role', 'Bestuur') ||
                      $user->roles->contains('role', 'Praktijkbegeleider') ||
                      $user->roles->contains('role', 'Loodsen Mentor') ||
                      $user->roles->contains('role', 'Ouderraad'))
                      )
                        <li class="breadcrumb-item"><a href="{{ route('agenda') }}">Agenda</a></li>
                    @endif
                    <li class="breadcrumb-item"><a
                            @if($view === 'month') href="{{ route('agenda.month', ['month' => $month, 'all' => $wantViewAll]) }}"
                            @else href="{{ route('agenda.schedule', ['month' => $month, 'all' => $wantViewAll]) }}" @endif>Mijn
                            agenda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Geplande activiteit</li>
                </ol>
            </nav>
        @endif

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

        <div class="bg-light rounded-2 w-100 d-flex flex-column align-items-center">
            <div class="p-3 w-100 d-flex align-items-center flex-column">
                <h1 class="text-center">{{ $activity->title }}</h1>
                @if($eventStart->isSameDay($eventEnd))
                    <h2 class="text-center">{{ $eventStart->format('j') }} {{ $eventStart->translatedFormat('F') }}
                        @ {{ $eventStart->format('H:i') }} - {{ $eventEnd->format('H:i') }}</h2>
                @else
                    <h2 class="text-center">{{ $eventStart->format('d-m-Y') }} tot {{ $eventEnd->format('d-m-Y') }}</h2>
                @endif
                @if(isset($activity->price))
                    @if($activity->price > 0)
                        <h3 class="text-center">€{{ $activity->price }}</h3>
                    @else
                        <h3 class="text-center">Deze activiteit is gratis!</h3>
                    @endif
                @endif
                @if(isset($activity->image))
                    <img class="mt-3 zoomable-image"
                         style="width: 100%; max-width: 800px; object-fit: cover; object-position: center;"
                         alt="Activiteit Afbeelding"
                         src="{{ asset('files/agenda/agenda_images/'.$activity->image) }}">
                @endif

                <div class="mt-4 w-100 agenda-content" style="align-self: start">{!! $activity->content !!}</div>
            </div>


            <div class="bg-white w-100 p-4 rounded">
                <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">description</span>Beschrijving
                </h2>
                <div class="d-flex flex-row flex-wrap gap-5 justify-content-between">
                    <div>
                        <h4 class="mb-2">Gegevens</h4>
                        <div class="d-flex flex-column gap-1">
                            @if($eventStart->isSameDay($eventEnd))
                                <p class="m-0"><strong>Datum</strong></p>
                                <p class="m-0">{{ $eventStart->format('j') }} {{ $eventStart->translatedFormat('F') }} </p>
                                <p class="m-0"><strong>Tijd</strong></p>
                                <p class="m-0">{{ $eventStart->format('H:i') }} </p>
                            @else
                                <p class="m-0"><strong>Begin</strong></p>
                                <p class="m-0">{{ $eventStart->format('j') }} {{ $eventStart->translatedFormat('F') }}
                                    om {{ $eventStart->format('H:i') }}</p>
                                <p class="m-0"><strong>Einde</strong></p>
                                <p class="m-0">{{ $eventEnd->format('j') }} {{ $eventEnd->translatedFormat('F') }}
                                    om {{ $eventEnd->format('H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(isset($activity->location))
                        <div>
                            <h4 class="mb-2">Locatie</h4>
                            <p class="m-0">{{ $activity->location }}</p>
                        </div>
                    @endif
                    @if(isset($activity->organisator))
                        <div>
                            <h4 class="mb-2">Organisator</h4>
                            <p class="m-0">{{ $activity->organisator }}</p>
                        </div>
                    @endif
                    @if(isset($activity->price))
                        <div>
                            <h4 class="mb-2">Prijs</h4>
                            @if($activity->price > 0)
                                <p class="m-0">€{{ $activity->price }}</p>
                            @else
                                <p class="m-0">Deze activiteit is gratis!</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($activity->presence)
                <div class="bg-white w-100 p-4 rounded mt-3">
                    <h2 class="flex-row gap-3">
                        <span class="material-symbols-rounded me-2">emoji_people</span>Aanwezigheid
                    </h2>

                    @if($canAlwaysView)
                        <p>Meld je hier aan of af voor {{ $activity->title }}.</p>

                        <!-- Parent's own presence status -->
                        <div>
                            <p><strong>Jouw aanwezigheid:</strong></p>

                            <div class="d-flex flex-row-responsive gap-2">
                                <a
                                    @if($presenceStatus !== "1")
                                        href="{{ route('agenda.activity.present', [$activity->id, $user->id]) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                    @endif
                                    class="d-flex flex-row align-items-center justify-content-center btn @if($presenceStatus === "1") btn-success @else btn-outline-success @endif">
                                    <span class="material-symbols-rounded me-2">event_available</span>
                                    <span>Aanmelden</span>
                                </a>
                                <a
                                    @if($presenceStatus !== "0")
                                        href="{{ route('agenda.activity.absent', [$activity->id, $user->id]) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                    @endif
                                    class="d-flex flex-row align-items-center justify-content-center btn @if($presenceStatus === "0") btn-danger @else btn-outline-danger @endif">
                                    <span class="material-symbols-rounded me-2">event_busy</span>
                                    <span>Afmelden</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(count($allowedChildren) > 0)
                        <div class="bg-light-subtle rounded-2 p-2 mt-3">
                            <p class="">Meldt hier je kind(eren) aan of af voor deze activiteit</p>
                            @endif

                            @foreach($allowedChildren as $child)
                                <div class="mt-4 bg-light p-3 rounded-3">
                                    <p><strong>{{ $child->name }}'s aanwezigheid:</strong></p>

                                    <div class="d-flex flex-row-responsive gap-2">
                                        <a
                                            @if($child->presence_status !== "1")
                                                href="{{ route('agenda.activity.present', ['id' => $activity->id, 'user' => $child->id]) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                            @endif
                                            class="d-flex flex-row align-items-center justify-content-center btn @if($child->presence_status === "1") btn-success @else btn-outline-success @endif">
                                            <span class="material-symbols-rounded me-2">event_available</span>
                                            <span>Aanmelden</span>
                                        </a>
                                        <a
                                            @if($child->presence_status !== "0")
                                                href="{{ route('agenda.activity.absent', ['id' => $activity->id, 'user' => $child->id]) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                            @endif
                                            class="d-flex flex-row align-items-center justify-content-center btn @if($child->presence_status === "0") btn-danger @else btn-outline-danger @endif">
                                            <span class="material-symbols-rounded me-2">event_busy</span>
                                            <span>Afmelden</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex flex-row-responsive mt-4">
                                @if($user && $user->roles->contains(function ($role) {
     return in_array($role->role, [
         'Dolfijnen Leiding',
         'Zeeverkenners Leiding',
         'Loodsen Stamoudste',
         'Loods',
         'Afterloodsen Organisator',
         'Administratie',
         'Bestuur',
         'Praktijkbegeleider',
         'Loodsen Mentor',
         'Ouderraad'
     ]);
 }))
                                    @if(isset($lesson))
                                        @if($isTeacher === true)
                                            <a href="{{ route('agenda.presence.activity', [$activity->id, 'lessonId' => $lesson->id]) }}"
                                               class="d-flex flex-row align-items-center justify-content-center btn btn-info">
                                                <span class="material-symbols-rounded me-2">free_cancellation</span>
                                                <span>Bekijk alle aan- of afmeldingen</span>
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('agenda.presence.activity', $activity->id) }}"
                                           class="d-flex flex-row align-items-center justify-content-center btn btn-info">
                                            <span class="material-symbols-rounded me-2">free_cancellation</span>
                                            <span>Bekijk alle aan- of afmeldingen</span>
                                        </a>
                                    @endif
                                @endif

                            </div>
                        </div>
                </div>
            @endif



            @if($activity->formElements->count() > 0)
                <div class="bg-white w-100 p-4 rounded mt-3">
                    <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">app_registration</span>Inschrijfformulier
                    </h2>
                    <form action="{{ route('agenda.activity.submit', $activity->id) }}" method="POST">
                        @csrf

                        @foreach ($activity->formElements as $formElement)
                            @php
                                $options = $formElement->option_value ? explode(',', $formElement->option_value) : [];
                                $oldValue = old('form_elements.' . $formElement->id);
                            @endphp

                            <div class="form-group">
                                <label
                                    for="formElement{{ $formElement->id }}">{{ $formElement->label }} @if($formElement->is_required)
                                        <span class="required-form">*</span>
                                    @endif</label>

                                @switch($formElement->type)
                                    @case('text')
                                    @case('email')
                                    @case('number')
                                    @case('date')
                                        <input type="{{ $formElement->type }}"
                                               id="formElement{{ $formElement->id }}"
                                               name="form_elements[{{ $formElement->id }}]"
                                               class="form-control"
                                               value="{{ $oldValue ?? '' }}"
                                            {{ $formElement->is_required ? 'required' : '' }}>
                                        @break

                                    @case('select')
                                        <select id="formElement{{ $formElement->id }}"
                                                name="form_elements[{{ $formElement->id }}]"
                                                class="form-select w-100"
                                            {{ $formElement->is_required ? 'required' : '' }}>
                                            <option value="">Selecteer een optie</option>
                                            @foreach ($options as $option)
                                                <option value="{{ $option }}"
                                                    {{ $oldValue == $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('radio')
                                        @foreach ($options as $option)
                                            <div class="form-check">
                                                <input type="radio"
                                                       id="formElement{{ $formElement->id }}_{{ $loop->index }}"
                                                       name="form_elements[{{ $formElement->id }}]"
                                                       value="{{ $option }}"
                                                       class="form-check-input"
                                                    {{ $oldValue == $option ? 'checked' : '' }}
                                                    {{ $formElement->is_required ? 'required' : '' }}>
                                                <label for="formElement{{ $formElement->id }}_{{ $loop->index }}"
                                                       class="form-check-label">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                        @break

                                    @case('checkbox')
                                        @php
                                            $oldValues = is_array($oldValue) ? $oldValue : [];
                                        @endphp
                                        @foreach ($options as $option)
                                            <div class="form-check">
                                                <input type="checkbox"
                                                       id="formElement{{ $formElement->id }}_{{ $loop->index }}"
                                                       name="form_elements[{{ $formElement->id }}][]"
                                                       value="{{ $option }}"
                                                       class="form-check-input">
                                                <label for="formElement{{ $formElement->id }}_{{ $loop->index }}"
                                                       class="form-check-label">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                        @break
                                @endswitch

                                @if ($errors->has('form_elements.' . $formElement->id))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('form_elements.' . $formElement->id) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <button
                            onclick="function handleButtonClick(button) {
                                 button.disabled = true;
                                button.classList.add('loading');

                                // Show the spinner and hide the text
                                button.querySelector('.button-text').style.display = 'none';
                                button.querySelector('.loading-spinner').style.display = 'inline-block';
                                button.querySelector('.loading-text').style.display = 'inline-block';

                                button.closest('form').submit();
                            }
                            handleButtonClick(this)"
                            class="btn btn-success mt-3 flex flex-row align-items-center justify-content-center">
                            <span class="button-text">Opslaan</span>
                            <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                                  aria-hidden="true"></span>
                            <span style="display: none" class="loading-text" role="status">Laden...</span>
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>
@endsection

