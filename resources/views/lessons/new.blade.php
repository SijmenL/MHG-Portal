@extends('layouts.app')

@vite(['resources/js/texteditor.js', 'resources/js/search-user.js', 'resources/css/texteditor.css', 'resources/js/home.js'])

@include('partials.editor')

@section('content')
    <div class="container col-md-11">
        <h1>Nieuwe lesomgeving</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nieuwe lesomgeving</li>
            </ol>
        </nav>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-light rounded-2 p-3">
            <div class="container">
                <form method="POST" action="{{ route('lessons.new.create') }}" enctype="multipart/form-data">
                    @csrf
                    <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">school</span>Les informatie
                    </h2>

                    <div class="d-flex flex-column">
                        <label for="title" class="col-md-4 col-form-label ">Titel <span
                                class="required-form">*</span></label>
                        <input name="title" type="text" class="form-control" id="title" value="{{ old('title') }}"
                        >
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="">
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

                    <div class="d-flex flex-row-responsive gap-2 justify-content-between align-items-center mt-3">
                        <div class="w-100">
                            <label for="date_start" class="col-md-4 col-form-label ">Start datum</label>
                            <input id="date_start" value="{{ old('date_start') }}" type="date"
                                   class="form-control @error('date_start') is-invalid @enderror" name="date_start">
                            @error('date_start')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="w-100">
                            <label for="date_end" class="col-md-4 col-form-label ">Eind datum</label>
                            <input id="date_end" value="{{ old('date_end') }}" type="date"
                                   class="form-control @error('date_end') is-invalid @enderror" name="date_end">
                            @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div>

                        <label for="user-search" class="col-md-4 col-form-label w-100">Deelnemers van de les (extra
                            Praktijkbegeleiders zijn toe te voegen vanuit de lesomgeving)</label>


                        <div>
                            <input id="users" name="users" type="hidden" value="{{old('users')}}"
                                   class="user-select-window user-select-none form-control"
                                   placeholder="Kies een gebruiker uit de lijst"
                                   aria-label="user" aria-describedby="basic-addon1">
                            <div class="user-select-window-popup no-shadow d-none mt-2"
                                 style="position: unset; display: block !important;">
                                <h3>Selecteer deelnemers</h3>
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
                    </div>
                    @error('users')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror

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
                            <span class="button-text">Les aanmaken</span>
                            <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                                  aria-hidden="true"></span>
                            <span style="display: none" class="loading-text" role="status">Laden...</span>
                        </button>
                        <a class="btn btn-danger text-white" href="{{ route('lessons') }}">Annuleren</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
