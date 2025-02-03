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

        <div class="alert alert-primary">Als ergens bugfixes vermeld staan, zijn deze terug te vinden op <a
                target="_blank" href="https://github.com/SijmenL/MHG-Portal/issues">de bugtracker</a>. Voeg hier vooral
            zelf ook nieuwe bugs aan toe!
        </div>

        <h2>Update 3.1</h2>
        <p class="fst-italic">03/02/2025</p>
        <ul>
            <li class="added">Algemene bugfixes en verbeteringen.</li>
            <li class="added">Datum en tijd van de aan of afmelding in de agenda.</li>
        </ul>

        <h2>Update 3.0</h2>
        <p class="fst-italic">08/01/2025</p>
        <ul>
            <li class="added">De digitale lesomgeving is toegevoegd</li>
            <ul>
                <li class="added">Praktijkbegeleiders kunnen lesomgevingen aanmaken</li>
                <li class="added">Lesomgevingen hebben hun eigen prikbord</li>
                <li class="added">Lesomgevingen hebben hun eigen agenda</li>
                <li class="added">Lesomgevingen kunnen deelnemers en praktijkbegeleiders bevatten. Praktijkbegeleiders zijn de docenten van de lesomgeving en kunnen dingen aanpassen</li>
                <li class="added">Lesomgevingen hebben hun eigen bestanden die geüpload kunnen worden door de praktijkbegeleiders.</li>
                <li class="added">Wanneer je bent toegevoegd aan een les kun je deze vanaf het dashboard bekijken.</li>
            </ul>
            <li class="added">Een link naar het MHG archief op de OneDrive is toegevoegd aan het dashboard voor 18+ leden.</li>
            <li class="added">Algemene bugfixes.</li>
        </ul>

        <h2>Update 2.2</h2>
        <p class="fst-italic">04/12/2024</p>
        <ul>
            <li class="added">Als de mailtjes verstuurd worden heeft het systeem even nodig achter de schermen. Alle
                opslaan knoppen hebben nu een animatie om dit aan te geven en worden uitgezet om meerdere posts te
                voorkomen.
            </li>
        </ul>

        <h2>Update 2.1</h2>
        <p class="fst-italic">01/10/2024</p>
        <ul>
            <li class="added">Nieuw notificatie systeem</li>
            <ul>
                <li class="added">Notificaties worden per e-mail verstuurd</li>
            </ul>
            <li class="added">Verschillende formulieren veranderd naar GET request in plaats van POST voor
                gebruiksgemak
            </li>
            <li class="added">QOL fixes in de agenda, zoals snellere navigatie</li>
            <li class="added">Ouders kunnen de activiteiten van de kinderen zien en kinderen aanwezig melden</li>
            <li class="added">Wat is er nieuw menu, waarin snel duidelijk wordt wat er veranderd is</li>
        </ul>

        <h2>Update 2.0.1</h2>
        <p class="fst-italic">01/10/2024</p>
        <ul>
            <li class="added">Bugfixes</li>
        </ul>

        <h2>Update 2.0</h2>
        <p class="fst-italic">23/09/2024</p>
        <ul>
            <li class="added">De kalender is volledig over naar portal</li>
            <ul>
                <li class="added">Activiteiten zijn aan te maken per gebruiker en speltak</li>
                <li class="added">Openbare activiteiten zijn via portal te maken en op de openbare website te zetten
                </li>
                <li class="added">Maand weergave en planning weergave</li>
                <li class="added">Aanwezigheid kan worden bijgehouden per activiteit, leden kunnen op aan- of afwezig
                    drukken en de leiding krijgt een net overzicht.
                </li>
                <li class="added">Inschrijfformulieren kunnen worden aangemaakt en mensen van buitenaf kunnen zich
                    inschrijven
                </li>
            </ul>
            <li class="added">Verschillende QOL updates:</li>
            <ul>
                <li class="added">De tekst editor werkt soepeler en is fijner in gebruik</li>
                <li class="added">De pagina voor het aanpassen van persoonsgegevens is opnieuw vormgegeven</li>
                <li class="added">Verschillende bugs zijn eruit gehaald</li>
            </ul>
        </ul>

        <h2>Update 1.1</h2>
        <p class="fst-italic">10/07/2024</p>
        <ul>
            <li class="added">Het beheer van nieuwsitems gaat volledig via portal</li>
            <ul>
                <li class="added">Iedereen kan nieuws aanmaken</li>
                <li class="added">Nieuws wordt op de website gepubliceerd wanneer de administratie dit geaccepteerd
                    heeft
                </li>
                <ul>
                    <li class="added">Gebruikers krijgen een melding wanneer hun nieuws op de site staat</li>
                </ul>
                <li class="added">Nog niet gepubliceerd nieuws kan bewerkt worden</li>
                <li class="added">Al het nieuws wat al op de website stond is overgezet naar het nieuwe systeem</li>
            </ul>
        </ul>

        <h2>Update 1.0</h2>
        <p class="fst-italic">26/03/2024</p>
        <ul>
            <li class="added">Administratieve logs toegevoegd, voor het vinden van bugs en fouten.</li>
            <ul>
                <li class="added">De logs worden aangemaakt bij elke administratieve handeling en bij het bekijken van
                    avg gegevens. Deze logs kunnen gebruikt worden om fouten uit het systeem te halen en om te kijken
                    waar dingen mis kunnen gaan.
                </li>
            </ul>
            <li class="added">Notificaties toegevoegd, notificaties worden verstuurd als:</li>
            <ul>
                <li class="added">Iemand een post maakt.</li>
                <li class="added">Je post of reactie een like krijgt.</li>
                <li class="added">Je account gelinkt wordt aan een ouder of kind.</li>
                <li class="added">Je gegevens worden aangepast.</li>
            </ul>
            <li class="added">Notificaties worden automatisch na 1 week verwijderd.</li>
            <li class="added">Notificaties kunnen een snelle link bevatten om bij hetgeen te komen waar de notificatie
                over gaat.
            </li>
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
