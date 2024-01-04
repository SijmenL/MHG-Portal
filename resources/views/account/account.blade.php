@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Mijn Account</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mijn Account</li>
            </ol>
        </nav>
        <div class="alert alert-warning d-flex align-items-center"><span class="material-symbols-rounded pe-1">info</span>Let op! Het veranderen van je wachtwoord is op dit moment niet mogelijk.</div>
        <div>
            <form method="POST" action="{{ route('account.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Naam') }}</label>

                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ $user->name }}" autocomplete="name" autofocus>

                        @error('name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="infix" class="col-md-4 col-form-label text-md-end">{{ __('Tussenvoegsel') }}</label>
                    <div class="col-md-6">
                        <input id="infix" value="{{ $user->infix }}" type="text"
                               class="form-control @error('infix') is-invalid @enderror" name="infix">
                        @error('infix')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="last_name" class="col-md-4 col-form-label text-md-end">{{ __('Achternaam') }}</label>
                    <div class="col-md-6">
                        <input id="last_name" value="{{ $user->last_name }}" type="text"
                               class="form-control @error('last_name') is-invalid @enderror" name="last_name">
                        @error('last_name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="profile_picture" class="col-md-4 col-form-label text-md-end">Profiel foto</label>
                    <div class="col-md-6">
                        <input class="form-control" value="{{ $user->profile_picture }}" id="profile_picture" type="file" name="profile_picture"
                               accept="image/*">
                        @error('profile_picture')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="sex" class="col-md-4 col-form-label text-md-end">{{ __('Geslacht') }}</label>
                    <div class="col-md-6">
                        <select id="sex" type="text" class="form-control @error('sex') is-invalid @enderror" name="sex">
                            <option name="man" @if($user->sex === 'Man') selected @endif>Man</option>
                            <option name="vrouw" @if($user->sex === 'Vrouw') selected @endif>Vrouw</option>
                            <option name="anders" @if($user->sex === 'Anders') selected @endif>Anders</option>
                        </select>

                        @error('sex')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="birth_date"
                           class="col-md-4 col-form-label text-md-end">{{ __('Geboortedatum') }}</label>
                    <div class="col-md-6">
                        <input id="birth_date" value="{{ $user->birth_date }}" type="date"
                               class="form-control @error('birth_date') is-invalid @enderror" name="birth_date">

                        @error('birth_date')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="street"
                           class="col-md-4 col-form-label text-md-end">{{ __('Straat & Huisnummer') }}</label>
                    <div class="col-md-6">
                        <input id="street" value="{{ $user->street }}" type="text"
                               class="form-control @error('street') is-invalid @enderror" name="street">
                        @error('street')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="postal_code" class="col-md-4 col-form-label text-md-end">{{ __('Postcode') }}</label>
                    <div class="col-md-6">
                        <input id="postal_code" value="{{ $user->postal_code }}" type="text"
                               class="form-control @error('postal_code') is-invalid @enderror" name="postal_code">
                        @error('postal_code')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="city" class="col-md-4 col-form-label text-md-end">{{ __('Woonplaats') }}</label>
                    <div class="col-md-6">
                        <input id="city" type="text" value="{{ $user->city }}"
                               class="form-control @error('city') is-invalid @enderror" name="city">
                        @error('city')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('Telefoonnummer') }}</label>
                    <div class="col-md-6">
                        <input id="phone" type="text" value="{{ $user->phone }}"
                               class="form-control @error('phone') is-invalid @enderror" name="phone">
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Addres') }}</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ $user->email }}" autocomplete="email">

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Wachtwoord') }}</label>

                    <div class="col-md-6">
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror" name="password"
                               autocomplete="new-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password-confirm"
                           class="col-md-4 col-form-label text-md-end">{{ __('Herhaal wachtwoord') }}</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                               autocomplete="new-password">
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Opslaan') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection
