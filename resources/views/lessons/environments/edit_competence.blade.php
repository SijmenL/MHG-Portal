@extends('layouts.lessons')

@vite(['resources/js/texteditor.js', 'resources/js/search-user.js', 'resources/css/texteditor.css', 'resources/js/home.js'])

@include('partials.editor')

@section('content')

    <div class="container col-md-11">
        <div class="d-flex flex-row align-items-center justify-content-between">
            <h1>Bewerk {{$competence->title}}</h1>

        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson.competences', $lesson->id) }}">Competenties</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bewerk {{$competence->title}}</li>
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
            <form action="{{ route('lessons.environment.lesson.competences.edit.store', [$lesson->id, $competence->id]) }}" class="w-100"
                  method="POST" style="text-align: start">
                @csrf

                <div class="d-flex flex-column">
                    <label for="title" class="col-md-4 col-form-label w-100">Titel <span
                            class="required-form">*</span></label>
                    <input name="title" type="text" class="form-control" id="title" value="{{ $competence->title }}"
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
                             class="text-input">{!! $competence->description !!}</div>
                        <small id="characters"></small>
                    </div>

                    <input id="content" name="description" type="hidden" value="{{ $competence->description }}">

                    @error('description')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
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
                    <a href="{{ route('lessons.environment.lesson.competences', [$competence->lesson_id]) }}" class="btn btn-danger text-white">Annuleren</a>
                    <a class="delete-button btn btn-outline-danger"
                       data-id="{{ $competence->id }}"
                       data-name="{{ $competence->title }} en de bijbehorende afgestreepte gegevens"
                       data-link="{{ route('lessons.environment.lesson.competences.delete', [$lesson->id, $competence->id]) }}">Verwijderen</a>
                </div>
            </form>
        </div>
    </div>
@endsection
