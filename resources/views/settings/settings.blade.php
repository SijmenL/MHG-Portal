@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Instellingen</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Instellingen</li>
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
            <h2 class="text-center">Instellingen</h2>
            <div class="settings-container">
                <a class="setting" href="{{ route('settings.account.edit') }}">
                    <div class="setting-text">
                        <div>
                            <h3>Persoonlijke informatie</h3>
                            <small>Pas je persoonlijke informatie aan zoals bijvoorbeeld je adres, telefoonnummer of profielfoto</small>
                        </div>
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </div>
                </a>
                <div class="devider"></div>
                <a class="setting" href="{{ route('settings.change-password') }}">
                    <div class="setting-text">
                        <div>
                            <h3>Verander wachtwoord</h3>
                            <small>Pas je wachtwoord aan</small>
                        </div>
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </div>
                </a>
                <div class="devider"></div>
                <a class="setting" href="{{ route('settings.edit-notifications') }}">
                    <div class="setting-text">
                        <div>
                            <h3>Notificaties</h3>
                            <small>Pas aan welke notificaties je via de mail en de app wilt krijgen</small>
                        </div>
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </div>
                </a>

                @if($user->accepted === 1)
                <div class="devider"></div>
                <a class="setting" href="{{ route('settings.parent') }}">
                    <div class="setting-text">
                        <div>
                            <h3>Maak of koppel ouder account</h3>
                            <small>Maak een account aan voor je ouders en/of verzorgers, zodat ze geen belangrijke
                                informatie
                                missen!</small>
                        </div>
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </div>
                </a>
                @if($user->parents->count() > 0 && !auth()->user()->roles->contains('role', 'Dolfijn') && !auth()->user()->roles->contains('role', 'Zeeverkenner'))
                <div class="devider"></div>
                    <a class="setting" href="{{ route('settings.remove-parent-link') }}">
                        <div class="setting-text">
                            <div>
                                <h3 class="text-danger">Verwijder ouder koppeling</h3>
                                <small>Verwijder de koppeling tussen jouw account en de accounts van je ouders en/of
                                    verzorgers.</small>
                            </div>
                            <span class="material-symbols-rounded">arrow_forward_ios</span>
                        </div>

                    </a>
                @endif
                @if($user->children->count() > 0)
                    <div class="devider"></div>
                    <a class="setting" href="{{ route('settings.remove-child-link') }}">
                        <div class="setting-text">
                            <div>
                                <h3 class="text-danger">Verwijder kind koppeling</h3>
                                <small>Verwijder de koppeling tussen jouw account en de accounts van je
                                    kinderen.</small>
                            </div>
                            <span class="material-symbols-rounded">arrow_forward_ios</span>
                        </div>

                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
@endsection
