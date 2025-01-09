@extends('layouts.lessons')

@vite(['resources/js/search-user.js'])


@section('content')

    @if($isTeacher)

    <div id="popup-teacher" class="popup d-none" style="margin-top: -122px">
        <div class="popup-body">
            <h2>Bewerk de <strong>praktijkbegeleiders</strong></h2>
            <p>Voeg mensen toe of haal mensen weg door op ze te klikken.</p>

            <form class="w-100 d-flex flex-column align-items-center" method="POST"
                  action="{{ route('lessons.environment.lesson.users.add.teacher', $lesson->id) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="w-100">
                    <input id="users" name="users" type="hidden"
                           value="{{ $teachers->pluck('id')->implode(',') }}"
                           class="user-select-window user-select-none form-control"
                           placeholder="Kies een gebruiker uit de lijst"
                           aria-label="user" aria-describedby="basic-addon1">

                    <div class="user-select-window-popup no-shadow d-none mt-2"
                         style="position: unset; display: block !important;">
                        <div class="input-group">
                            <label class="input-group-text" id="basic-addon1">
                                <span class="material-symbols-rounded">search</span></label>
                            <input type="text" data-type="multiple" data-stayopen="true"
                                   class="user-select-search form-control" id="user-search"
                                   placeholder="Zoeken op naam, email, adres etc."
                                   aria-label="Zoeken" aria-describedby="basic-addon1">
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

                <div class="button-container">
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
                        class="btn btn-success text-white flex flex-row align-items-center justify-content-center">
                        <span class="button-text">Praktijkbegeleiders opslaan</span>
                        <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                              aria-hidden="true"></span>
                        <span style="display: none" class="loading-text" role="status">Laden...</span>
                    </button>
                    <a id="popup-teacher-button-close" class="btn btn-outline-danger">Annuleren</a>
                </div>
            </form>
        </div>
    </div>

    <div id="popup-student" class="popup d-none" style="margin-top: -122px">
        <div class="popup-body">
            <h2>Bewerk de <strong>deelnemers</strong></h2>
            <p>Voeg mensen toe of haal mensen weg door op ze te klikken.</p>

            <form class="w-100 d-flex flex-column align-items-center" method="POST"
                  action="{{ route('lessons.environment.lesson.users.add.student', $lesson->id) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="w-100">
                    <input id="users" name="users" type="hidden"
                           value="{{ $students->pluck('id')->implode(',') }}"
                           class="user-select-window user-select-none form-control"
                           placeholder="Kies een gebruiker uit de lijst"
                           aria-label="user" aria-describedby="basic-addon1">

                    <div class="user-select-window-popup no-shadow d-none mt-2"
                         style="position: unset; display: block !important;">
                        <div class="input-group">
                            <label class="input-group-text" id="basic-addon1">
                                <span class="material-symbols-rounded">search</span></label>
                            <input type="text" data-type="multiple" data-stayopen="true"
                                   class="user-select-search form-control" id="user-search"
                                   placeholder="Zoeken op naam, email, adres etc."
                                   aria-label="Zoeken" aria-describedby="basic-addon1">
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

                <div class="button-container">
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
                        class="btn btn-success text-white flex flex-row align-items-center justify-content-center">
                        <span class="button-text">Deelnemers opslaan</span>
                        <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                              aria-hidden="true"></span>
                        <span style="display: none" class="loading-text" role="status">Laden...</span>
                    </button>
                    <a id="popup-student-button-close" class="btn btn-outline-danger">Annuleren</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let teacherPopup = document.getElementById('popup-teacher');
            let teacherPopupButton = document.getElementById('popup-teacher-button');
            let teacherPopupButtonClose = document.getElementById('popup-teacher-button-close');

            let studentPopup = document.getElementById('popup-student');
            let studentPopupButton = document.getElementById('popup-student-button');
            let studentPopupButtonClose = document.getElementById('popup-student-button-close');

            teacherPopupButton.addEventListener('click', function () {
                teacherPopup.classList.remove('d-none');
            });

            teacherPopupButtonClose.addEventListener('click', function () {
                teacherPopup.classList.add('d-none');
            });

            studentPopupButton.addEventListener('click', function () {
                studentPopup.classList.remove('d-none');
            });

            studentPopupButtonClose.addEventListener('click', function () {
                studentPopup.classList.add('d-none');
            });
        });
    </script>
    @endif

    <div class="container col-md-11">
        <h1>Deelnemers</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Deelnemers</li>
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

        <div>
            <div class="d-flex flex-row justify-content-between">
                <h2>{{ Str::plural('Praktijkbegeleider', count($teachers)) }}</h2>
                @if($isTeacher)
                <a id="popup-teacher-button" class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                   style="border: none">
                    <span class="material-symbols-rounded" style="font-size: xx-large">manage_accounts</span>
                </a>
                @endif
            </div>
            @if(count($teachers) > 0)

                <div class="d-flex flex-column gap-3 justify-content-center">
                    @foreach($teachers as $user)
                        <div
                            class="d-flex align-items-center flex-row rounded gap-3 p-2 w-100 bg-secondary-subtle">
                            @if($user->profile_picture)
                                <img alt="profielfoto" class="profle-picture zoomable-image"
                                     src="{{ asset('/profile_pictures/' .$user->profile_picture) }}">
                            @else
                                <img alt="profielfoto" class="profle-picture"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif

                            <div class="d-flex flex-column align-items-start justify-content-center">
                                <h3 class="m-0">{{ $user->name.' '.$user->infix.' '.$user->last_name }}</h3>
                                <a class="m-0 text-decoration-none text-black"
                                   href="tel:{{$user->phone}}">{{ $user->phone }}</a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <span class="material-symbols-rounded me-2">person_off</span>Geen praktijkbegeleiders gevonden...
                </div>
            @endif
        </div>
        <div class="mt-5">

            <div class="d-flex flex-row justify-content-between">
            <h2>{{ Str::plural('Deelnemer', count($students)) }}</h2>
                @if($isTeacher)
                <a id="popup-student-button" class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                   style="border: none">
                    <span class="material-symbols-rounded" style="font-size: xx-large">manage_accounts</span>
                </a>
                @endif
            </div>
            @if(count($students) > 0)
                <div class="d-flex flex-column gap-3 justify-content-center">
                    @foreach($students as $user)
                        <div
                            class="d-flex align-items-center flex-row rounded gap-3 p-2 w-100 bg-secondary-subtle">
                            @if($user->profile_picture)
                                <img alt="profielfoto" class="profle-picture zoomable-image"
                                     src="{{ asset('/profile_pictures/' .$user->profile_picture) }}">
                            @else
                                <img alt="profielfoto" class="profle-picture"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif

                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <div class="d-flex flex-column align-items-start justify-content-center">
                                    <h3 class="m-0">{{ $user->name.' '.$user->infix.' '.$user->last_name }}</h3>
                                    <a class="m-0 text-decoration-none text-black"
                                       href="tel:{{$user->phone}}">{{ $user->phone }}</a>
                                    <a class="m-0 text-decoration-none text-black"
                                       href="mailto:{{$user->email}}">{{ $user->email }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <span class="material-symbols-rounded me-2">person_off</span>Geen deelnemers gevonden...
                </div>
            @endif
        </div>

    </div>
@endsection
