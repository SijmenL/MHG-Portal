@extends('layouts.lessons')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@vite(['resources/js/texteditor.js', 'resources/js/search-user.js', 'resources/css/texteditor.css', 'resources/js/home.js'])

@include('partials.editor')

@section('content')
    @if($isTeacher)
        <div id="popup-exam" class="popup {{ request()->query('error') == 'true' ? '' : 'd-none' }}"
             style="margin-top: -122px">
            <div class="popup-body">
                <h2>Voeg een competentie toe aan de lesomgeving</h2>

                <form action="{{ route('lessons.environment.lesson.competences.store', $lesson->id) }}" class="w-100"
                      method="POST" style="text-align: start">
                    @csrf

                    <div class="d-flex flex-column">
                        <label for="title" class="col-md-4 col-form-label w-100">Competentie <span
                                class="required-form">*</span></label>
                        <input name="title" type="text" class="form-control" id="title" value="{{ old('title') }}"
                        >
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="text-input">Omschrijving <span
                                class="required-form">*</span></label>
                        <div class="editor-parent">
                            @yield('editor')
                            <div id="text-input" contenteditable="true" name="text-input"
                                 class="text-input">{!! old('description') !!}</div>
                            <small id="characters"></small>
                        </div>

                        <input id="content" name="description" type="hidden" value="{{ old('description') }}">

                        @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div
                        class="button-container w-100 d-flex flex-row-responsive align-items-center justify-content-center">
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
                            <span class="button-text">Competentie toevoegen</span>
                            <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                                  aria-hidden="true"></span>
                            <span style="display: none" class="loading-text" role="status">Laden...</span>
                        </button>
                        <a id="popup-exam-button-close" class="btn btn-outline-danger">Annuleren</a>
                    </div>
                </form>

            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                let examPopup = document.getElementById('popup-exam');
                let examPopupButton = document.getElementById('popup-exam-button');
                let examPopupButtonClose = document.getElementById('popup-exam-button-close');

                examPopupButton.addEventListener('click', function () {
                    examPopup.classList.remove('d-none');
                });

                examPopupButtonClose.addEventListener('click', function () {
                    // Hide the popup
                    examPopup.classList.add('d-none');

                    // Remove the ?error from the URL
                    const url = new URL(window.location.href);
                    url.searchParams.delete('error');
                    window.history.pushState({}, '', url);
                });

            })
        </script>
    @endif

    <div class="container col-md-11">
        <div class="d-flex flex-row align-items-center justify-content-between">
            <h1>Competenties {{ $lesson->title }}</h1>
            @if($isTeacher)
                <a id="popup-exam-button"
                   class="btn btn-outline-dark">
                    Competentie toevoegen
                </a>
            @endif
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Competentielijst</li>
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

        @if(count($competences) > 0)

            <div class="d-flex flex-column gap-3">
                @if($isTeacher)
                    @php
                        $users = $lesson->users;
                        $userIds = $users->pluck('id')->toArray();
                        $currentIndex = array_search($selectedUser->id, $userIds);

                        $prevUser = $currentIndex > 0 ? $users[$currentIndex - 1] : null;
                        $nextUser = $currentIndex < count($users) - 1 ? $users[$currentIndex + 1] : null;
                    @endphp

                    <div class="d-flex flex-row justify-content-between rounded p-2 align-items-center"
                         style="background-color: #ebedef">

                        {{-- Previous User Button --}}
                        <a class="btn d-flex align-items-center justify-content-center
              @if(!$prevUser) cursor-default text-secondary @endif"
                           @if($prevUser) href="{{ route('lessons.environment.lesson.competences', ['lessonId' => $lesson->id, 'user' => $prevUser->id]) }}" @endif>
                            <span class="material-symbols-rounded">arrow_back_ios</span>
                        </a>

                        <div class="d-flex flex-row no-scrollbar overflow-y-scroll" id="user-scroll-container">
                            @foreach($users as $user)
                                <a href="{{ route('lessons.environment.lesson.competences', ['lessonId' => $lesson->id, 'user' => $user->id]) }}"
                                   class="d-flex @if($selectedUser->id === $user->id) bg-primary text-white @else bg-light text-black @endif
           text-decoration-none flex-column gap-1 justify-content-center align-items-center text-center m-2 p-2 rounded"
                                   style="min-width: 100px"
                                   id="user-{{ $user->id }}">
                                    @if($user->profile_picture)
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('/profile_pictures/' . $user->profile_picture) }}">
                                    @else
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('img/no_profile_picture.webp') }}">
                                    @endif
                                    {{ $user->name.' '.$user->infix.' '.$user->last_name }}
                                </a>
                            @endforeach
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                let container = document.getElementById("user-scroll-container");
                                let selectedUser = document.getElementById("user-{{ $selectedUser->id }}");

                                if (container && selectedUser) {
                                    let containerWidth = container.clientWidth;
                                    let selectedUserOffset = selectedUser.offsetLeft;
                                    let selectedUserWidth = selectedUser.clientWidth;

                                    // Scroll to center the selected user
                                    container.scrollLeft = selectedUserOffset - (containerWidth / 2) + (selectedUserWidth / 2);
                                }
                            });
                        </script>


                        {{-- Next User Button --}}
                        <a class="btn d-flex align-items-center justify-content-center
              @if(!$nextUser) cursor-default text-secondary @endif"
                           @if($nextUser) href="{{ route('lessons.environment.lesson.competences', ['lessonId' => $lesson->id, 'user' => $nextUser->id]) }}" @endif>
                            <span class="material-symbols-rounded">arrow_forward_ios</span>
                        </a>
                    </div>

                @else
                    @php
                        $passedCount = $competences->filter(function ($competence) use ($selectedUser) {
                            return $competence->competenceResults
                                ->where('user_id', $selectedUser->id)
                                ->where('passed', true)
                                ->isNotEmpty();
                        })->count();
                    @endphp

                    <p>Je hebt {{ $passedCount }}/{{ $competences->count() }} competenties behaald!</p>

                @endif

                <table class="position-relative table table-striped table-bordered" style="width: 100%">
                    <thead style="position: sticky; top: 0px; background-color: white; z-index: 100;">
                    <tr>
                        <th></th>
                        <th style="min-width: 100px" class="text-center">Competentie</th>
                        <th>Omschrijving</th>
                        @if($isTeacher)
                            <th style="min-width: 115px">Opties</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($competences as $competence)
                        @php
                            // Check if the selected user has passed this competence
                            $competenceResult = $competence->competenceResults
                                ->where('user_id', $selectedUser->id)
                                ->first();

                            if (isset($competenceResult)) {
                            $hasPassed = $competenceResult->passed;
                        } else {
                                $hasPassed = false;
                        }
                        @endphp
                        <tr>
                            <th class="text-center" style="font-weight: unset; vertical-align: middle;">
                                <div class="d-flex align-items-center justify-content-center w-100 h-100">
                                    @if($isTeacher)
                                        <input type="checkbox"
                                               class="competence-checkbox"
                                               data-competence-id="{{ $competence->id }}"
                                               data-user-id="{{ $selectedUser->id }}"
                                               {{ $hasPassed ? 'checked' : '' }}
                                               {{ !$isTeacher ? 'disabled' : '' }}
                                               style="transform: scale(1.5);">
                                    @else
                                        @if($hasPassed)
                                            <span class="material-symbols-rounded text-success">check_box</span>
                                        @else
                                            <span class="material-symbols-rounded">check_box_outline_blank</span>
                                        @endif
                                    @endif
                                </div>
                            </th>

                            <th class="text-center" style="font-weight: unset; vertical-align: middle;">
                                <div
                                    class="d-flex @if($hasPassed && !$isTeacher) text-success @endif align-items-center justify-content-center w-100 h-100">
                                    {{ $competence->title }}
                                </div>
                            </th>

                            <th style="font-weight: unset;"
                                class="@if($hasPassed && !$isTeacher) text-success @endif">{!! $competence->description !!}</th>

                            @if($isTeacher)
                                <th class="text-center" style="font-weight: unset; vertical-align: middle;">
                                    <div
                                        class="d-flex align-items-center justify-content-center w-100 h-100">
                                        <a class="btn btn-outline-dark"
                                           href="{{route('lessons.environment.lesson.competences.edit', [$lesson->id, $competence->id])}}">Bewerken</a>
                                    </div>
                                </th>
                            @endif
                        </tr>
                    @endforeach

                    @if($isTeacher)
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                document.querySelectorAll('.competence-checkbox').forEach(checkbox => {
                                    checkbox.addEventListener('change', function () {
                                        const competenceId = this.dataset.competenceId;
                                        const userId = this.dataset.userId;
                                        const isChecked = this.checked;

                                        fetch('/lessen/omgeving/{{$lesson->id}}/competenties/update', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                            },
                                            body: JSON.stringify({
                                                competence_id: competenceId,
                                                user_id: userId,
                                                passed: isChecked
                                            })
                                        })
                                            .then(response => response.json())
                                            .then(data => {
                                                console.log(data.message);
                                            })
                                            .catch(error => console.error('Error:', error));
                                    });
                                });
                            });
                        </script>
                    @endif

                    </tbody>
                </table>
            </div>

        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">post</span>Er zijn nog geen competenties toegevoegd aan
                deze
                lesomgeving.
            </div>
        @endif

    </div>
@endsection
