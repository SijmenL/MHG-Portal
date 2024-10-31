@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">Je bent als ouder toegevoegd</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, {{$data['sender_full_name']}} heeft je toegevoegd als ouder.</p>
        <br>
        <p>Als ouder kun je de gegevens van je kind inzien en deze bewerken wanneer nodig. Met een ouder account kun je
            meekijken met alles wat je kind beleefd binnen de MHG en blijf je op de hoogte van wat er speelt binnen de
            speltak.</p>
        <br>
        <p>Je kunt zelf altijd de koppeling verwijderen via de <a href="https://portal.waterscoutingmhg.nl/instellingen/kind-account/verwijder">instellingen</a>.</p>
        <br>
        <p>Je kind kan deze koppeling verwijderen zodra die bij de loodsen komt.</p>
        <br>
        <a class="action-button" href="https://portal.waterscoutingmhg.nl/">Kil hier om naar portal te gaan!</a>
        <br>
        <br>
        <p>Neem vooral contact op als deze koppeling niet de bedoeling was en verwijder deze in de instellingen.</p>

    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat je als ouder account bent gekoppeld aan je kind.
        </p>
    </td>
@endsection

