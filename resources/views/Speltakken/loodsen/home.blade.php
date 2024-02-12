@extends('layouts.loodsen')

@section('content')
    <div class="header py-4" style="background-image: url({{ asset('files/loodsen/loodsen.webp') }});">
        <div>
            <p class="header-title">Loodsen</p>
            <p class="header-text">Welkom op de digitale omgeving van de Loodsen! </p>
        </div>
    </div>
    <div class="container col-md-11">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
            <div class="">
                <h1 class="">Opties</h1>
                <p>Voor nu zijn er een aantal opties in het portaal beschikbaar, je kunt op dit moment bijvoorbeeld de leiding
                    bekijken. In de toekomst zal hier meer mogelijk zijn, zoals dingen
                    delen met je groep, je aan- of af melden voor groepdraaien & activiteiten en bijvoorbeeld de agenda
                    bekijken. Hou de omgeving dus goed in de gaten!</p>
                <div class="bg-light rounded-2 p-3">
                    <h2>Acties</h2>
                    <div class="quick-action-bar">

                        @if(auth()->user() && (auth()->user()->roles->contains('role', 'Loodsen Stamoudste') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')|| auth()->user()->roles->contains('role', 'Ouderraad')))
                            <a class="btn btn-info quick-action" href="{{ route('loodsen.groep') }}">
                                <span class="material-symbols-rounded">groups_2</span>
                                <p>Groep</p>
                            </a>
                        @endif
                            <a class="btn btn-info quick-action" href="{{ route('loodsen.leiding') }}">
                                <span class="material-symbols-rounded">supervisor_account</span>
                                <p>Leiding</p>
                            </a>
                            <a class="btn btn-info quick-action" href="{{ route('loodsen.flunkyball') }}">
                                <img style="filter: unset" alt="flunkyball" src="{{ asset('files/loodsen/flunkyball/logo/logo-flunkyball.webp') }}">
                                <p>Flunkyball</p>
                            </a>
                            <a class="btn btn-info quick-action" href="{{ route('loodsen.leiding') }}">
                                <img style="filter: unset" alt="loodsenbar" src="{{ asset('files/loodsen/logo-bar.webp') }}">
                                <p>Loodsenbar</p>
                            </a>
                    </div>
                </div>
            </div>
            <div class="">
                <img class="w-100" alt="groepsfoto" src="{{ asset('files/loodsen/Overzwaaien-loodsen.jpg') }}">
            </div>
        </div>

    </div>
@endsection
