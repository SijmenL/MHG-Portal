@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Persoonlijke informatie</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings')}}">Instellingen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Persoonlijke informatie</li>
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
            <form method="POST" action="{{ route('settings.account.store') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="container">
                    <div class="mt-4">
                        <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">person</span>Algemene
                            Gegevens</h2>
                        <div class="d-flex flex-row-responsive">
                            <div class="m-4 d-flex align-items-center justify-content-center">
                                @if($user->profile_picture)
                                    <img class="zoomable-image" alt="profielfoto" style="width: 100%; min-width: 25px; max-width: 250px"
                                         src="{{ asset('/profile_pictures/' .$user->profile_picture) }}">
                                @else
                                    <img alt="profielfoto" style="width: 100%; min-width: 25px; max-width: 250px"
                                         src="{{ asset('/img/no_profile_picture.webp') }}">
                                @endif
                            </div>
                            <div class="w-100">

                                <div class="row">
                                    <div class="col">
                                        <label for="profile_picture"
                                               class="col-md-4 col-form-label ">Profielfoto</label>
                                        <input class="form-control mt-2 col" value="{{ $user->profile_picture }}"
                                               id="profile_picture"
                                               type="file" name="profile_picture"
                                               accept="image/*">
                                        @error('profile_picture')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col">
                                        <label for="name" class="col-md-4 col-form-label ">Voornaam</label>

                                        <input id="name" type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               name="name" value="{{ $user->name }}" autocomplete="name" autofocus>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror

                                    </div>
                                    <div class="col">
                                        <label for="infix" class="col-md-4 col-form-label ">Tussenvoegsel</label>

                                        <input id="infix" type="text"
                                               class="form-control @error('infix') is-invalid @enderror"
                                               name="infix" value="{{ $user->infix }}" autocomplete="infix" autofocus>
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
                                               name="last_name" value="{{ $user->last_name }}" autocomplete="last_name"
                                               autofocus>
                                        @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="sex" class="col-md-4 col-form-label ">Geslacht</label>

                                        <select id="sex" type="text"
                                                class="form-select @error('sex') is-invalid @enderror"
                                                name="sex">
                                            <option @if($user->sex === 'Man') selected @endif >Man</option>
                                            <option @if($user->sex === 'Vrouw') selected @endif >Vrouw</option>
                                            <option @if($user->sex === 'Anders') selected @endif >Anders</option>
                                        </select>
                                        @error('sex')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror

                                    </div>
                                    <div class="col">
                                        <label for="birth_date" class="col-md-4 col-form-label ">Geboortedatum</label>
                                        <input id="birth_date" value="{{ $user->birth_date }}" type="date"
                                               class="form-control @error('birth_date') is-invalid @enderror"
                                               name="birth_date">
                                        @error('birth_date')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">call</span>Contact Gegevens
                    </h2>
                    <div class="col">
                        <label for="email" class="col-md-4 col-form-label ">E-mail</label>
                        <input id="email" value="{{ $user->email }}" type="email"
                               class="form-control @error('email') is-invalid @enderror" name="email"
                               autocomplete="email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="row align-items-end">
                        <div class="col">
                            <label for="street" class="col-form-label ">Straat & huisnummer</label>
                            <input id="street" value="{{ $user->street }}" type="text"
                                   class="form-control @error('street') is-invalid @enderror" name="street">
                            @error('street')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="postal_code" class="col-md-4 col-form-label ">Postcode</label>
                            <input id="postal_code" value="{{ $user->postal_code }}" type="text"
                                   class="form-control @error('postal_code') is-invalid @enderror" name="postal_code">
                            @error('postal_code')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="city" class="col-md-4 col-form-label ">Woonplaats</label>
                            <input id="city" value="{{ $user->city }}" type="text"
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
                        <input id="phone" value="{{ $user->phone }}" type="text"
                               class="form-control @error('phone') is-invalid @enderror" name="phone">
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">security</span>Algemene
                        Voorwaarden</h2>
                    <div>
                        <label for="avg" class="col-md-4 col-form-label ">AVG Toestemming</label>
                        <select id="avg" type="text" class="form-select @error('avg') is-invalid @enderror" name="avg">
                            <option @if($user->avg === 1) selected @endif value="1">Ja</option>
                            <option @if($user->avg === 0) selected @endif value="0">Nee</option>
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
                    <button type="submit" class="btn btn-success">Opslaan</button>
                    <a href="{{ route('settings') }}"
                       class="btn btn-danger text-white">Annuleren</a>
                </div>

            </form>
        </div>
    </div>
@endsection
