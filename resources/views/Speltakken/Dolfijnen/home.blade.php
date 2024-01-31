@extends('layouts.dolfijnen')

@section('content')
    <div class="container col-md-11">
        <h1>De Dolfijnen</h1>
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <p>Welkom op de digitale omgeving van de Dolfijnen! In de toekomst zal hier meer mogelijk zijn, zoals dingen
            delen met je groep, je aan- of af melden voor groepdraaien & activiteiten en bijvoorbeeld de agenda
            bekijken. Hou de omgeving dus goed in de gaten!</p>
    </div>
@endsection
