@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Maak ouder account</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings')}}">Instellingen</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings.parent')}}">Maak of koppel ouder
                        account</a></li>
                <li class="breadcrumb-item active" aria-current="page">Maak ouder account</li>
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

        <div class="bg-light rounded-2 p-3">
            <h2>Maak een account</h2>
            <p>Maak een nieuw ouder account aan. We koppelen automatisch dit nieuwe account als ouder aan jouw account. Als ouder is het mogelijk om de persoonsgegevens van het kind te bekijken en aan te
                passen. Ook krijgt de ouder toegang tot de speltak omgeving van het kind.</p>
            <p class="text-danger">Alleen de ouder kan de koppeling weer ongedaan maken, tenzij je Loods of After Loods
                bent.</p>
            <form method="POST" action="{{ route('settings.link-new-parent.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="container">
                    <div class="row">
                        <div class="col">
                            <label for="name" class="col-md-4 col-form-label ">Voornaam</label>

                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" autocomplete="name" autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>
                        <div class="col">
                            <label for="infix" class="col-md-4 col-form-label ">Tussenvoegsel</label>

                            <input id="infix" type="text" class="form-control @error('infix') is-invalid @enderror"
                                   name="infix" value="{{ old('infix') }}" autocomplete="infix" autofocus>
                            @error('infix')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>
                        <div class="col">
                            <label for="last_name" class="col-md-4 col-form-label ">Achternaam</label>

                            <input id="last_name" type="text"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   name="last_name" value="{{ old('last_name') }}" autocomplete="last_name"
                                   autofocus>
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="email" class="col-md-4 col-form-label ">E-mail</label>
                        <input id="email" value="{{ old('email') }}" type="email"
                               class="form-control @error('email') is-invalid @enderror" name="email" autocomplete="email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="col-md-4 col-form-label ">Wachtwoord</label>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror" name="password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="street" class="col-form-label ">Straat & huisnummer</label>
                            <input id="street" value="{{ old('street') }}" type="text"
                                   class="form-control @error('street') is-invalid @enderror" name="street">
                            @error('street')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="postal_code" class="col-md-4 col-form-label ">Postcode</label>
                            <input id="postal_code" value="{{ old('postal_code') }}" type="text"
                                   class="form-control @error('postal_code') is-invalid @enderror" name="postal_code">
                            @error('postal_code')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="city" class="col-md-4 col-form-label ">Woonplaats</label>
                            <input id="city" value="{{ old('city') }}" type="text"
                                   class="form-control @error('city') is-invalid @enderror" name="city">
                            @error('city')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="phone" class="col-md-4 col-form-label ">Telefoonnummer</label>
                        <input id="phone" value="{{ old('phone') }}" type="text"
                               class="form-control @error('phone') is-invalid @enderror" name="phone">
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="">
                        <label for="profile_picture" class="col-md-4 col-form-label ">Profielfoto</label>

                        <div class="d-flex flex-row-responsive gap-4 align-items-center justify-content-center">
                            <input class="form-control mt-2 col" value="{{ old('profile_picture') }}" id="profile_picture"
                                   type="file" name="profile_picture"
                                   accept="image/*">
                            @error('profile_picture')
                        </div>
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror

                    </div>
                    <div>
                        <label for="sex" class="col-md-4 col-form-label ">Geslacht</label>

                        <select id="sex" type="text" class="form-select @error('sex') is-invalid @enderror" name="sex">
                            <option @if(old('sex') === 'Man') selected @endif >Man</option>
                            <option @if(old('sex') === 'Vrouw') selected @endif >Vrouw</option>
                            <option @if(old('sex') === 'Anders') selected @endif >Anders</option>
                        </select>
                        @error('sex')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror

                    </div>
                    <div>
                        <label for="birth_date" class="col-md-4 col-form-label ">Geboortedatum</label>
                        <input id="birth_date" value="{{ old('birth_date') }}" type="date"
                               class="form-control @error('birth_date') is-invalid @enderror" name="birth_date">
                        @error('birth_date')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div>
                        <label for="avg" class="col-md-4 col-form-label ">AVG Toestemming</label>
                        <select id="avg" type="text" class="form-select @error('avg') is-invalid @enderror" name="avg">
                            <option @if(old('avg') === 1) selected @endif value="1">Ja</option>
                            <option @if(old('avg') === 0) selected @endif value="0">Nee</option>
                        </select>
                        @error('avg')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                </div>

                @if ($errors->any())
                    <div class="text-danger">
                        <p>Er is iets misgegaan...</p>
                    </div>
                @endif

                <div class="d-flex flex-row flex-wrap gap-2 mt-3">
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
                    <a href="{{ route('settings.parent') }}"
                       class="btn btn-danger text-white">Annuleren</a>
                </div>

            </form>
        </div>
    </div>
@endsection
