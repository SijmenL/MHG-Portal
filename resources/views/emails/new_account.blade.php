@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">Welkom bij de MHG!</h1>
@endsection

@section('info')
    @php
        use Illuminate\Support\Str;

        $user = \App\Models\User::findOrFail($data['relevant_id']);
    @endphp

    <div>
        <p>Beste {{ $data['reciever_name'] }}, wat ontzettend leuk dat je je hebt ingeschreven
            als {{ $user->roles[0]->role }}!</p>
        <br>
        <p>We gaan je inschrijving doorsturen naar deze speltak en komen zo snel mogelijk bij je terug met meer informatie!</p>
        <br>
        <p>Voor nu hebben we al wel een account voor je aangemaakt op ons ledenportaal. Op dit portaal kun je voor nu je
            gegevens bekijken en wijzigen, maar als we je inschrijving hebben behandeld kun je hier onder andere posts
            en activiteiten van je speltak zien!</p>
        <a class="action-button"
           href="https://portal.waterscoutingmhg.nl/">Ga naar het ledenportaal</a>
        <br>
        <br>
        <p>Mocht je vragen hebben, dan kun je deze alvast stellen door dit mailtje te beantwoorden.</p>
        <br>
        <p>Alvast heel veel plezier namens ons toegewenst en welkom bij de groep!</p>
        <br>
        <p>Groetjes van Team Administratie!</p>


    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat je je hebt ingeschreven voor de Matthijs Heldt Groep.
            Als jij dit niet was kun je contact met ons opnemen door te reageren op deze mail.
        </p>
    </td>
@endsection

