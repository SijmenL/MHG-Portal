@extends('layouts.lessons')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    @if($isTeacher)
        <div id="popup-exam" class="popup {{ request()->query('error') == 'true' ? '' : 'd-none' }}"
             style="margin-top: -122px">
            <div class="popup-body">
                <h2>Voeg een examen toe aan de lesomgeving</h2>

                <form action="{{ route('lessons.environment.lesson.results.store', $lesson->id) }}" class="w-100"
                      method="POST" style="text-align: start">
                    @csrf

                    <div class="d-flex flex-column">
                        <label for="title" class="col-md-4 col-form-label w-100">Titel <span
                                class="required-form">*</span></label>
                        <input name="title" type="text" class="form-control" id="title" value="{{ old('title') }}"
                        >
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="d-flex flex-column">
                        <label for="date" class="col-md-4 col-form-label w-100">Datum van het examen</label>
                        <input name="date" type="date" class="form-control" id="date" value="{{ old('date') }}"
                        >
                        @error('date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="d-flex flex-column w-full">
                        <label for="max_points" class="col-md-4 col-form-label w-100">Maximaal te behalen aantal
                            punten</label>
                        <input name="max_points" type="number" class="form-control" id="max_points"
                               value="{{ old('max_points') }}"
                        >
                        @error('max_points')
                        <span class="text-danger">{{ $message }}</span>
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
                            <span class="button-text">Examen toevoegen</span>
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
            <h1>Resultaten</h1>
            @if($isTeacher)
                <a id="popup-exam-button"
                   class="btn btn-outline-dark">
                    Examen toevoegen
                </a>
            @endif
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Resultaten</li>
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

        @if($isTeacher)

            <script>
                // Check if the "editedtest" query parameter is present in the URL
                window.onload = function () {
                    const urlParams = new URLSearchParams(window.location.search);
                    const editedTestId = urlParams.get('editedtest');

                    if (editedTestId) {
                        const targetElement = document.getElementById(editedTestId);

                        // Scroll to the element with the given ID if it exists
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                }
            </script>

            <div>
                <p>Bekijk per examen de resultaten van de deelnemers, en pas de resultaten aan.</p>
                    @if(count($tests) > 0)

                <div class="settings-container">

                        @foreach ($tests as $test)
                            <a class="setting" data-bs-toggle="collapse" href="#{{$test->id}}" role="button"
                               aria-expanded="{{ request()->query('editedtest') == (string) $test->id ? 'true' : 'false' }}"
                               aria-controls="original">

                                <div class="setting-text">
                                    <div>
                                        <h3>{{ $test->title }}</h3>
                                        <small>{{ Carbon::parse($test->date)->translatedFormat('l d M Y') }}
                                        </small>
                                    </div>
                                    <span class="material-symbols-rounded">expand_more</span>
                                </div>
                            </a>

                            <div style="background-color: #e8e9ed"
                                 class="collapse multi-collapse {{ request()->query('editedtest') == (string) $test->id ? 'show' : '' }}"
                                 id="{{$test->id}}">
                                <div class="bg-light p-3 rounded">
                                    <form
                                        action="{{ route('lessons.environment.lesson.results.store.grades', $test->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="container mt-4">
                                            <div class="overflow-x-scroll no-scrolbar" style="max-width: 75vw">

                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Deelnemer</th>
                                                        <th>Score</th>
                                                        <th>Feedback</th>
                                                        <th>Geslaagd</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($lesson->users as $user)
                                                        @php
                                                            // Define the roles that classify the user as a teacher
                                                            $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

                                                            // Check if the user is a teacher based on roles or lesson-specific permissions
                                                            $isUserTeacher = $user->roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
                                                                $lesson->user_id === $user->id ||
                                                                $lesson->users()
                                                                    ->where('user_id', $user->id)
                                                                    ->wherePivot('teacher', true)
                                                                    ->exists();
                                                        @endphp

                                                        @if(!$isUserTeacher)
                                                            <tr>
                                                                <td>{{ $user->name.' '.$user->infix.' '.$user->last_name }}</td>
                                                                <input type="hidden" name="grades[{{ $user->id }}][user_id]" value="{{ $user->id }}">

                                                                <td class="d-flex flex-row gap-2 h-100" style="min-width: 60px">
                                                                    <input type="number" name="grades[{{ $user->id }}][score]" class="form-control"
                                                                           value="{{ $test->testResults->where('user_id', $user->id)->first()->score ?? '' }}">
                                                                    @if(isset($test->max_points))
                                                                        <p class="w-25" style="min-width: 30px">/{{$test->max_points}}</p>
                                                                    @endif
                                                                </td>

                                                                <td style="min-width: 250px">
                <textarea name="grades[{{ $user->id }}][feedback]" class="form-control"
                          style="height: 30px">@if(isset($test->testResults->where('user_id', $user->id)->first()->feedback)){{ trim($test->testResults->where('user_id', $user->id)->first()->feedback) ?? '' }}@endif</textarea>
                                                                </td>

                                                                <td>
                                                                    <select name="grades[{{ $user->id }}][passed]" class="form-control">
                                                                        <option value="0" {{ !($test->testResults->where('user_id', $user->id)->first()->passed ?? true) ? 'selected' : '' }}>
                                                                            Nee
                                                                        </option>
                                                                        <option value="1" {{ ($test->testResults->where('user_id', $user->id)->first()->passed ?? false) ? 'selected' : '' }}>
                                                                            Ja
                                                                        </option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="d-flex flex-row gap-2">
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
                                                class="btn btn-success flex flex-row align-items-center justify-content-center"
                                                type="submit">
                                                <span class="button-text">Sla resultaten op</span>
                                                <span style="display: none"
                                                      class="loading-spinner spinner-border spinner-border-sm"
                                                      aria-hidden="true"></span>
                                                <span style="display: none" class="loading-text"
                                                      role="status">Laden...</span>
                                            </button>

                                            <a class="btn btn-outline-dark" href="{{route('lessons.environment.lesson.results.edit.exam', [$lesson->id, $test->id])}}">Bewerk examen</a>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>

                        @if(!$loop->last)
                            <div class="devider"></div>
                        @endif

                        @endforeach
                </div>
            </div>

        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">post</span>Er zijn nog geen examens toegevoegd aan deze
                lesomgeving.
            </div>
        @endif

        @else

            @if(count($tests) > 0)
                <div class="overflow-x-scroll no-scrolbar" style="max-width: 95vw">
                    <table class="position-relative table table-striped table-bordered" style="width: 100%">
                        <thead style="position: sticky; top: 0px; background-color: white; z-index: 100;">
                        <tr>
                            <th>Examen</th>
                            <th>Score</th>
                            <th>Feedback</th>
                            <th>Geslaagd</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($tests as $test)
                            <tr>
                                <td style="min-width: 150px">
                                    <p><strong>{{ $test->title }}</strong></p>
                                    <p>{{Carbon::parse($test->date)->translatedFormat('l d M Y') }}</p>
                                </td>
                                <td style="min-width: 75px">
                                    @if(isset($test->testResults->where('user_id', $user->id)->first()->score))
                                        <p>{{ $test->testResults->where('user_id', $user->id)->first()->score }}@if(isset($test->max_points))
                                                /{{$test->max_points}}
                                            @endif</p>
                                    @endif
                                </td>
                                <td style="min-width: 350px">
                                    <p>{{ $test->testResults->where('user_id', $user->id)->first()->feedback ?? '' }}</p>
                                </td>
                                <td>
                                    <p>{{ optional($test->testResults->where('user_id', $user->id)->first())->passed ? 'Ja' : 'Nee'}}</p>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <span class="material-symbols-rounded me-2">post</span>Er zijn nog geen examens toegevoegd aan deze
                    lesomgeving.
                </div>
            @endif
        @endif
    </div>
@endsection
