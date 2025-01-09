@extends('layouts.lessons')

@section('content')

    <div class="container col-md-11">
        <div class="d-flex flex-row align-items-center justify-content-between">
            <h1>Bewerk {{$test->title}}</h1>

        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson.results', $lesson->id) }}">Resultaten</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bewerk {{$test->title}}</li>
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
            <form action="{{ route('lessons.environment.lesson.results.edit.exam.store', [$lesson->id, $test->id]) }}" class="w-100"
                  method="POST" style="text-align: start">
                @csrf

                <div class="d-flex flex-column">
                    <label for="title" class="col-md-4 col-form-label w-100">Titel <span
                            class="required-form">*</span></label>
                    <input name="title" type="text" class="form-control" id="title" value="{{ $test->title }}"
                    >
                    @error('title')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="d-flex flex-column">
                    <label for="date" class="col-md-4 col-form-label w-100">Datum van het examen</label>
                    <input name="date" type="date" class="form-control" id="date" value="{{ $test->date }}"
                    >
                    @error('date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="d-flex flex-column w-full">
                    <label for="max_points" class="col-md-4 col-form-label w-100">Maximaal te behalen aantal
                        punten</label>
                    <input name="max_points" type="number" class="form-control" id="max_points"
                           value="{{ $test->max_points }}"
                    >
                    @error('max_points')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div
                    class="button-container w-100 d-flex flex-row-responsive">
                    <button type="submit"
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
                        <span class="button-text">Opslaan</span>
                        <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                              aria-hidden="true"></span>
                        <span style="display: none" class="loading-text" role="status">Laden...</span>
                    </button>
                    <a href="{{ route('lessons.environment.lesson.results', [$test->lesson_id, 'editedtest=' . $test->id]) }}" class="btn btn-danger text-white">Annuleren</a>
                    <a class="delete-button btn btn-outline-danger"
                       data-id="{{ $test->id }}"
                       data-name="{{ $test->title }} en alle resultaten"
                       data-link="{{ route('lessons.environment.lesson.results.exam.delete', [$lesson->id, $test->id]) }}">Verwijderen</a>
                </div>
            </form>
        </div>
    </div>
@endsection
