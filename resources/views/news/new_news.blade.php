@extends('layouts.app')
@include('partials.editor')

@vite(['resources/js/texteditor.js', 'resources/css/texteditor.css'])

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">
        <h1>Publiceer een nieuwtje</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('news') }}">Nieuws</a></li>
                <li class="breadcrumb-item active" aria-current="page">Publiceer een nieuwtje</li>
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

        <p>Het kan zijn dat je nieuws niet meteen op de site te vinden is, we kijken je item eerst na voordat we hem
            publiceren. Onder Mijn Inzendingen kun je al je items zien en bewerken.</p>

        <div class="bg-light rounded-2 p-3">
            <div class="container">
                <form method="POST" action="{{ route('news.new.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column">
                        <label for="title" class="col-md-4 col-form-label ">Titel van je bericht</label>
                        <input name="title" type="text" class="form-control" id="title" value="{{ old('title') }}"
                        >
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex flex-column">
                        <label for="category" class="col-md-4 col-form-label ">Categorie waar je bericht invalt</label>
                        <select id="category"
                                class="w-100 form-select @error('category') is-invalid @enderror"
                                name="category">
                            <option value="Post" {{ old('category') == 'Post' ? 'selected' : '' }}>Post</option>
                            <option value="Artikel" {{ old('category') == 'Artikel' ? 'selected' : '' }}>Artikel
                            </option>
                            <option value="Update" {{ old('category') == 'Update' ? 'selected' : '' }}>Update</option>
                        </select>
                        @error('category')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex flex-column">
                        <label for="speltak" class="col-md-4 col-form-label ">Eventuele speltakken die bij je bericht
                            passen</label>
                        <div class="custom-select">
                            <select id="speltak" class="d-none" name="speltak[]" multiple>
                                <option data-description="Dolfijnen"
                                        value="dolfijnen" {{ in_array('dolfijnen', old('speltak', [])) ? 'selected' : '' }}>
                                    Dolfijnen
                                </option>
                                <option data-description="Zeeverkenners"
                                        value="zeeverkenners" {{ in_array('zeeverkenners', old('speltak', [])) ? 'selected' : '' }}>
                                    Zeeverkenners
                                </option>
                                <option data-description="Loodsen"
                                        value="loodsen" {{ in_array('loodsen', old('speltak', [])) ? 'selected' : '' }}>
                                    Loodsen
                                </option>
                                <option data-description="Afterloodsen"
                                        value="afterloodsen" {{ in_array('afterloodsen', old('speltak', [])) ? 'selected' : '' }}>
                                    Afterloodsen
                                </option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap gap-1" id="button-container">
                        </div>

                        <script>
                            let select = document.getElementById('speltak');
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

                    <div class="d-flex flex-column">
                        <label for="date" class="col-md-4 col-form-label ">Publicatiedatum</label>
                        <input name="date" type="date" class="form-control" id="date" value="{{ old('date') }}"
                        >
                        @error('date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex flex-column">
                        <label for="description" class="col-md-4 col-form-label ">Korte samenvatting of
                            beschrijving</label>
                        <input name="description" type="text" class="form-control" id="description"
                               value="{{ old('description') }}"
                        >
                        @error('description')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small id="characters2">0/200</small>
                    </div>

                    <script>
                        let textInput2 = document.getElementById('description')
                        let characters2 = document.getElementById('characters2')

                        addEventListener('input', function () {

                            characters2.innerHTML = `${textInput2.value.toString().length}/200`;

                            if (textInput2.value.toString().length > 200) {
                                characters2.style.color = 'red';
                            } else {
                                characters2.style.color = 'black';
                            }
                        });

                    </script>

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

                    <button type="submit" class="btn btn-dark mt-3">Opslaan</button>
                </form>
            </div>
        </div>

    </div>
@endsection

