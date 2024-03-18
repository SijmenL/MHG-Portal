@extends('layouts.app')

@section('content')
    <div class="header" style="background-image: url({{ asset('img/general/MHG_vloot.jpg') }})">
        <div>
            <p class="header-title">Ledenportaal</p>
            <p class="header-text">Welkom op de digitale omgeving van de Matthijs Heldt Groep! </p>
        </div>
    </div>
    <div class="container col-md-11">
        <h1>Welkom, {{ $user->name }}</h1>
        <p>{{ $date }}</p>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-light rounded-2 p-3">
            <h2>Snelle Acties</h2>
            <div class="quick-action-bar">

                @if(auth()->user() && auth()->user()->roles->contains('role', 'Administratie'))
                    <a class="btn btn-admin quick-action" href="{{ route('admin') }}">
                        <span class="material-symbols-rounded">admin_panel_settings</span>
                        <p>Administratie</p>
                    </a>
                @endif

                <a class="btn btn-info quick-action" href="{{ route('settings') }}">
                    <span class="material-symbols-rounded">settings</span>
                    <p>Instellingen</p>
                </a>

                @if(auth()->user()->children()->count() > 0)
                    <a class="btn btn-info quick-action" href="{{ route('children') }}">
                        <span class="material-symbols-rounded">family_restroom</span>
                        <p>Mijn kinderen</p>
                    </a>
                @endif


                @if(auth()->user() &&
                    (auth()->user()->roles->contains('role', 'Dolfijnen Leiding') ||
                    auth()->user()->roles->contains('role', 'Zeeverkenners Leiding') ||
                    auth()->user()->roles->contains('role', 'Loodsen Stamoudste') ||
                    auth()->user()->roles->contains('role', 'Afterloodsen Organisator') ||
                    auth()->user()->roles->contains('role', 'Vrijwilliger') ||
                    auth()->user()->roles->contains('role', 'Administratie') ||
                    auth()->user()->roles->contains('role', 'Bestuur') ||
                    auth()->user()->roles->contains('role', 'Ouderraad'))
                    )
                    <a class="btn btn-info quick-action" href="{{ route('leiding') }}">
                        <span class="material-symbols-rounded">supervisor_account</span>
                        <p>Leiding & Organisatie</p>
                    </a>
                @endif


                @if(auth()->user() &&
                    (auth()->user()->roles->contains('role', 'Dolfijn') ||
                    auth()->user()->roles->contains('role', 'Dolfijnen Leiding') ||
                    auth()->user()->roles->contains('role', 'Administratie') ||
                    auth()->user()->roles->contains('role', 'Bestuur') ||
                    auth()->user()->roles->contains('role', 'Ouderraad') ||
                    auth()->user()->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'Dolfijn');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('dolfijnen') }}">
                        <img alt="dolfijnen" src="{{ asset('img/icons/dolfijnen.png') }}">
                        <p>Dolfijnen</p>
                    </a>
                @endif
                @if(auth()->user() &&
                    (auth()->user()->roles->contains('role', 'Zeeverkenner') ||
                    auth()->user()->roles->contains('role', 'Zeeverkenners Leding') ||
                    auth()->user()->roles->contains('role', 'Administratie') ||
                    auth()->user()->roles->contains('role', 'Bestuur') ||
                    auth()->user()->roles->contains('role', 'Ouderraad') ||
                    auth()->user()->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'Zeeverkenner');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('zeeverkenners') }}">
                        <img alt="dolfijnen" src="{{ asset('img/icons/zeeverkenners.png') }}">
                        <p>Zeeverkenners</p>
                    </a>
                @endif
                @if(auth()->user() &&
                    (auth()->user()->roles->contains('role', 'Loods') ||
                    auth()->user()->roles->contains('role', 'Loodsen Stamoudste') ||
                    auth()->user()->roles->contains('role', 'Administratie') ||
                    auth()->user()->roles->contains('role', 'Bestuur') ||
                    auth()->user()->roles->contains('role', 'Ouderraad') ||
                    auth()->user()->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'Loods');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('loodsen') }}">
                        <img alt="dolfijnen" src="{{ asset('img/icons/loodsen.png') }}">
                        <p>Loodsen</p>
                    </a>
                @endif

                @if(auth()->user() &&
                    (auth()->user()->roles->contains('role', 'After Loods') ||
                    auth()->user()->roles->contains('role', 'After Loodsen Leding') ||
                    auth()->user()->roles->contains('role', 'Administratie') ||
                    auth()->user()->roles->contains('role', 'Bestuur') ||
                    auth()->user()->roles->contains('role', 'Ouderraad') ||
                    auth()->user()->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'After Loods');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('afterloodsen') }}">
                        <img alt="afterloodsen" src="{{ asset('img/icons/after_loodsen.png') }}">
                        <p>After Loodsen</p>
                    </a>
                @endif


                {{--                <a class="btn btn-secondary quick-action" href="">--}}
                {{--                    <span class="material-symbols-rounded">archive</span>--}}
                {{--                    <p>Club archief</p>--}}
                {{--                </a>--}}
                {{--                <a class="btn btn-secondary quick-action" href="">--}}
                {{--                    <span class="material-symbols-rounded">news</span>--}}
                {{--                    <p>Nieuws</p>--}}
                {{--                </a>--}}
                {{--                <a class="btn btn-secondary quick-action" href="">--}}
                {{--                    <span class="material-symbols-rounded">event</span>--}}
                {{--                    <p>Evenementen</p>--}}
                {{--                </a>--}}
            </div>
        </div>
        <h1 class="mt-2"></h1>
    </div>
@endsection
