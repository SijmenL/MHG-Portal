@extends('layouts.app')
@include('partials.editor')

@vite(['resources/js/texteditor.js', 'resources/js/search-user.js', 'resources/css/texteditor.css'])

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">
        <h1>Voeg een agendapunt toe</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('agenda') }}">Agenda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Voeg een agendapunt toe</li>
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
                <form method="POST" action="{{ route('agenda.new.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column">
                        <label for="title" class="col-md-4 col-form-label ">Titel</label>
                        <input name="title" type="text" class="form-control" id="title" value="{{ old('title') }}"
                        >
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex flex-row-responsive gap-2 justify-content-between align-items-center">
                        <div class="w-100">
                            <label for="date_start" class="col-md-4 col-form-label ">Start datum</label>
                            <input id="date_start" value="{{ old('date_start') }}" type="datetime-local"
                                   class="form-control @error('date_start') is-invalid @enderror" name="date_start">
                            @error('date_start')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="w-100">
                            <label for="date_end" class="col-md-4 col-form-label ">Eind datum</label>
                            <input id="date_end" value="{{ old('date_end') }}" type="datetime-local"
                                   class="form-control @error('date_end') is-invalid @enderror" name="date_end">
                            @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
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

                    <div class="w-100">
                        <label for="public" class="col-form-label ">Maak dit een openbare activiteit (Het zal ook op de normale website komen te staan als activiteit)</label>
                        <select id="public" type="text" class="form-select @error('public') is-invalid @enderror" name="public">
                            <option value="0">Nee</option>
                            <option value="1">Ja</option>
                        </select>
                        @error('public')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="text-input">De content van je bericht</label>
                        <div class="editor-parent">
                            @yield('editor')
                            <div id="text-input" contenteditable="true" name="text-input"
                                 class="text-input">{!! old('content') !!}</div>
                            <small id="characters"></small>
                        </div>

                        @error('content')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <input id="content" name="content" type="hidden" value="{{ old('content') }}">

                    <div class="d-flex flex-column mt-4 mb-2">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                            aria-expanded="false" aria-controls="collapseOne">
                                        <label for="select-roles" class="col-md-4 col-form-label ">Eventuele rollen
                                            waarvoor je agendapunt geldt</label>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="custom-select">
                                            <select id="select-roles" class="d-none" id="roles" name="roles[]"
                                                    multiple>
                                                @foreach($all_roles as $role)
                                                    <option data-description="{{ $role->description }}"
                                                            value="{{ $role->id }}">
                                                        {{ $role->role }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-flex flex-wrap gap-1" id="button-container">
                                        </div>

                                        <script>
                                            let select = document.getElementById('select-roles');
                                            let buttonContainer = document.getElementById('button-container');

                                            select.querySelectorAll('option').forEach(option => {
                                                const button = document.createElement('p');
                                                button.title = option.getAttribute('data-description');
                                                button.textContent = option.textContent;
                                                button.classList.add('btn', 'btn-secondary');
                                                button.dataset.value = option.value;

                                                if (option.selected) {
                                                    button.classList.add('btn-primary', 'text-white');
                                                    button.classList.remove('btn-secondary');
                                                }

                                                button.addEventListener('click', () => {
                                                    option.selected = !option.selected;
                                                    if (option.selected) {
                                                        button.classList.add('btn-primary', 'text-white');
                                                        button.classList.remove('btn-secondary');
                                                    } else {
                                                        button.classList.remove('btn-primary', 'text-white');
                                                        button.classList.add('btn-secondary');
                                                    }
                                                });

                                                buttonContainer.appendChild(button);
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwo" aria-expanded="false"
                                            aria-controls="collapseTwo">
                                        <label for="users" class="col-md-4 col-form-label ">Eventuele gebruikers
                                            waarvoor je agendapunt
                                            geldt</label>
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <input id="users" name="users" type="text"
                                               class="user-select-window form-control"
                                               placeholder="Zoeken op gebruikers id"
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
                            </div>
                        </div>


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

                    <button type="submit" class="btn btn-dark mt-3">Opslaan</button>
                </form>
            </div>
        </div>
    </div>
@endsection

