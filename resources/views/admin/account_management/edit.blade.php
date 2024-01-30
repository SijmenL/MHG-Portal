@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Bewerk {{$account->name}} {{$account->infix}} {{$account->last_name}}</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin')}}">Administratie</a></li>
                <li class="breadcrumb-item" aria-current="page"><a
                        href="{{route('admin.account-management')}}">Gebruikers</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Bewerk {{$account->name}} {{$account->infix}} {{$account->last_name}}</li>
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

        <form method="POST" action="{{ route('admin.account-management.store', $account) }}"
              enctype="multipart/form-data">
            @csrf
            <div class="overflow-scroll no-scrolbar" style="max-width: 100vw">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th><label for="name" class="col-md-4 col-form-label text-md-end">Voornaam</label></th>
                        <th>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ $account->name }}" autocomplete="name" autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="infix" class="col-md-4 col-form-label text-md-end">Tussenvoegsel</label></th>
                        <th>
                            <input id="infix" type="text" class="form-control @error('infix') is-invalid @enderror"
                                   name="infix" value="{{ $account->infix }}" autocomplete="infix" autofocus>
                            @error('infix')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="last_name" class="col-md-4 col-form-label text-md-end">Achternaam</label></th>
                        <th>
                            <input id="last_name" type="text"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   name="last_name" value="{{ $account->last_name }}" autocomplete="last_name"
                                   autofocus>
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="email" class="col-md-4 col-form-label text-md-end">E-mail</label></th>
                        <th><input id="email" value="{{ $account->email }}" type="email" class="form-control @error('email') is-invalid @enderror" name="email"  autocomplete="email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="profile_picture" class="col-md-4 col-form-label text-md-end">Profielfoto</label></th>
                        <th>
                            @if($account->profile_picture)
                                <img alt="profielfoto" class="w-25"
                                     src="{{ asset('/profile_pictures/' .$account->profile_picture) }}">
                              @endif
                                <input class="form-control mt-2" value="{{ $account->profile_picture }}" id="profile_picture"
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
                        <th><label for="sex" class="col-md-4 col-form-label text-md-end">Geslacht</label></th>
                        <th>
                            <select id="sex" type="text" class="form-select @error('sex') is-invalid @enderror" name="sex" >
                                <option @if($account->sex === 'Man') selected @endif >Man</option>
                                <option @if($account->sex === 'Vrouw') selected @endif >Vrouw</option>
                                <option @if($account->sex === 'Anders') selected @endif >Anders</option>
                            </select>
                            @error('sex')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </th>
                    </tr>
                    <tr>
                        <th><label for="birth_date" class="col-md-4 col-form-label text-md-end">Geboortedatum</label></th>
                        <th><input id="birth_date" value="{{ $account->birth_date }}" type="date" class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" >
                            @error('birth_date')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="street" class="col-md-4 col-form-label text-md-end">Straat & huisnummer</label></th>
                        <th><input id="street" value="{{ $account->street }}" type="text" class="form-control @error('street') is-invalid @enderror" name="street" >
                            @error('street')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="postal_code" class="col-md-4 col-form-label text-md-end">Postcode</label></th>
                        <th><input id="postal_code" value="{{ $account->postal_code }}" type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" >
                            @error('postal_code')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="city" class="col-md-4 col-form-label text-md-end">Woonplaats</label></th>
                        <th><input id="city" value="{{ $account->city }}" type="text" class="form-control @error('city') is-invalid @enderror" name="city" >
                            @error('city')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="phone" class="col-md-4 col-form-label text-md-end">Telefoonnummer</label></th>
                        <th><input id="phone" value="{{ $account->phone }}" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" >
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="avg" class="col-md-4 col-form-label text-md-end">AVG Toestemming</label></th>
                        <th><select id="avg" type="text" class="form-select @error('avg') is-invalid @enderror" name="avg" >
                                <option @if($account->avg === '1') selected @endif value="1">Ja</option>
                                <option @if($account->avg === '0') selected @endif value="0">Nee</option>
                            </select>
                            @error('avg')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th><label for="member_date" class="col-md-4 col-form-label text-md-end">Lid sinds</label></th>
                        <th><input id="member_date" value="{{ $account->member_date }}" type="date" class="form-control @error('member_date') is-invalid @enderror" name="member_date">
                            @error('member_date')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror</th>
                    </tr>
                    <tr>
                        <th>Aangepast op</th>
                        <th>{{ \Carbon\Carbon::parse($account->updated_at)->format('d-m-Y H:i:s') }}</th>
                    </tr>
                    <tr>
                        <th>Aangemaakt op</th>
                        <th>{{ \Carbon\Carbon::parse($account->created_at)->format('d-m-Y H:i:s') }}</th>
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
                <a href="{{ route('admin.account-management.details', ['id' => $account->id]) }}"
                   class="btn btn-outline-danger">Annuleren</a>
            </div>



        </form>
    </div>
@endsection
