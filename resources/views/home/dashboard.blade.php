@extends('layouts.app')

@vite('resources/js/home.js')

@section('content')
    {{-- Blade file, e.g., home/dashboard.blade.php --}}
    <div id="newPopUp" class="popup" style="margin-top: -122px; display: flex">
        <div class="popup-body overflow-hidden">
            <h2>Wat is er nieuw in versie 2.1?</h2>

            <!-- Nav tabs -->
            <div class="tab-container" style="overflow-x: auto; white-space: nowrap;">
                <ul class="nav nav-tabs flex-nowrap" style="max-width: calc(100vw - 40px)" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="tab1-tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Notificaties</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab2-tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Agenda</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab3-tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Overig</a>
                    </li>
                    <!-- Add more tabs as needed -->
                </ul>
            </div>

            <!-- Tab content -->
            <div class="tab-content w-100 mb-4">
                <div class="mt-2 p-2 text-start tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    <h4>Notificaties zijn uitgebreid!</h4>
                    <p>Er is een nieuw notificatie systeem die je ook e-mailtjes stuurt! Je kunt nu ook in de instellingen je voorkeuren meegeven welke notificaties je waar wilt ontvangen.</p>
                    <p>Standaard krijg je alle notificaties ook per mail binnen, zorg er dus voor dat je mailadres klopt!</p>
                </div>
                <div class="mt-2 p-2 text-start tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    <h4>Kleine aanpassingen in de agenda</h4>
                    <ul>
                        <li>Betere navigatie, als je op de verkeerde pagina zit zal je een knop kunnen vinden naar de juiste pagina</li>
                        <li>Activiteiten worden pas uit het overzicht gehaald als ze voorbij zijn, niet wanneer ze beginnen</li>
                        <li>De maand weergave is verbeterd</li>
                    </ul>
                </div>
                <div class="mt-2 p-2 text-start tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    <h4>Bugs & QOL</h4>
                    <p>De code is hier en daar opgepoetst en portal zal dus beter werken! Kijk voor de volledige bugtracker op <a href="https://github.com/SijmenL/MHG-Portal/issues">de bugtracker</a>.</p>
                    <p>Wanneer je een foutje tegenkomt zijn we nog steeds altijd bereikbaar!</p>
                </div>
            </div>

            <button id="close-new-popup" class="btn btn-outline-danger" style="float: right;">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
    </div>


    <div id="popUp" class="popup" style="margin-top: -122px; display: none">
        <div class="popup-body">
            <div class="page">
                <h2>Welkom op de ledenomgeving van de <strong>MHG</strong>!</h2>
                <p>Op dit portaal kun je verschillende dingen doen. We geven je graag een rondleiding!</p>
                <p>Klik op de pijltjes <span class="material-symbols-rounded" style="transform: translateY(7px)">arrow_back</span>
                    <span class="material-symbols-rounded" style="transform: translateY(7px)">arrow_forward</span> om
                    door de rondleing heen te scrollen en druk op het kruisje <span
                        class="material-symbols-rounded" style="transform: translateY(7px)">close</span> om de
                    rondleiding te stoppen.</p>
            </div>
            <div class="page" style="display: none">
                <h2>Hoofdmenu</h2>
                <p>Het portaal opent altijd op de beginpagina, oftewel het dashboard. Deze ziet er voor iedereen anders
                    uit, op basis van wat je binnen de club kan en mag. Er kunnen altijd knoppen bijkomen in toekomstige
                    updates.</p>
                <img class="w-100" src="{{ asset('img/tutorial/dashboard.png') }}">
            </div>
            <div class="page" style="display: none">
                <h2>Speltak</h2>
                <div class="d-flex flex-row-responsive align-items-center justify-content-center gap-4">
                    <div style="text-align: start">
                        <p>Elke speltak heeft op het Ledenportaal zijn eigen pagina.</p>
                        <p>Binnen deze pagina zijn verschillende dingen te doen. Zo heeft elke speltak zijn eigen
                            prikbord, waarop verschillende meldingen van de leiding zullen verschijnen en waarop je zelf
                            ook leuke dingen kunt plaatsten!</p>
                        <p>Verder kun je ook de informatie van de leiding oproepen, zoals telefoonnummers en
                            verenigingsgebonden e-mailadressen.</p>
                    </div>
                    <img class="w-50" src="{{ asset('img/tutorial/speltakpagina.png') }}">
                </div>
            </div>
            <div class="page" style="display: none">
                <h2>Speltak</h2>
                <div class="d-flex flex-row-responsive align-items-center justify-content-center gap-4">
                    <div style="text-align: start">
                        <p>Als je een post op het prikbord maakt kan deze geliket worden, en er kunnen zelfs mensen op
                            reageren.</p>
                        <p>Posts die je maakt kan je altijd nog bewerken of verwijderen als dat zou moeten. Er kan
                            niemand anders je post aanpassen.</p>
                        <p>Posts worden in de gaten gehouden door team Admin en de leiding van de speltak.</p>
                    </div>
                    <img class="w-50" src="{{ asset('img/tutorial/post.png') }}">
                </div>
            </div>
            <div class="page" style="display: none">
                <h2>Instellingen</h2>
                <div class="d-flex flex-row-responsive align-items-center justify-content-center gap-4">

                    <div style="text-align: start">
                        <p>We gaan nu naar <a href="{{ route('settings') }}">de instellingen</a>.</p>
                        <p>Je kunt hier verschillende dingen regelen, zoals je persoonlijke gegevens en je
                            wachtwoord.</p>
                        <p>Ook kun je hier een ouderaccount koppelen. Een ouder account is een account die kan meekijken
                            met wat je in portal doet. Zo kan het meekijken met komende activiteiten, posts die je maakt
                            en is de informatie van de leiding erg makkelijk op te vragen.</p>
                        <p>Je kan een bestaand account laten koppelen als ouderaccount, als je ouder of verzorger
                            bijvoorbeeld Afterloods is geweest of in de ouderraad zit. Je kan ook een volledig nieuw
                            account aanmaken.</p>
                    </div>
                    <img class="w-50" src="{{ asset('img/tutorial/settings.png') }}">
                </div>
            </div>
            <div class="page" style="display: none">
                <h2>Instellingen</h2>
                <div class="d-flex flex-row-responsive align-items-center justify-content-center gap-4">

                    <div style="text-align: start">
                        <p>Een ouder koppeling kan verwijderd worden door het kind als deze bij de Loodsen of
                            Afterloodsen zit.</p>
                        <p>Een kindkoppeling kan altijd door de ouder verwijderd worden.</p>
                    </div>
                    <img class="w-75" src="{{ asset('img/tutorial/delete-parent.png') }}">
                </div>
            </div>
            <div class="page" style="display: none">
                <h2>Notificaties</h2>
                <div class="d-flex flex-row-responsive align-items-center justify-content-center gap-4">

                    <div style="text-align: start">
                        <p>Op het dashboard staat ook een knop notificaties.</p>
                        <p>Op de pagina <a href="{{ route('notifications') }}">Notificaties</a> krijg je alle
                            notificaties te zien die binnen het ledenportaal naar
                            jou
                            toegestuurd zijn. Dit is bijvoorbeeld als iemand een post maakt binnen jouw speltak of je
                            accountgegevens zijn bijvoorbeeld aangepast door je ouder of de administratie.</p>
                        <p>Notificaties met een lichte kleur zijn nog ongelezen.</p>
                        <p>Alle notificaties die je hier ontvangt, ontvang je ook per mail.</p>
                    </div>
                    <img class="w-75" src="{{ asset('img/tutorial/notificaties.png') }}">
                </div>
            </div>
            @if($user->children()->count() > 0)
                <div class="page" style="display: none">
                    <h2>Kinderen</h2>
                    <div class="d-flex flex-row-responsive align-items-center justify-content-center gap-4">
                        <div style="text-align: start">
                            <p>Omdat je een kindaccount gelinkt hebt, heb je toegang tot de <a
                                    href="{{ route('children') }}">Mijn Kinderen</a> pagina.</p>
                            <p>Op deze pagina kan je de persoonsgegevens van je kinderen aanpassen.</p>
                        </div>
                        <img class="w-50" src="{{ asset('img/tutorial/kinderen.png') }}">
                    </div>
                </div>
            @endif

            <div class="page" style="display: none">
                <h2>Leiding & Organisatie</h2>
                @if($user &&
                   ($user->roles->contains('role', 'Dolfijnen Leiding') ||
                   $user->roles->contains('role', 'Zeeverkenners Leiding') ||
                   $user->roles->contains('role', 'Loodsen Stamoudste') ||
                   $user->roles->contains('role', 'Afterloodsen Organisator') ||
                   $user->roles->contains('role', 'Vrijwilliger') ||
                   $user->roles->contains('role', 'Administratie') ||
                   $user->roles->contains('role', 'Bestuur') ||
                   $user->roles->contains('role', 'Praktijkbegeleider') ||
                   $user->roles->contains('role', 'Loodsen Mentor') ||
                   $user->roles->contains('role', 'Ouderraad'))
                   )
                    <p>De pagina <a href="{{ route('leiding') }}">Leiding & Organisatie</a> bevat net als de
                        speltakpagina's een prikbord waar informatie met elkaar gedeeld kan worden.</p>
                    <p>Op deze pagina valt ook een archief te vinden van de notules van de afgelopen groepsraden.</p>

                    <p>Ook is er een grote lijst te zien waar iedereen binnen de organisatie te vinden is.</p>
                    <img class="w-100" src="{{ asset('img/tutorial/team admin.png') }}">
                @else
                    <p>Op de pagina <a href="{{ route('leiding.leiding') }}">Leiding & Organisatie</a> is een overzicht
                        te vinden met alle belangrijke contactpersonen binnen de club. Je kan hier de telefoonnummers en
                        clubgebonden e-mailadressen opvragen.</p>
                    <img class="w-100" src="{{ asset('img/tutorial/team admin.png') }}">
                @endif
            </div>
            <div class="page" style="display: none">
                <h2>Nieuws</h2>
                <div class="d-flex flex-row-responsive align-items-center justify-content-center gap-4">
                    <div style="text-align: start">
                        <p>Op de pagina <a href="{{ route('news') }}">Nieuws</a> kun je nieuwtjes insturen die op de
                            normale website komen te staan.</p>
                        <p>Als je een nieuwtje instuurt wordt deze eerst nagekeken voordat deze online komt te
                            staan.</p>
                    </div>
                    <img class="w-50" src="{{ asset('img/tutorial/nieuws.png') }}">
                </div>
            </div>
            <div class="page" style="display: none">
                <h2>Als laatste..</h2>
                <p>In de toekomst zullen we nog meer dingen toe gaan voegen aan ons Ledenportaal, dus als je op het
                    dashboard een nieuwe knop ziet, kijk dan zeker de rondleiding nog even na door op het vraagteken te
                    drukken.</p>
                <p>Mocht je nog vragen hebben staan we hier altijd voor open! We zijn te mailen via <a
                        href="mailto:administratie@waterscoutingmhg.nl">administratie@waterscoutingmhg.nl</a>!</p>
                <p>Groetjes en veel plezier!</p>
                <p>Team Administratie</p>
            </div>

            <div class="button-container">
                <div>
                    <a id="previous-page" class="btn btn-dark"><span class="material-symbols-rounded">arrow_back</span></a>
                    <a id="next-page" class="btn btn-dark"><span
                            class="material-symbols-rounded">arrow_forward</span></a>
                </div>
                <a id="close-popup" class="btn btn-outline-danger"><span
                        class="material-symbols-rounded">close</span></a>
            </div>
        </div>
    </div>

    <div class="header" style="background-image: url({{ asset('img/general/MHG_vloot.jpg') }})">
        <div>
            <p class="header-title">Ledenportaal</p>
            <p class="header-text">Welkom op de digitale omgeving van de Matthijs Heldt Groep! </p>
        </div>
    </div>
    <div class="container col-md-11">

        <div class="d-flex flex-row justify-content-between align-items-center">
            <div style="max-width: 75vw">
                <h1>Welkom, {{ $user->name }}</h1>
                <p>{{ $date }}</p>
            </div>
            <div class="d-flex flex-row gap-2">
                <a id="new-button" class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                   style="border: none">
                    <span class="material-symbols-rounded" style="font-size: xx-large">autorenew</span>
                </a>
                <a id="help-button" class="btn btn-outline-dark d-flex align-items-center justify-content-center"
                   style="border: none">
                    <span class="material-symbols-rounded" style="font-size: xx-large">help</span>
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-light rounded-2 p-3">
            <h2>Acties</h2>
            <div class="quick-action-bar">

                @if($user->roles->contains('role', 'Administratie') ||
                    $user->roles->contains('role', 'Secretaris'))
                    <a class="btn btn-admin quick-action" href="{{ route('admin') }}">
                        <div style="margin-bottom: -10px; position: relative">
                            <span class="material-symbols-rounded">admin_panel_settings</span>
                            @if($admin >= 1)
                                <pre style="position: absolute"
                                     class="badge badge-pill bg-danger dashboard-notification">{{ $admin }}</pre>
                            @endif
                        </div>
                        <p>Administratie</p>
                    </a>
                @endif

                <a class="btn btn-info quick-action" href="{{ route('settings') }}">
                    <span class="material-symbols-rounded">settings</span>
                    <p>Instellingen</p>
                </a>

                <a class="btn btn-info quick-action" href="{{ route('notifications') }}">
                    <div style="margin-bottom: -10px; position: relative">
                        <span class="material-symbols-rounded">notifications</span>
                        @if($notifications >= 1)
                            <pre style="position: absolute"
                                 class="badge badge-pill bg-danger dashboard-notification">{{ $notifications }}</pre>
                        @endif
                    </div>
                    <p>Notificaties</p>
                </a>

                @if($user->children()->count() > 0)
                    <a class="btn btn-info quick-action" href="{{ route('children') }}">
                        <span class="material-symbols-rounded">family_restroom</span>
                        <p>Mijn kinderen</p>
                    </a>
                @endif


                @if($user &&
                    ($user->roles->contains('role', 'Dolfijnen Leiding') ||
                    $user->roles->contains('role', 'Zeeverkenners Leiding') ||
                    $user->roles->contains('role', 'Loodsen Stamoudste') ||
                    $user->roles->contains('role', 'Afterloodsen Organisator') ||
                    $user->roles->contains('role', 'Vrijwilliger') ||
                    $user->roles->contains('role', 'Administratie') ||
                    $user->roles->contains('role', 'Bestuur') ||
                    $user->roles->contains('role', 'Praktijkbegeleider') ||
                    $user->roles->contains('role', 'Loodsen Mentor') ||
                    $user->roles->contains('role', 'Ouderraad'))
                    )
                    <a class="btn btn-dark quick-action" href="{{ route('leiding') }}">
                        <span class="material-symbols-rounded">supervisor_account</span>
                        <p>Leiding & Organisatie</p>
                    </a>
                @else
                    <a class="btn btn-info quick-action" href="{{ route('leiding.leiding') }}">
                        <span class="material-symbols-rounded">supervisor_account</span>
                        <p>Leiding & Organisatie</p>
                    </a>
                @endif


                @if($user && $user->accepted === 1 &&
                    ($user->roles->contains('role', 'Dolfijn') ||
                    $user->roles->contains('role', 'Dolfijnen Leiding') ||
                    $user->roles->contains('role', 'Administratie') ||
                    $user->roles->contains('role', 'Bestuur') ||
                    $user->roles->contains('role', 'Ouderraad') ||
                    $user->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'Dolfijn');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('dolfijnen') }}">
                        <img alt="dolfijnen" src="{{ asset('img/icons/dolfijnen.png') }}">
                        <p>Dolfijnen</p>
                    </a>
                @endif
                @if($user && $user->accepted === 1 &&
                    ($user->roles->contains('role', 'Zeeverkenner') ||
                    $user->roles->contains('role', 'Zeeverkenners Leiding') ||
                    $user->roles->contains('role', 'Administratie') ||
                    $user->roles->contains('role', 'Bestuur') ||
                    $user->roles->contains('role', 'Ouderraad') ||
                    $user->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'Zeeverkenner');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('zeeverkenners') }}">
                        <img alt="dolfijnen" src="{{ asset('img/icons/zeeverkenners.png') }}">
                        <p>Zeeverkenners</p>
                    </a>
                @endif
                @if($user && $user->accepted === 1 &&
                    ($user->roles->contains('role', 'Loods') ||
                    $user->roles->contains('role', 'Loodsen Stamoudste') ||
                    $user->roles->contains('role', 'Administratie') ||
                    $user->roles->contains('role', 'Bestuur') ||
                    $user->roles->contains('role', 'Ouderraad') ||
                    $user->roles->contains('role', 'Loodsen Mentor') ||
                    $user->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'Loods');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('loodsen') }}">
                        <img alt="dolfijnen" src="{{ asset('img/icons/loodsen.png') }}">
                        <p>Loodsen</p>
                    </a>
                @endif

                @if($user && $user->accepted === 1 &&
                    ($user->roles->contains('role', 'Afterloods') ||
                    $user->roles->contains('role', 'Afterloodsen Organisator') ||
                    $user->roles->contains('role', 'Administratie') ||
                    $user->roles->contains('role', 'Bestuur') ||
                    $user->roles->contains('role', 'Ouderraad') ||
                    $user->children()->whereHas('roles', function ($query) {
                        $query->where('role', 'After Loods');
                    })->exists())
                )
                    <a class="btn btn-dark quick-action" href="{{ route('afterloodsen') }}">
                        <img alt="afterloodsen" src="{{ asset('img/icons/after_loodsen.png') }}">
                        <p>After Loodsen</p>
                    </a>
                @endif


                {{--                                                <a class="btn btn-secondary quick-action" href="">--}}
                {{--                                                    <span class="material-symbols-rounded">archive</span>--}}
                {{--                                                    <p>Club archief</p>--}}
                {{--                                                </a>--}}
                <a class="btn btn-secondary quick-action" href="{{ route('news') }}">
                    <span class="material-symbols-rounded">news</span>
                    <p>Nieuws</p>
                </a>
                @if($user &&
                    ($user->roles->contains('role', 'Dolfijnen Leiding') ||
                    $user->roles->contains('role', 'Zeeverkenners Leiding') ||
                    $user->roles->contains('role', 'Loodsen Stamoudste') ||
                    $user->roles->contains('role', 'Loods') ||
                    $user->roles->contains('role', 'Afterloodsen Organisator') ||
                    $user->roles->contains('role', 'Administratie') ||
                    $user->roles->contains('role', 'Bestuur') ||
                    $user->roles->contains('role', 'Praktijkbegeleider') ||
                    $user->roles->contains('role', 'Loodsen Mentor') ||
                    $user->roles->contains('role', 'Ouderraad'))
                    )
                    <a class="btn btn-secondary quick-action" href="{{ route('agenda') }}">
                        <span class="material-symbols-rounded">event</span>
                        <p>Agenda</p>
                    </a>
                @else
                    <a class="btn btn-secondary quick-action" href="{{ route('agenda.month') }}">
                        <span class="material-symbols-rounded">event</span>
                        <p>Agenda</p>
                    </a>
                @endif
            </div>
        </div>
        <h1 class="mt-2"></h1>
    </div>
@endsection
