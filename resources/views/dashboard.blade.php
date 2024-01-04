@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Welkom, {{ $user->name }}</h1>
        <p>{{ $date }}</p>

        <div class="bg-light rounded-2 p-3">
            <h2>Snelle Acties</h2>
            <div class="quick-action-bar">
{{--                Knoppen verschijnen en verdwijnen per rol.--}}
                <a class="btn btn-info quick-action" href="{{ route('account') }}">
                    <span class="material-symbols-rounded">person</span>
                    <p>Mijn account</p>
                </a>

{{--                Kinderen knop voor een ouderaccount. Verschijnt als een kind gekoppeld is.--}}
                <a class="btn btn-info quick-action" href="">
                    <span class="material-symbols-rounded">family_restroom</span>
                    <p>Mijn kinderen</p>
                </a>

{{--                Knop om de lessen te bekijken--}}
                <a class="btn btn-info quick-action" href="">
                    <span class="material-symbols-rounded">school</span>
                    <p>Lessen</p>
                </a>

{{--                Knop om de inisgnes te bekijken--}}
                <a class="btn btn-info quick-action" href="{{ route('insignes') }}">
                    <span class="material-symbols-rounded">award_star</span>
                    <p>Insignes</p>
                </a>

                {{--                Account beheer voor de administratie--}}
                <a class="btn btn-info quick-action" href="">
                    <span class="material-symbols-rounded">manage_accounts</span>
                    <p>Account beheer</p>
                </a>
                <a class="btn btn-info quick-action" href="">
                    <span class="material-symbols-rounded">code</span>
                    <p>Website beheer</p>
                </a>

{{--                Knoppen voor de speltakken, waar de gebruikers informatie kunnen vinden--}}
                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/dolfijnen.png') }}">
                    <p>Dolfijnen</p>
                </a>
                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/zeeverkenners.png') }}">
                    <p>Zeeverkenners</p>
                </a>
                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/loodsen.png') }}">
                    <p>Loodsen</p>
                </a>
                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/after_loodsen.png') }}">
                    <p>After Loodsen</p>
                </a>

{{--                Algemene pagina's--}}
                <a class="btn btn-secondary quick-action" href="">
                    <span class="material-symbols-rounded">archive</span>
                    <p>Club archief</p>
                </a>
                <a class="btn btn-secondary quick-action" href="">
                    <span class="material-symbols-rounded">news</span>
                    <p>Nieuws</p>
                </a>
                <a class="btn btn-secondary quick-action" href="">
                    <span class="material-symbols-rounded">event</span>
                    <p>Evenementen</p>
                </a>
            </div>
        </div>
        <h1 class="mt-2"></h1>
    </div>
@endsection
