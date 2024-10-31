@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">Je MHG wachtwoord is gewijzigd.</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, het wachtwoord van je MHG account is door de administratie gewijzigd.</p>
        <br>
        <p>Wij zullen deze wijziging altijd in overleg hebben gedaan en anders hoor je zo snel mogelijk van ons!</p>
        <br>
        <p>Bij vragen of opmerkingen kun je altijd op dit mailtje antwoorden!</p>
        <a class="action-button" href="https://portal.waterscoutingmhg.nl/">Klik hier om naar het ledenportaal te gaan!</a>
    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat je wachtwoord voor het ledenportaal is gewijzigd.
            Als je deze notificaties niet meer wilt ontvangen, wijzig dan je instellingen op de instellingen pagina.
        </p>
    </td>
@endsection

