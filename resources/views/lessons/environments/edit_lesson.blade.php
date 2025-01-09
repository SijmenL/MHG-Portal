@extends('layouts.lessons')

@vite(['resources/js/texteditor.js', 'resources/js/search-user.js', 'resources/css/texteditor.css', 'resources/js/home.js'])

@include('partials.editor')

@section('content')

    <div class="container col-md-11">
        <div class="d-flex flex-row align-items-center justify-content-between">
            <h1>Bewerk {{$lesson->title}}</h1>

        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bewerk {{$lesson->title}}</li>
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
            <form method="POST" action="{{ route('lessons.environment.lesson.edit.store', $lesson->id) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="d-flex flex-column">
                    <label for="title" class="col-md-4 col-form-label ">Titel <span
                            class="required-form">*</span></label>
                    <input name="title" type="text" class="form-control" id="title" value="{{ $lesson->title }}"
                    >
                    @error('title')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                @if(isset($lesson->image))
                    <div class="m-4 d-flex align-items-center justify-content-center">
                        <img class="zoomable-image" alt="Coverafbeelding"
                             style="width: 100%; min-width: 25px; max-width: 250px"
                             src="{{ asset('/files/lessons/lesson-images/' .$lesson->image) }}">
                    </div>
                @endif
                <div>
                    <label for="image" class="col-md-4 col-form-label ">Coverafbeelding</label>
                    <div class="d-flex flex-row-responsive gap-4 align-items-center justify-content-center">
                        <input class="form-control mt-2 col" id="image" type="file" name="image"
                               accept="image/*">
                        @error('image')
                    </div>
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


                <div class="mt-4">
                    <label for="text-input">Beschrijving van je les <span
                            class="required-form">*</span></label>
                    <div class="editor-parent">
                        @yield('editor')
                        <div id="text-input" contenteditable="true" name="text-input"
                             class="text-input">{!! $lesson->description !!}</div>
                        <small id="characters"></small>
                    </div>

                    <input id="content" name="description" type="hidden" value="{{ $lesson->description }}">

                    @error('description')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-flex flex-row-responsive gap-2 justify-content-between align-items-center mt-3">
                    <div class="w-100">
                        <label for="date_start" class="col-md-4 col-form-label ">Start datum</label>
                        <input id="date_start" value="{{ $lesson->date_start }}" type="date"
                               class="form-control @error('date_start') is-invalid @enderror" name="date_start">
                        @error('date_start')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="w-100">
                        <label for="date_end" class="col-md-4 col-form-label ">Eind datum</label>
                        <input id="date_end" value="{{ $lesson->date_end }}" type="date"
                               class="form-control @error('date_end') is-invalid @enderror" name="date_end">
                        @error('date_end')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
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
                        <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                              aria-hidden="true"></span>
                        <span style="display: none" class="loading-text" role="status">Laden...</span>
                    </button>
                    <a class="btn btn-danger text-white" href="{{ route('lessons.environment.lesson', $lesson->id) }}">Annuleren</a>
                    <a class="delete-button btn btn-outline-danger"
                       data-id="{{ $lesson->id }}"
                       data-name="{{ $lesson->title }} en alle gegevens"
                       data-link="{{ route('lessons.environment.lesson.delete', $lesson->id) }}">Verwijderen</a>
                </div>
            </form>
        </div>
    </div>
@endsection
