@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Details {{$account->name}} {{$account->infix}} {{$account->last_name}}</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin')}}">Administratie</a></li>
                <li class="breadcrumb-item" aria-current="page"><a
                        href="{{route('admin.account-management')}}">Gebruikers</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Details {{$account->name}} {{$account->infix}} {{$account->last_name}}</li>
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

        <div class="overflow-scroll no-scrolbar" style="max-width: 100vw">
            <table class="table table-striped">
                <tbody>
                <tr>
                    <th>Voornaam</th>
                    <th>{{ $account->name }}</th>
                </tr>
                <tr>
                    <th>Tussenvoegsel</th>
                    <th>{{ $account->infix }}</th>
                </tr>
                <tr>
                    <th>Achternaam</th>
                    <th>{{ $account->last_name }}</th>
                </tr>
                @if($account->profile_picture)
                    <tr>
                        <th>Profielfoto</th>
                        <th>
                            <img alt="profielfoto" class="w-25"
                                 src="{{ asset('/profile_pictures/' . $account->profile_picture) }}">
                        <th>
                    </tr>
                @endif
                <tr>
                    <th>Geslacht</th>
                    <th>{{ $account->sex }}</th>
                </tr>
                <tr>
                    <th>Geboortedatum</th>
                    <th>{{ \Carbon\Carbon::parse($account->birth_date)->format('d-m-Y') }}</th>
                </tr>
                <tr>
                    <th>Straat & huisnummer</th>
                    <th>{{ $account->street }}</th>
                </tr>
                <tr>
                    <th>Postcode</th>
                    <th>{{ $account->postal_code }}</th>
                </tr>
                <tr>
                    <th>Woonplaats</th>
                    <th>{{ $account->city }}</th>
                </tr>
                <tr>
                    <th>Telefoonnummer</th>
                    <th>{{ $account->phone }}</th>
                </tr>
                <tr>
                    <th>E-mail</th>
                    <th>{{ $account->email }}</th>
                </tr>
                <tr>
                    <th>AVG Toestemming</th>
                    <th>@if($account->avg)
                            Ja
                        @else
                            Nee
                        @endif</th>
                </tr>
                <tr>
                    <th>Lid sinds</th>
                    <th> @if($account->member_date)
                            {{ Carbon\Carbon::parse($account->member_date)->format('d-m-Y') }}
                        @endif
                    </th>
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

        <div class="d-flex flex-row flex-wrap gap-2">
            <a href="{{ route('admin.account-management')}}" class="btn btn-info">Terug</a>
            <a href="{{ route('admin.account-management.edit', ['id' => $account->id]) }}" class="btn btn-dark">Bewerk</a>
            <a class="delete-button btn btn-outline-danger"
               data-id="{{ $account->id }}"
               data-name="{{ $account->name . ' ' . $account->infix . ' ' . $account->last_name }}"
               data-link="{{ route('admin.account-management.delete', $account->id) }}">Verwijderen</a>

        </div>
    </div>
@endsection
