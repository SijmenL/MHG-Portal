@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">De ouder-kind koppeling is verbroken</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, {{$data['sender_full_name']}} heeft de ouderkoppeling verbroken.</p>
        <br>
        <p>Omdat je kind nu bij de loodsen zit kan de koppeling verbroken worden i.v.m. de AVG regelgeving.</p>
        <br>
        <p>Mocht dit een fout zijn, maak dan samen met je kind de ouderkoppeling opnieuw aan via de instellingen van het kind account of neem contact op.</p>
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

