@extends('layouts.loodsen')

@section('content')
    <div class="flunky-background">
        <div class="py-4 container col-md-11">
            <h1>Flunkyball</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Flunkyball</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <p>Flunkyball is het spel dat inmiddels niet meer weg te denken is van een avondje Loodsenbar. Bij een
                spelletje horen natuurlijk regels, maar ook de muziek is van enorm belang! Daarom zijn op deze pagina de
                enige echte FlunkyDJ en het handboek op te vragen!</p>
        </div>
        <div class="buttons">
            <a href="{{ route('loodsen.flunkyball.flunkydj') }}" class="clickable-button"
               style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.9)), url('{{ asset('files/loodsen/flunkyball/flunkyball-image.jpg')}}')">
                <p class="button-text-main">FlunkyDJ</p>
                <p class="button-text">De klassieke Flunkyball DJ muziek app!</p>
            </a>
            <a href="" class="clickable-button"
               style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.9)), url('{{ asset('files/loodsen/flunkyball/handboek-image.jpg')}}')">
                <p class="button-text-main">Handboek</p>
                <p class="button-text">Het officiële Flunkyball Handboek, met alle regels!</p>
            </a>
            @if(auth()->user() && (auth()->user()->roles->contains('role', 'Loodsen Stamoudste') || auth()->user()->roles->contains('role', 'Administratie')))
            <a href="{{ route('loodsen.flunkyball.music') }}" class="clickable-button"
               style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.9)), url('{{ asset('files/loodsen/flunkyball/admin-image.png')}}')">
                <p class="button-text-main">Muziek Beheer</p>
                <p class="button-text">Stamoudste: Beheer de FlunkyDJ muziek</p>
            </a>

                <a href="" class="clickable-button"
                   style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.9)), url('{{ asset('files/loodsen/flunkyball/rule-image.jpg')}}')">
                    <p class="button-text-main">Regel Beheer</p>
                    <p class="button-text">Stamoudste: Pas de regels in het handboek aan</p>
                </a>
            @endif
        </div>
    </div>
@endsection
