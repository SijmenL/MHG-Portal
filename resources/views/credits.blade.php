@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Credits</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Credits</li>
            </ol>
        </nav>

        <div class="text-center">
            <p>De online omgeving en de website zijn met liefde en plezier ontwikkeld door</p>
            <h2>Team Administratie Waterscouting Matthijs Heldt Groep</h2>
            <p>bestaande uit</p>
            <h3>Sijmen Lokers</h3>
            <h3>Devyn de Vos</h3>
            <h3>Niels Cabaret</h3>
            <h3>Simon Klaren</h3>

            <br>
            <p>Onder toezicht en in opdracht van</p>
            <h2>Bestuur MHG</h2>
            <p>bestaande uit</p>
            <h3>Wim Nelemans</h3>
            <h3>Marijn de Vos</h3>
            <h3>Arjen van de Wetering</h3>
            <h3>Nijs van Noordennen</h3>

            <br>
            <h2>Overige credits</h2>
            <p>FlunkyDJ - idee & ontwikkeling door Sijmen Lokers</p>
            <p>Loodsenbar streepsysteem - idee & ontwikkeling door Simon Klaren</p>
            <p>Veel oude archieffoto's door Ruud van der Zee</p>

            <br>
            <p>Druk en/of spelfouten voorbehouden.</p>
            <p>© Matthijs Heldt Groep 2008 – {{ date('Y') }}</p>
        </div>

    </div>
@endsection
