@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Changelog</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Changelog</li>
            </ol>
        </nav>

        <h2>Update 1.0</h2>
        <p class="fst-italic">26/03/2024</p>
        <ul>
            <li class="added">Administratieve logs toegevoegd, voor het vinden van bugs en fouten.</li>
            <ul>
                <li class="added">De logs worden aangemaakt bij elke administratieve handeling en bij het bekijken van avg gegevens. Deze logs kunnen gebruikt worden om fouten uit het systeem te halen en om te kijken waar dingen mis kunnen gaan.</li>
            </ul>
            <li class="added">Notificaties toegevoegd, notificaties worden verstuurd als:</li>
            <ul>
                <li class="added">Iemand een post maakt.</li>
                <li class="added">Je post of reactie een like krijgt.</li>
                <li class="added">Je account gelinkt wordt aan een ouder of kind.</li>
                <li class="added">Je gegevens worden aangepast.</li>
            </ul>
            <li class="added">Notificaties worden automatisch na 1 week verwijderd.</li>
            <li class="added">Notificaties kunnen een snelle link bevatten om bij hetgeen te komen waar de notificatie over gaat.</li>
        </ul>

        <h2>Update 0.6</h2>
        <p class="fst-italic">28/02/2024</p>
        <ul>
            <li class="added">Prikbord toegevoegd aan elke speltak</li>
            <ul>
                <li class="added">Gebruikers kunnen posts maken, bewerken en verwijderen</li>
                <li class="added">Gebruikers kunnen reacties onder de posts en andere reacties achter laten</li>
                <li class="added">Reacties kunnen worden bewerkt en verwijderd</li>
                <li class="added">Posts en reacties kunnen worden geliked door gebruikers</li>
                <li class="added">Administratieve achterkant voor het prikbord</li>
                <ul>
                    <li class="added">Het bestuur, team Admin, de ouderraad en de leiding kan posts verwijderen, niemand
                        kan deze bewerken behalve de gebruiker die de post gemaakt heeft
                    </li>
                </ul>
            </ul>
            <li class="added">Omgeving voor Leiding & Organisatie</li>
            <ul>
                <li class="added">Eigen prikbord</li>
                <li class="added">Directe link naar de notules van de afgelopen groepsraden</li>
            </ul>
        </ul>


        <h2>Update 0.5</h2>
        <p class="fst-italic">14/02/2024</p>
        <ul>
            <li class="added">Redesign van het dashboard</li>
            <ul>
                <li class="added">Uiterlijk van de knoppen aangepast</li>
                <li class="added">Banner toegevoegd</li>
            </ul>
            <li class="added">Rollen systeem toegevoegd</li>
            <ul>
                <li class="added">Gebruikers kunnen een rol toegewezen krijgen</li>
                <li class="added">Rollen bepalen welke pagina's de gebruiker toegang heeft</li>
            </ul>
            <li class="added">Ouder/Kind systeem toegevoegd</li>
            <ul>
                <li class="added">Gebruikers kunnen aan elkaar gekoppeld worden als ouder/kind</li>
                <li class="added">De ouder krijgt toegang tot alle pagina's waar het kind ook toegang tot heeft, m.u.v.
                    administratieve pagina's
                </li>
            </ul>
            <li class="added">Speltak pagina's toegevoegd</li>
            <ul>
                <li class="added">Gebruikers kunnen de leiding of organisatie van de speltak bekijken</li>
                <li class="added">Leiding kan gebruikers gegevens opvragen</li>
                <li class="added">De Loodsen hebben als test al speciale pagina's gekregen:</li>
                <ul>
                    <li class="added">FlunkyDJ</li>
                    <li class="added">Flunkyball handboek</li>
                    <li class="added">Stamoudsten kunnen muziek aan FlunkyDJ toevoegen, bewerken en verwijderen</li>
                </ul>
            </ul>
            <li class="added">Instellingen toegevoegd</li>
            <ul>
                <li class="added">Pas je eigen persoonsgegevens en je wachtwoord aan</li>
                <li class="added">Maak een koppeling met een ouder account door er een aan te maken of een emailadres in
                    te vullen
                </li>
                <li class="added">Kinderen die geen Dolfijn of Zeeverkenner zijn kunnen de ouderkoppeling weer
                    verwijderen
                </li>
                <li class="added">Ouders kunnen de koppeling altijd verwijderen.</li>
            </ul>
            <li class="added">Administratie achterkant toegevoegd</li>
            <ul>
                <li class="added">Aanmaken, bewerken en verwijderen van accounts</li>
                <li class="added">Toewijzen van rollen</li>
            </ul>
            <li class="removed">Het registreren is verplaatst naar de administrative kant</li>
            <li class="removed">Wachtwoord vergeten is verwijderd, stuur een mailtje naar Team Admin in dit geval.</li>
        </ul>

        <h2>Update 0.4 beta</h2>
        <p class="fst-italic">29/12/2023</p>
        <ul>
            <li class="added">Portal verplaatst naar eigen subdomein (andere website)</li>
            <li class="added">Dashboard toegevoegd</li>
            <li class="added">Profielfoto's toegevoegd</li>
        </ul>

        <h2>Update 0.3</h2>
        <p class="fst-italic">05/10/2023</p>
        <ul>
            <li class="added">Inloggen</li>
            <li class="added">Registreren</li>
        </ul>

    </div>
@endsection
