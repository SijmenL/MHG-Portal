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
        <h1>Geplande activiteit</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
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
                    <li class="breadcrumb-item"><a href="{{ route('agenda') }}">Agenda</a></li>
                @endif
                <li class="breadcrumb-item"><a
                        @if($view === 'month') href="{{ route('agenda.month', ['month' => $month, 'all' => $wantViewAll]) }}"
                        @else href="{{ route('agenda.schedule', ['month' => $month, 'all' => $wantViewAll]) }}" @endif>Mijn
                        agenda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Geplande activiteit</li>
            </ol>
        </nav>

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
            <div class="p-5 w-100 d-flex align-items-center flex-column">
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

                <div class="mt-4 w-100" style="align-self: start">{!! $activity->content !!}</div>
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
                    <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">emoji_people</span>Aanwezigheid
                    </h2>
                    <p>Meld je hier aan of af voor {{ $activity->title }}.</p>

                    @if($presenceStatus === "0")
                        <p style="font-weight: bolder">Je hebt je afgemeld!</p>
                    @endif
                    @if($presenceStatus === "1")
                        <p style="font-weight: bolder">Je hebt je aangemeld!</p>
                    @endif

                    <div class="d-flex flex-row-responsive gap-2">

                        <a @if($presenceStatus !== "1") href="{{ route('agenda.activity.present', $activity->id) }}" @endif class="d-flex flex-row align-items-center justify-content-center btn @if($presenceStatus === "1") btn-success @else btn-outline-success @endif">
                            <span class="material-symbols-rounded me-2">event_available</span>
                            <span>Aanmelden</span>
                        </a>
                        <a @if($presenceStatus !== "0") href="{{ route('agenda.activity.absent', $activity->id) }}" @endif class="d-flex flex-row align-items-center justify-content-center btn @if($presenceStatus === "0") btn-danger @else btn-outline-danger @endif">
                            <span
                                class="material-symbols-rounded me-2">event_busy</span>
                            <span>Afmelden</span></a>
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

                        <button type="submit" class="btn btn-primary text-white mt-3">Opslaan</button>
                    </form>
                </div>
            @endif

        </div>

    </div>
@endsection
