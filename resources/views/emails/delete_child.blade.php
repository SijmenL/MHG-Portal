@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">De ouder-kind koppeling is verbroken</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, {{$data['sender_full_name']}} heeft de ouderkoppeling verbroken.</p>
        <br>
        <p>De ouder-kind koppeling is verbroken. {{$data['sender_full_name']}} kan je persoonsgegevens niet meer inzien en je account niet meer aanpassen.</p>
        <br>
        <p>Mocht dit een fout zijn, maak dan de koppeling opnieuw via de <a href="https://portal.waterscoutingmhg.nl/instellingen/ouder-account">instellingen</a>.</p>
        <br>
    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat de ouder-kind koppeling verbroken is
        </p>
    </td>
@endsection

