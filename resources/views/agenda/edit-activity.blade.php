@extends('layouts.app')
@include('partials.editor')

@vite(['resources/js/texteditor.js', 'resources/js/search-user.js', 'resources/css/texteditor.css'])

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp



@section('content')
    <div id="popUp" class="popup" style="margin-top: -122px; display: none">
        <div class="popup-body">
            <div class="page">
                <h2>Inschrijfformulier</h2>
                <p>Je kan de volgende elementen toevoegen aan een inschrijfformulier:</p>
                <div class="d-flex flex-column gap-2 w-100">
                    <div class="d-flex flex-row gap-4 justify-content-between">
                        <label for="info1" class="col-form-label ">Tekst</label>
                        <input id="info1" class="form-control" type="text" value="Lorum ipsum">
                    </div>
                    <div class="d-flex flex-row gap-4 justify-content-between">
                        <label for="info2" class="col-form-label ">Email</label>
                        <input id="info2" class="form-control" type="email" value="administratie@waterscoutingmhg.nl">
                    </div>
                    <div class="d-flex flex-row gap-4 justify-content-between">
                        <label for="info3" class="col-form-label ">Nummer</label>
                        <input id="info3" class="form-control" type="number" value="42">
                    </div>
                    <div class="d-flex flex-row gap-4 justify-content-between">
                        <label for="info4" class="col-form-label ">Datum</label>
                        <input id="info4" class="form-control" type="date" value="2003-11-12">

                    </div>
                    <div class="d-flex flex-row gap-4 justify-content-between">
                        <label for="info5" class="col-form-label ">Dropdown</label>
                        <select id="info5" class="form-select">
                            <option>Selecteer een optie</option>
                            <option>Optie 1</option>
                            <option>Optie 2</option>
                        </select>
                    </div>
                    <div class="d-flex flex-row gap-4 justify-content-between">
                        <label for="info6" class="col-form-label ">Radio</label>
                        <input name="info6" id="info6" class="form-check-input" type="radio" checked="checked">
                        <label for="info7" class="col-form-label ">Radio</label>
                        <input name="info6" id="info7" class="form-check-input" type="radio">
                    </div>
                    <div class="d-flex flex-row gap-4 justify-content-between">
                        <label for="info8" class="col-form-label ">Checkbox</label>
                        <input name="info8" id="info8" class="form-check-input" type="checkbox" checked="checked">
                        <label for="info9" class="col-form-label ">Checkbox</label>
                        <input name="info8" id="info9" class="form-check-input" type="checkbox" checked="checked">
                    </div>
                </div>
            </div>
            <div class="button-container">
                <a id="close-popup" class="btn btn-outline-danger"><span
                        class="material-symbols-rounded">close</span></a>
            </div>
        </div>
    </div>

    <div class="container col-md-11">
        <h1>Bewerk {{ $activity->title }}</h1>
        @if(isset($lesson))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('lessons.environment.lesson.planning', $lesson->id) }}">Planning</a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('agenda.edit', ['lessonId' => $lesson->id]) }}">Agendapunten bewerken</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bewerk {{ $activity->title }}</li>
                </ol>
            </nav>
        @else
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('agenda') }}">Agenda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('agenda.edit') }}">Activiteiten bewerken</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bewerk {{ $activity->title }}</li>
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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-light rounded-2 p-3">
            <div class="container">
                <form method="POST" action="{{ route('agenda.edit.activity.save', $activity->id) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="mt-4">
                        <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">event</span>Algemene
                            Informatie</h2>
                        <div class="d-flex flex-column">
                            <label for="title" class="col-md-4 col-form-label ">Titel <span
                                    class="required-form">*</span></label>
                            <input name="title" type="text" class="form-control" id="title"
                                   value="{{ $activity->title }}"
                            >
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="">
                            <label for="image" class="col-md-4 col-form-label ">Coverafbeelding <span
                                    class="required-form">*</span></label>
                            <div class="d-flex flex-row-responsive gap-4 align-items-center justify-content-center">
                                <input class="form-control mt-2 col" id="image" type="file" name="image"
                                       accept="image/*">
                                @if($activity->image)
                                    <img class="zoomable-image" alt="profielfoto"
                                         style="width: 100%; min-width: 25px; max-width: 250px"
                                         src="{{ asset('files/agenda/agenda_images/'.$activity->image) }}">
                                @endif
                            </div>
                            @error('image')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="text-input">De content van je activiteit</label>
                            <div class="editor-parent">
                                @yield('editor')
                                <div id="text-input" contenteditable="true" name="text-input"
                                     class="text-input">{!! $activity->content !!}</div>
                                <small id="characters"></small>
                            </div>

                            <input id="content" name="content" type="hidden" value="{{ $activity->content }}">

                            @error('content')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                            @enderror
                        </div>
                    </div>


                        @if(!isset($lesson))
                    <div class="mt-4">
                        <div class="d-flex flex-row-responsive justify-content-between align-items-center">
                            <h2 class="flex-row gap-3"><span
                                    class="material-symbols-rounded me-2">app_registration</span>Inschrijfformulier</h2>
                            <a id="help-button"
                               class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                               style="border: none">
                                <span class="material-symbols-rounded" style="font-size: xx-large">help</span>
                            </a>
                        </div>
                        <p>Bij sommige activiteiten is het nodig om een inschrijfformulier toe te voegen, bijvoorbeeld
                            om bij te houden hoeveel teams meedoen met een pubquiz, of hoeveel kinderen mee willen lopen
                            in een spooktocht. Als je niet zeker weet wat elk type veld doet, klik dan op <span
                                id="help-button2" class="material-symbols-rounded"
                                style="transform: translateY(7px); cursor: pointer">help</span> voor meer informatie.
                        </p>

                        <script>
                            let helpButton = document.getElementById('help-button');
                            let helpButton2 = document.getElementById('help-button2');
                            let body = document.getElementById('app');
                            let html = document.querySelector('html');
                            let popUp = document.getElementById('popUp');


                            helpButton.addEventListener('click', function () {
                                openPopup();
                            });

                            helpButton2.addEventListener('click', function () {
                                openPopup();
                            });

                            closeButton = document.getElementById('close-popup');
                            closeButton.addEventListener('click', closePopup);

                            function openPopup() {
                                let scrollPosition = window.scrollY;
                                html.classList.add('no-scroll');
                                window.scrollTo(0, scrollPosition);
                                popUp.style.display = 'flex';
                            }

                            function closePopup() {
                                popUp.style.display = 'none';
                                html.classList.remove('no-scroll');
                            }

                        </script>
                        <p>Druk op de knop "Voeg veld toe" om een invoerveld toe te voegen.</p>

                        <div id="form-elements"
                             class="d-flex flex-column bg-info p-2 gap-2 m-2 rounded @if(!$activity->form_labels))d-none @endif">
                            @if(isset($activity->formElements) && $activity->formElements->isNotEmpty())
                                @foreach($activity->formElements as $index => $formElement)
                                    <div class="d-flex flex-column gap-2 bg-white rounded p-4 align-items-start"
                                         id="formElement{{ $index }}">
                                        <button type="button" class="btn btn-outline-danger align-self-end"
                                                onclick="removeFormElement(${fields})">Verwijder veld
                                        </button>
                                        <label for="fieldLabel{{ $index }}">Veldlabel (bijvoorbeeld: Naam, Achternaam,
                                            Adres)</label>
                                        <input id="fieldLabel{{ $index }}" class="form-control" type="text"
                                               name="form_labels[]" value="{{ $formElement->label }}">

                                        <label for="fieldType{{ $index }}">Type veld</label>
                                        <select id="fieldType{{ $index }}" class="form-control" name="form_types[]"
                                                onchange="handleFieldTypeChange(this, {{ $index }})">
                                            <option value="text" {{ $formElement->type == 'text' ? 'selected' : '' }}>
                                                Tekst
                                            </option>
                                            <option value="email" {{ $formElement->type == 'email' ? 'selected' : '' }}>
                                                E-mail
                                            </option>
                                            <option
                                                value="number" {{ $formElement->type == 'number' ? 'selected' : '' }}>
                                                Getal
                                            </option>
                                            <option value="date" {{ $formElement->type == 'date' ? 'selected' : '' }}>
                                                Datum
                                            </option>
                                            <option
                                                value="select" {{ $formElement->type == 'select' ? 'selected' : '' }}>
                                                Dropdown
                                            </option>
                                            <option value="radio" {{ $formElement->type == 'radio' ? 'selected' : '' }}>
                                                Radio
                                            </option>
                                            <option
                                                value="checkbox" {{ $formElement->type == 'checkbox' ? 'selected' : '' }}>
                                                Checkbox
                                            </option>
                                        </select>

                                        @if(in_array($formElement->type, ['select', 'radio', 'checkbox']))
                                            <div id="optionsContainer{{ $index }}" class="mt-2 w-100">
                                                <label>Waardes die in je dropdown, radio of checkbox komen te
                                                    staan</label>
                                                <div id="options{{ $index }}" class="w-100">
                                                    @foreach(explode(',', $formElement->option_value) as $option)
                                                        <!-- Split the string into an array -->
                                                        <div
                                                            class="d-flex flex-row-responsive align-items-center gap-2 w-100 mt-2">
                                                            <input type="text" class="form-control w-full"
                                                                   name="form_options[{{ $index }}][]"
                                                                   value="{{ trim($option) }}"> <!-- Trim whitespace -->
                                                            <button type="button"
                                                                    class="btn btn-outline-danger d-flex align-items-center justify-content-center"
                                                                    style="min-width: 10%" onclick="removeOption(this)">
                                                                <span class="material-symbols-rounded">close</span>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-info mt-2"
                                                        onclick="addOption({{ $index }})">Voeg optie toe
                                                </button>
                                            </div>
                                        @endif

                                        <label for="fieldRequired{{ $index }}">Verplicht veld</label>
                                        <input id="fieldRequired{{ $index }}" class="form-check-input" type="checkbox"
                                               name="is_required[]" {{ $formElement->is_required ? 'checked' : '' }}>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                        <button class="btn btn-primary text-white" type="button" onclick="addFormElement()">Voeg veld
                            toe
                        </button>
                        <script>
                            let fields = 0;

                            function addFormElement() {
                                let elementContainer = document.getElementById('form-elements');
                                elementContainer.classList.remove('d-none');

                                let html = `
        <div class="d-flex flex-column gap-2 bg-white rounded p-4 align-items-start" id="formElement${fields}">
            <button type="button" class="btn btn-outline-danger align-self-end" onclick="removeFormElement(${fields})">Verwijder veld</button>
            <label for="fieldLabel${fields}">Veldlabel (bijvoorbeeld: Naam, Achternaam, Adres)</label>
            <input id="fieldLabel${fields}" class="form-control" type="text" name="form_labels[]">

            <label for="fieldType${fields}">Type veld (wat je veld accepteerd, bijvoorbeeld "Tekst" als je een naam wilt of "Dropdown" bij een lijst tijdsloten)</label>
            <select id="fieldType${fields}" class="form-select" name="form_types[]" onchange="handleFieldTypeChange(this, ${fields})">
                <option value="text">Tekst</option>
                <option value="email">E-mail (geldig e-mail adres)</option>
                <option value="number">Getal</option>
                <option value="date">Datum</option>
                <option value="select">Dropdown (Kies 1 waarde uit een menu)</option>
                <option value="radio">Radio (Kies 1 waarde uit verschillende opties)</option>
                <option value="checkbox">Checkbox (Kies meerdere waardes uit verschillende opties)</option>
            </select>

            <div id="optionsContainer${fields}" class="d-none mt-2 w-100">
                <label>Waardes die in je dropdown, radio of checkbox komen te staan</label>
                <div id="options${fields}" class="w-100">
                    <div class="d-flex flex-row align-items-center gap-2 w-100 mt-2">
                        <input type="text" class="form-control w-full" name="form_options[${fields}][]">
                        <button type="button" class="btn btn-outline-danger d-flex align-items-center justify-content-center" style="min-width: 10%" onclick="removeOption(this)"><span
                                                                class="material-symbols-rounded">close</span></button>
                    </div>
                </div>
                <button type="button" class="btn btn-info mt-2" onclick="addOption(${fields})">Voeg optie toe</button>
            </div>

            <label for="fieldRequired${fields}">Verplicht veld</label>
            <input id="fieldRequired${fields}" class="form-check-input" type="checkbox" name="is_required[]">
        </div>`;

                                elementContainer.insertAdjacentHTML('beforeend', html);
                                fields++;
                            }

                            function removeFormElement(index) {
                                let element = document.getElementById(`formElement${index}`);
                                element.remove();
                            }

                            function addOption(fieldIndex) {
                                let optionsContainer = document.getElementById(`options${fieldIndex}`);
                                let newOptionHtml = `
        <div class="d-flex align-items-center gap-2 w-100 mt-2">
            <input type="text" class="form-control w-full" name="form_options[${fieldIndex}][]">
            <button type="button" class="btn btn-outline-danger d-flex align-items-center justify-content-center" style="min-width: 10%" onclick="removeOption(this)"><span
                                                                class="material-symbols-rounded">close</span></button>
        </div>`;
                                optionsContainer.insertAdjacentHTML('beforeend', newOptionHtml);
                            }

                            function removeOption(button) {
                                button.parentElement.remove();
                            }

                            function handleFieldTypeChange(select, fieldIndex) {
                                let optionsContainer = document.getElementById(`optionsContainer${fieldIndex}`);
                                let selectedValue = select.value;

                                if (['select', 'radio', 'checkbox'].includes(selectedValue)) {
                                    optionsContainer.classList.remove('d-none');
                                } else {
                                    optionsContainer.classList.add('d-none');
                                }
                            }
                        </script>


                    </div>
                    @endif


                    <div class="mt-4">
                        <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">date_range</span>Datum &
                            Tijd</h2>
                        <div class="d-flex flex-row-responsive gap-2 justify-content-between align-items-center">
                            <div class="w-100">
                                <label for="date_start" class="col-md-4 col-form-label ">Start datum en tijd <span
                                        class="required-form">*</span></label>
                                <input id="date_start" value="{{ $activity->date_start }}" type="datetime-local"
                                       class="form-control @error('date_start') is-invalid @enderror" name="date_start">
                                @error('date_start')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="w-100">
                                <label for="date_end" class="col-md-4 col-form-label ">Eind datum en tijd <span
                                        class="required-form">*</span></label>
                                <input id="date_end" value="{{ $activity->date_end }}" type="datetime-local"
                                       class="form-control @error('date_end') is-invalid @enderror" name="date_end">
                                @error('date_end')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="mt-4">
                        <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">event_note</span>Extra's
                        </h2>
                        <div class="w-100">
                            <label for="presence" class="col-form-label ">Laat de gebruikers zich aan of af melden voor
                                deze activiteit <span
                                    class="required-form">*</span></label>
                            <select id="presence" type="text"
                                    class="form-select @error('presence') is-invalid @enderror"
                                    name="presence">
                                <option value="0">Nee</option>
                                <option value="1" selected>Ja</option>
                            </select>
                            @error('presence')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        @if(!isset($lesson))

                        <div class="w-100">
                            <label for="public" class="col-form-label ">Maak dit een openbare activiteit (Het zal ook op
                                de
                                normale website komen te staan als activiteit) <span
                                    class="required-form">*</span></label>
                            <select id="public" type="text" class="form-select @error('public') is-invalid @enderror"
                                    name="public">
                                <option value="0">Nee</option>
                                <option value="1">Ja</option>
                            </select>
                            @error('public')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>


                        <div class="w-100">
                            <label for="price" class="col-form-label ">Prijs (als je dit
                                veld leeg laat vermelden we de prijs niet en voor een gratis evenement kun je 0
                                invullen)</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input name="price" type="number" class="form-control" aria-label="price"
                                       aria-describedby="price" id="price" value="{{ $activity->price }}">
                            </div>
                            @error('price')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        @else
                            <input name="public" type="hidden" value="0">
                            <input name="lesson_id" type="hidden" value="{{$lesson->id}}">
                        @endif

                        <div class="d-flex flex-column">
                            <label for="location" class="col-form-label ">Locatie, bijvoorbeeld "De veste" of "Sluisweg
                                4 4782PT"</label>
                            <input name="location" type="text" class="form-control" id="location"
                                   value="{{ $activity->location }}"
                            >
                            @error('location')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        @if(!isset($lesson))

                        <div class="d-flex flex-column">
                            <label for="organisator" class="col-form-label ">Organisatie, bijvoorbeeld "De Loodsen" of
                                "Leiding Zeeverkenners"</label>
                            <input name="organisator" type="text" class="form-control" id="organisator"
                                   value="{{ $activity->organisator }}"
                            >
                            @error('organisator')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mt-4">
                            <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">for_you</span>Rollen
                                & Mensen</h2>
                            <p>Als je geen rollen of gebruikers toevoegt aan je activiteit wordt deze voor iedereen
                                zichtbaar!</p>
                            <p>Om een activiteit aan te maken voor bijvoorbeeld de Zeeverkenners, kun je de rol
                                "Zeeverkenners" kiezen onder "Eventuele rollen waarvoor je activiteit geldt".</p>
                            <p>Vergeet niet om ook de leiding rol toe te voegen als je iets aan je speltak toevoegd!</p>
                            <div class="d-flex flex-column mt-4 mb-2">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                    aria-expanded="false" aria-controls="collapseOne">
                                                <label for="select-roles" class="col-md-4 col-form-label ">Eventuele
                                                    rollen
                                                    waarvoor je activiteit geldt</label>
                                            </button>
                                        </h2>

                                        <div id="collapseOne" class="accordion-collapse collapse"
                                             data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="custom-select">
                                                    @php
                                                        $selectedRoles = explode(',', $activity->roles);
                                                    @endphp

                                                    <select id="select-roles" class="d-none" name="roles[]" multiple>
                                                        @foreach($all_roles as $role)
                                                            <option data-description="{{ $role->description }}"
                                                                    value="{{ $role->id }}"
                                                                    @if(in_array($role->id, $selectedRoles)) selected @endif>
                                                                {{ $role->role }}
                                                            </option>
                                                        @endforeach
                                                    </select>


                                                </div>
                                                <div class="d-flex flex-wrap gap-1" id="button-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseTwo" aria-expanded="false"
                                                    aria-controls="collapseTwo">
                                                <label for="users" class="col-md-4 col-form-label ">Eventuele gebruikers
                                                    waarvoor je activiteit
                                                    geldt</label>
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                             data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <input id="users" name="users" type="text"
                                                       value="{{ $activity->users }}"
                                                       class="user-select-window user-select-none form-control"
                                                       placeholder="Kies een gebruiker uit de lijst"
                                                       aria-label="user" aria-describedby="basic-addon1">
                                                <div class="user-select-window-popup d-none mt-2"
                                                     style="position: unset; display: block !important;">
                                                    <h3>Selecteer gebruikers</h3>
                                                    <div class="input-group">
                                                        <label class="input-group-text" id="basic-addon1">
                                                            <span class="material-symbols-rounded">search</span></label>
                                                        <input type="text" data-type="multiple" data-stayopen="true"
                                                               class="user-select-search form-control"
                                                               placeholder="Zoeken op naam, email, adres etc."
                                                               aria-label="Zoeken" aria-describedby="basic-addon1"

                                                        >
                                                    </div>
                                                    <div class="user-list no-scrolbar">
                                                        <div
                                                            class="w-100 h-100 d-flex justify-content-center align-items-center"><span
                                                                class="material-symbols-rounded rotating"
                                                                style="font-size: xxx-large">progress_activity</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div>
                        @error('roles')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        @error('users')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center flex-row mt-3 gap-2">
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
                            class="btn btn-success flex flex-row align-items-center justify-content-center">
                            <span class="button-text">Opslaan</span>
                            <span style="display: none" class="loading-spinner spinner-border spinner-border-sm" aria-hidden="true"></span>
                            <span style="display: none" class="loading-text" role="status">Laden...</span>
                        </button>
                        @if(isset($lesson))
                            <a class="btn btn-danger text-white" href="{{ route('agenda.edit', ['lessonId' => $lesson->id]) }}">Annuleren</a>
                            <a class="delete-button btn btn-outline-danger"
                               data-id="{{ $activity->id }}"
                               data-name="{{ $activity->title }}"
                               data-link="{{ route('agenda.delete', [$activity->id, 'lessonId' => $lesson->id]) }}">Verwijderen</a>
                        @else
                        <a class="btn btn-danger text-white" href="{{ route('agenda.edit') }}">Annuleren</a>
                        <a class="delete-button btn btn-outline-danger"
                           data-id="{{ $activity->id }}"
                           data-name="{{ $activity->title }}"
                           data-link="{{ route('agenda.delete', $activity->id) }}">Verwijderen</a>
                        @endif
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

