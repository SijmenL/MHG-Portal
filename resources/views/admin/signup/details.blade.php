@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Details @if($account !== null) {{$account->name}} {{$account->infix}} {{$account->last_name}}@endif</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin')}}">Administratie</a></li>
                <li class="breadcrumb-item" aria-current="page"><a
                        href="{{route('admin.signup')}}">Inschrijvingen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details @if($account !== null) {{$account->name}} {{$account->infix}} {{$account->last_name}}@endif</li>
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

        @if($account !== null)
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
                    <tr>
                        <th>Dolfijnen Naam</th>
                        <th>{{ $account->dolfijnen_name }}</th>
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
                    @if($account->roles->count() > 0)
                        <tr>
                            <th>Rollen</th>
                            <th>
                                @foreach ($account->roles as $role)
                                    <span title="{{ $role->description }}"
                                          class="badge rounded-pill text-bg-primary text-white fs-6 p-2">{{ $role->role }}</span>
                                @endforeach
                            </th>
                        </tr>
                    @endif
                    @if($account->children->count() > 0)
                        <tr>
                            <th>Kinderen</th>
                            <th class="d-flex flex-wrap flex-row gap-2">
                                @foreach ($account->children as $child)
                                    <a href="{{ route('admin.account-management.details', ['id' => $child->id]) }}"
                                       class="d-flex flex-column gap-1 align-items-center m-2 bg-light p-2 rounded" target="_blank">
                                        @if($child->profile_picture)
                                            <img alt="profielfoto" class="profle-picture"
                                                 src="{{ asset('/profile_pictures/' . $child->profile_picture) }}">
                                        @else
                                            <img alt="profielfoto" class="profle-picture"
                                                 src="{{ asset('img/no_profile_picture.webp') }}">
                                        @endif
                                        {{ $child->name.' '.$child->infic.' '.$child->last_name }}
                                    </a>
                                @endforeach
                            </th>
                        </tr>
                    @endif
                    @if($account->parents->count() > 0)
                        <tr>
                            <th>Ouders</th>
                            <th class="d-flex flex-wrap flex-row gap-2">
                                @foreach ($account->parents as $parent)
                                    <a href="{{ route('admin.account-management.details', ['id' => $parent->id]) }}"
                                       class="d-flex flex-column gap-1 align-items-center m-2 bg-light p-2 rounded" target="_blank">
                                        @if($parent->profile_picture)
                                            <img alt="profielfoto" class="profle-picture"
                                                 src="{{ asset('/profile_pictures/' . $parent->profile_picture) }}">
                                        @else
                                            <img alt="profielfoto" class="profle-picture"
                                                 src="{{ asset('img/no_profile_picture.webp') }}">
                                        @endif
                                        {{ $parent->name.' '.$parent->infic.' '.$parent->last_name }}
                                    </a>
                                @endforeach
                            </th>
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
                        <th><a href="tel:{{ $account->phone }}">{{ $account->phone }}</a></th>
                    </tr>
                    <tr>
                        <th>E-mail</th>
                        <th><a href="mailto:{{ $account->email }}">{{ $account->email }}</a></th>
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
        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">person_off</span>Geen account gevonden...
            </div>
        @endif

        <div class="d-flex flex-row flex-wrap gap-2">
            <a href="{{ route('admin.signup') }}" class="btn btn-info">Terug</a>
        </div>
    </div>
@endsection
