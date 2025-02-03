@extends('layouts.app')

@section('content')
    <div class="header" style="background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.5)), url({{ asset('img/general/MHG_vloot.jpg') }})">
        <div>
            <p class="header-title">Hier gaat iets mis...</p>
        </div>
    </div>

    <div class="container col-md-11">
        <h1>Oeps, er lijkt iets compleet mis te zijn gegaan!</h1>
        <p>Probeer het later nog een keer of <a href="mailto:administratie@waterscoutingmhg.nl">neem contact met team Admin</a> op!</p>


        <a class="btn btn-primary text-white" href="{{ route('dashboard') }}">Ga Terug Naar De Homepagina</a>



    </div>
@endsection
