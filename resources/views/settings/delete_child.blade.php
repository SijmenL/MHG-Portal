@extends('layouts.app')

@section('content')
    @if(Session::has('continue'))
        <div class="popup" style="margin-top: -122px">
            <div class="popup-body">
                <h2>Weet je zeker dat je de kind koppeling wilt verwijderen?</h2>
                <p>De koppeling
                    met {{ Session::get('continue')['name'] }} {{ Session::get('continue')['infix'] }} {{ Session::get('continue')['last_name'] }}
                    zal per direct verwijderd worden. Beide accounts blijven gewoon bestaan.</p>
                <div class="button-container">
                    <a class="btn btn-success"
                       href="{{ route('settings.remove-child-link.confirm', ['id' => Session::get('continue')['id']]) }}">Ja,
                        ontkoppel</a>
                    <a class="btn btn-outline-danger" href="{{ route('settings.remove-child-link') }}">Nee, annuleren</a>
                </div>
            </div>
        </div>
    @endif


    <div class="container col-md-11">
        <h1>Verwijder kind koppeling</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings')}}">Instellingen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Verwijder kind koppeling</li>
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
            <h2>Verwijder een koppling</h2>
            <p>De koppeling tussen het kind account en dit account wordt verwijderd. Je kan niet meer meekijken
                en kan ook geen informatie meer bewerken. Je kan altijd opnieuw gekoppeld worden door het kind en het
                kind en dit account worden niet verwijderd.</p>

            <div class="settings-container">

                <div class="parent-links">
                    @foreach ($user->children as $index => $child)
                        @if ($index > 0)
                            <div class="devider"></div>
                        @endif
                        <a class="setting" href="{{ route('settings.remove-child-link.id', ['id' => $child->id]) }}">
                            <div class="setting-text">
                                <div class="d-flex flex-row-responsive gap-4 align-items-center">
                                    @if($child->profile_picture)
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('/profile_pictures/' . $child->profile_picture) }}">
                                    @else
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('img/no_profile_picture.webp') }}">
                                    @endif
                                    <div>
                                        <h3 class="text-danger">Ontkoppel {{ $child->name.' '.$child->infix.' '.$child->last_name }}</h3>
                                        <small>{{ $child->email }}</small>
                                    </div>
                                </div>
                                <span class="material-symbols-rounded">delete</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="d-flex flex-row flex-wrap gap-2 mt-3">
                <a href="{{ route('settings') }}"
                   class="btn btn-dark">Terug</a>
            </div>
        </div>
    </div>
@endsection
