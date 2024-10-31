@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">Er is een nieuwe inschrijving!</h1>
@endsection

@section('info')
    <div>
        @php
            use Illuminate\Support\Str;

            $user = \App\Models\User::findOrFail($data['relevant_id']);
        @endphp

        <p>Beste {{ $data['reciever_name'] }}, {{ $user->name.' '.$user->infix.' '.$user->last_name }} heeft zich
            ingeschreven als {{ $user->roles[0]->role }}.</p>
        <br>

        <table>
            <tr>
                <th class="table-info">Geslacht</th>
                <td>{{ $user->sex }}</td>
            </tr>
            <tr>
                <th class="table-info">Naam</th>
                <td>{{ $user->name.' '.$user->infix.' '.$user->last_name }}</td>
            </tr>
            <tr>
                <th class="table-info">Geboortedatum</th>
                <td>{{ $user->birth_date }}</td>
            </tr>
            <tr>
                <th class="table-info">Straat & huisnummer</th>
                <td>{{ $user->street }}</td>
            </tr>
            <tr>
                <th class="table-info">Postcode</th>
                <td>{{ $user->postal_code }}</td>
            </tr>
            <tr>
                <th class="table-info">Woonplaats</th>
                <td>{{ $user->city }}</td>
            </tr>
            <tr>
                <th class="table-info">E-mail</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th class="table-info">Telefoonnummer</th>
                <td>{{ $user->phone }}</td>
            </tr>
            <tr>
                <th class="table-info">AVG</th>
                <td>{{ $user->avg ? 'Ja' : 'Nee' }}</td>
            </tr>
        </table>

        <br>
        <p>Deze inschrijving moet nog goedgekeurd worden!</p>
        <br>

        <a class="action-button"
           href="https://portal.waterscoutingmhg.nl/{{ $data['link'] }}">Klik hier om deze inschrijving te
            bekijken!</a>
    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat iemand zich heeft ingeschreven.
            Als je deze notificaties niet meer wilt ontvangen, wijzig dan je instellingen op de instellingen pagina.
        </p>
    </td>
@endsection

