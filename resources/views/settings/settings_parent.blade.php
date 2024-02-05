@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Maak of koppel ouder account</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings') }}">Instellingen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Maak of koppel ouder account</li>
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
            <h2>Instellingen</h2>
            <div class="settings-container">
                @if($user->parents()->count() > 0)
                <div class="bg-info p-2">
                    <h4>Je bent al gekoppeld aan een of meer ouder ouder accounts:</h4>
                    <div class="d-flex flex-wrap flex-row gap-2 align-items-center justify-content-center">
                        @foreach ($user->parents as $parent)
                            <div class="d-flex flex-column gap-1 align-items-center m-2 bg-info-subtle p-2 rounded"
                                 target="_blank">
                                @if($parent->profile_picture)
                                    <img alt="profielfoto" class="profle-picture"
                                         src="{{ asset('/profile_pictures/' . $parent->profile_picture) }}">
                                @else
                                    <img alt="profielfoto" class="profle-picture"
                                         src="{{ asset('img/no_profile_picture.webp') }}">
                                @endif
                                <span>{{ $parent->name.' '.$parent->infix.' '.$parent->last_name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                <a class="setting" href="{{ route('settings.link-parent') }}">
                    <div class="setting-text">
                        <div>
                            <h3>Koppel een bestaand account</h3>
                            <small>Kies een al bestaand MHG account die als ouder aan dit account wordt
                                gekoppeld</small>
                        </div>
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </div>
                </a>
                <div class="devider"></div>
                <a class="setting" href="{{ route('settings.link-new-parent.create') }}">
                    <div class="setting-text">
                        <div>
                            <h3>Maak een nieuw ouder account</h3>
                            <small>Maak een geheel nieuw account aan die als ouder aan dit account wordt
                                gekoppeld.</small>
                        </div>
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </div>
                </a>
            </div>
            <div class="d-flex flex-row flex-wrap gap-2 mt-3">
                <a href="{{ route('settings') }}"
                   class="btn btn-dark">Terug</a>
            </div>
        </div>
@endsection
