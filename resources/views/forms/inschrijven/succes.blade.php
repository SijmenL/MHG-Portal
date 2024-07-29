@extends('layouts.contact')

@section('content')
    <div class="d-flex justify-content-center align-items-center"
         style="height: 100vh; width: 100vw; background: white">
        <div class="contact d-flex gap-4 shadow m-4">
            <div class="contact-image" style="background-image: url({{ asset('img/general/inschrijven.jpg') }})">
            </div>
            <div class="d-flex flex-column p-3 contact-text justify-content-center">
                <h1>Schrijf je in!</h1>

                <div class="p-3 border-2 border-info-subtle" style="border: solid; border-radius: 15px;">
                    <h2>Bedankt voor het inschrijven!</h2>
                    <p>We hebben op ons <a target="_blank" href="https://portal.waterscoutingmhg.nl/login">ledenportaal</a> alvast een
                        account voor je aangemaakt. Je kan hier inloggen met je <b>e-mailadres</b> en je
                        <b>wachtwoord</b>. Je kan hier voor nu je persoonsgegevens aanpassen. Zodra
                        je inschrijving verwerkt is, kun je bijvoorbeeld ook posts en informatie van de speltak zien of een ouderaccount aanmaken!</p>
                    <p>Je krijgt van ons een e-mail als we je inschrijving hebben verwerkt!</p>
                </div>
            </div>
        </div>
@endsection
