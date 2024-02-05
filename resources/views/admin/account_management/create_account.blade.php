@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Maak account</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin')}}">Administratie</a></li>
                <li class="breadcrumb-item active" aria-current="page">Maak account</li>
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

        <form method="POST" action="{{ route('admin.create-account-store') }}"
              enctype="multipart/form-data">
            @csrf
            <div class="overflow-scroll no-scrolbar" style="max-width: 100vw">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th><label for="name" class="col-md-4 col-form-label ">Voornaam</label></th>
                        <th>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" autocomplete="name" autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="infix" class="col-md-4 col-form-label ">Tussenvoegsel</label></th>
                        <th>
                            <input id="infix" type="text" class="form-control @error('infix') is-invalid @enderror"
                                   name="infix" value="{{ old('infix') }}" autocomplete="infix" autofocus>
                            @error('infix')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="last_name" class="col-md-4 col-form-label ">Achternaam</label></th>
                        <th>
                            <input id="last_name" type="text"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   name="last_name" value="{{ old('last_name') }}" autocomplete="last_name"
                                   autofocus>
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="dolfijnen_name" class="col-md-4 col-form-label ">Dolfijnen Naam</label></th>
                        <th>
                            <input id="dolfijnen_name" type="text"
                                   class="form-control @error('dolfijnen_name') is-invalid @enderror"
                                   name="dolfijnen_name" value="{{ old('dolfijnen_name') }}" autocomplete="dolfijnen_name"
                                   autofocus>
                            @error('dolfijnen_name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="email" class="col-md-4 col-form-label ">E-mail</label></th>
                        <th><input id="email" value="{{ old('email') }}" type="email" class="form-control @error('email') is-invalid @enderror" name="email"  autocomplete="email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="password" class="col-md-4 col-form-label ">Wachtwoord</label></th>
                        <th><input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="profile_picture" class="col-md-4 col-form-label ">Profielfoto</label></th>
                        <th>
                            <input class="form-control mt-2" value="{{ old('profile_picture') }}" id="profile_picture"
                                   type="file" name="profile_picture"
                                   accept="image/*">
                            @error('profile_picture')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                        <th>
                    </tr>
                    <tr>
                        <th><label for="roles" class="col-md-4 col-form-label ">Rollen</label></th>
                        <th>
                            <div class="custom-select">
                                <select id="select-roles" class="d-none" id="roles" name="roles[]" multiple>
                                    @foreach($all_roles as $role)
                                        <option data-description="{{ $role->description }}" value="{{ $role->id }}">
                                            {{ $role->role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex flex-wrap gap-1" id="button-container">
                            </div>

                        </th>
                    </tr>
                    <tr>
                        <th><label for="sex" class="col-md-4 col-form-label ">Geslacht</label></th>
                        <th>
                            <select id="sex" type="text" class="form-select @error('sex') is-invalid @enderror" name="sex" >
                                <option @if(old('sex') === 'Man') selected @endif >Man</option>
                                <option @if(old('sex') === 'Vrouw') selected @endif >Vrouw</option>
                                <option @if(old('sex') === 'Anders') selected @endif >Anders</option>
                            </select>
                            @error('sex')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="birth_date" class="col-md-4 col-form-label ">Geboortedatum</label></th>
                        <th><input id="birth_date" value="{{ old('birth_date') }}" type="date" class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" >
                            @error('birth_date')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="street" class="col-md-4 col-form-label ">Straat & huisnummer</label></th>
                        <th><input id="street" value="{{ old('street') }}" type="text" class="form-control @error('street') is-invalid @enderror" name="street" >
                            @error('street')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="postal_code" class="col-md-4 col-form-label ">Postcode</label></th>
                        <th><input id="postal_code" value="{{ old('postal_code') }}" type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" >
                            @error('postal_code')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="city" class="col-md-4 col-form-label ">Woonplaats</label></th>
                        <th><input id="city" value="{{ old('city') }}" type="text" class="form-control @error('city') is-invalid @enderror" name="city" >
                            @error('city')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="phone" class="col-md-4 col-form-label ">Telefoonnummer</label></th>
                        <th><input id="phone" value="{{ old('phone') }}" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" >
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="avg" class="col-md-4 col-form-label ">AVG Toestemming</label></th>
                        <th><select id="avg" type="text" class="form-select @error('avg') is-invalid @enderror" name="avg" >
                                <option @if(old('avg') === '0') selected @endif value="0">Nee</option>
                                <option @if(old('avg') === '1') selected @endif value="1">Ja</option>
                            </select>
                            @error('avg')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="member_date" class="col-md-4 col-form-label ">Lid sinds</label></th>
                        <th><input id="member_date" value="{{ old('member_date') }}"  type="date" class="form-control @error('member_date') is-invalid @enderror" name="member_date">
                            @error('member_date')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    </tbody>
                </table>

            </div>

            @if ($errors->any())
                <div class="text-danger">
                    <p>Er is iets misgegaan...</p>
                </div>
            @endif

            <div class="d-flex flex-row flex-wrap gap-2">
                <button type="submit" class="btn btn-success">Opslaan</button>
            </div>



        </form>
    </div>
@endsection
