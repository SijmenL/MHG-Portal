@extends('emails.layouts.something_new_layout')

@section('title')
    <h1 class="centered">Er is een nieuwe inschrijving!🎉</h1>
@endsection

@section('greeting')
    <p class="centered">Beste {{ $reciever_name }}, via portal is een nieuwe inschrijving binnen gekomen! Bekijk hieronder de details:</p>
@endsection


@section('info')
    <div class="two-columns">
        <div class="column">
            <p>Name: {{ $data->name }}</p>
            <p>Email: {{ $data->email }}</p>
            <p>Sex: {{ $data->sex }}</p>
            <p>Last Name: {{ $data->last_name }}</p>
            <p> Birth Date: {{ $data->birth_date }}</p>
            <p> Street: {{ $data->street }}</p>
            <p>Postal Code: {{ $data->postal_code }}</p>
            <p>City: {{ $data->city }}</p>
            <p> Phone: {{ $data->phone }}</p>
        </div>
        <div class="column">
            <p>Infix: {{ $data->infix }}</p>
            <p>Avg: {{ $data->avg ? 'Yes' : 'No' }}</p>
            <p>Member Date: {{ $data->member_date }}</p>
            <p>Dolfijnen Name: {{ $data->dolfijnen_name }}</p>
            <p>Children: {{ $data->children }}</p>
            <p>Parents: {{ $data->parents }}</p>
        </div>
    </div>
@endsection

@section('actions')
    <a href="{{ $btnLink }}" class="action-button">Ga naar inschrijving</a>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">Deze email is automatisch gegenereerd op basis van een nieuwe inschrijving die is toegevoegd via Portal. Als je deze notificaties niet meer wilt ontvangen, wijzig dan je instellingen op de instellingen pagina.</p>
    </td>
@endsection