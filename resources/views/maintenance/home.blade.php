@extends('layouts.app')

@vite('resources/js/home.js')

@section('content')

    <div class="header"
         style="background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.5)), url({{ asset('files/maintenance/banner.webp') }})">
        <div>
            <p class="header-title">Onderhoud</p>
        </div>
    </div>


    <div class="container col-md-11">
        <div class="d-flex flex-row-responsive mb-2 justify-content-between align-items-center">
            <div>
                <h1>Onderhoud</h1>
                <p>Kies een object waarvan je de onderhoudsstatus wilt registreren</p>
            </div>
        </div>

    </div>

@endsection
