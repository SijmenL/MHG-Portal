@extends('layouts.loodsen')

@section('content')
    <div class="flunky-background">
        <div class="py-4 container col-md-11">
            <h1>Handboek</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.flunkyball') }}">Flunkyball</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Handboek</li>
                </ol>
            </nav>

            @if(Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if(Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <p>Flunkyball is een drankspel waarin twee teams het tegen elkaar opnemen. Wij kennen op dit moment drie
                vormen: <strong>Original</strong>, <strong>Extreme</strong> en <strong>1v1</strong>.</p>
            <p>Op deze pagina zijn alle officiële regels te lezen, dus pak ze er vooral bij als er weer iemand woest op
                de scheids af komt lopen!</p>

                <div class="settings-container">
                    <a class="setting" data-bs-toggle="collapse" href="#original" role="button"
                       aria-expanded="false"
                       aria-controls="original">

                        <div class="setting-text">
                            <div>
                                <h3>Flunkyball Original</h3>
                                <small>De spelregels van het orginele flunkyball spel.</small>
                            </div>
                            <span class="material-symbols-rounded">expand_more</span>
                        </div>

                        <div class="collapse multi-collapse" id="original">
                            <div class="bg-light p-3 rounded">
                                <h4>Officieel Regelement Flunkyball</h4>
                                <p class="fst-italic">Een combinatie van Internationale Regels en die van ons zelf.</p>

                                <p>Benodigdheden:</p>
                                <ul>
                                    <li>
                                        Per persoon 1 flesje bier
                                    </li>
                                    <li>
                                        1 petfles van 1,5 liter, met een bodem van water
                                    </li>
                                    <li>
                                        1 bal
                                    </li>
                                </ul>

                                <p>Twee teams staan tegenover elkaar, elk in een rij, ongeveer 10 meter uit elkaar. Het
                                    aantal spelers in een team ligt niet vast; teams mogen zelf ingedeeld worden en
                                    hoeven na overleg met de scheids niet van gelijke grootte te zijn.</p>
                                <p>Elke speler heeft een biertje voor zich staan en in het midden van het veld staat de
                                    fles. De teams mogen om de beurt op de fles gooien. Als de fles omvalt moet het team
                                    dat gegooid heeft zo snel mogelijk hun bier op drinken. Als de fles omligt moet het
                                    andere team de fles weer rechtop in het midden zetten en de bal meenemen. Zodra de
                                    bal en de spelers weer aan hun kant staan wordt er stop geroepen door de
                                    scheidsrechter en moet het andere
                                    team stoppen met drinken. Het team dat als eerste al hun bier op heeft, wint. Een
                                    biertje is pas op als er na 3 seconden rechtop boven het hoofd houden geen druppels
                                    meer uitkomen. </p>

                                <p>Het spel kan door een scheidsrechter of door een jury gefloten worden.</p>

                                <h4><strong>Straffen</strong></h4>
                                <strong>Mogelijke straffen:</strong>
                                <ul>
                                    <li>
                                        Waarschuwing
                                    </li>
                                    <li>
                                        Nieuw bier
                                    </li>
                                    <li>
                                        Biertje aanvullen
                                    </li>
                                </ul>

                                <p>Wanneer er gesproken wordt over een straf mag de scheidsrechter bepalen wat deze
                                    straf is. Er hangen geen vaste regels aan wanneer welke straf gegeven moet worden,
                                    maar hieronder staan de richtlijnen.</p>
                                <p>Hoe een waarschuwing werkt mag bepaald worden door de scheids, bijvoorbeeld maximaal
                                    1 per speler of bijvoorbeeld 1 per team.</p>


                                <strong>Waarschuwing:</strong>
                                <ul>
                                    <li>
                                        De lijn te vroeg oversteken
                                    </li>
                                    <li>
                                        Te vroeg drinken of te laat stoppen
                                    </li>
                                    <li>
                                        De bal niet juist werpen
                                    </li>
                                    <li>
                                        De scheids of spelers beledigen
                                    </li>
                                </ul>
                                <strong>Nieuw bier:</strong>
                                <ul>
                                    <li>
                                        Het omvallen van een bierflesje, om wat voor reden dan ook
                                    </li>
                                    <li>
                                        Er komt nog bier uit het bierflesje wanneer een speler denkt dat deze op is
                                    </li>
                                </ul>


                                <h4>Artikel 1: Flunkyball is geen binnensport</h4>
                                <p>Flunkyball moet ten allen tijde buiten worden gespeeld, voordat er weer een ruit
                                    sneuveld.</p>

                                <h4>Artikel 2: Begin spel</h4>
                                <p>Het spel wordt gestart wanneer de scheids de bal aan een team geeft. De scheids mag
                                    zelf bepalen aan welk team de bal gegeven wordt, maar kan dit eerlijker doen door
                                    bijvoorbeeld een getal onder de 10 te vragen of door bijvoorbeeld twee teamleden
                                    steen papier schaar te laten spelen.</p>

                                <h4>Artikel 3: Muziek</h4>
                                <p>Het spel mag allen gespeeld worden als Funky Town van Lips Inc opstaat. Als er een
                                    ander nummer te horen valt en er wordt toch verder gespeeld, krijgen de teamleden
                                    van het team dat verder speelt allemaal nieuw bier.</p>
                                <p>De scheidsrechter zorgt ervoor dat er andere muziek klinkt wanneer het spel niet
                                    gespeeld mag worden.</p>

                                <h4>Artikel 4: De scheids heeft <strong>altijd</strong> gelijk</h4>
                                <p>De scheidsrechter is ten alle tijden bepalend, en een beslissing moet gehoorzaamd
                                    worden. Als dit niet het geval is, kan de speler een passende straf krijgen.</p>

                                <h4>Artikel 5: Bier ernaast</h4>
                                <p>Als een biertje schuimt of overstroomd kan de scheids daar straf voor geven.</p>

                                <h4>Artikel 6: Bier dat omvalt</h4>
                                <p>Bier wat omvalt moet zo snel mogelijk weer rechtop gezet worden. De speler van wie
                                    het bier is ontvangt een nieuwe.</p>

                                <h4>Artikel 7: Druppelen</h4>
                                <p>Als een speler denkt het flesje leeg te hebben moet deze naar de scheids komen en het
                                    flesje minstens 3 seconden boven hun hoofd houden. Wanneer er geen bier uitkomt is
                                    de speler klaar, komt er wel bier uit, dan krijgt de speler straf.</p>

                                <h4>Artikel 8: Optillen</h4>
                                <p>Bier mag nooit opgetild worden, tenzij het team mag drinken. Gebeurt dit wel dan
                                    hangt er een straf aan.</p>

                                <h4>Artikel 9: Helpen</h4>
                                <p>Spelers mogen elkaar niet helpen, tenzij afgesproken met de scheids. Gebeurt dit
                                    zonder overleg dan hangt er een straf aan.</p>

                                <h4>Artikel 10: Lopen</h4>
                                <p>Spelers mogen pas gaan lopen als bal de hand van het andere team verlaat. Wordt er
                                    eerder gelopen dan hangt er een straf aan.</p>

                                <h4>Artikel 11: 1 speler</h4>
                                <p>Wanneer er nog een speler over is in een team mag een teamgenoot helpen met
                                    rennen.</p>

                                <h4>Artikel 12: Gooien</h4>
                                <p>De bal moet op een normale manier gegooid worden, onderhands en rustig. Slingerworpen
                                    zijn absoluut verboden.</p>

                                <h4>Artikel 13: Stop</h4>
                                <p>Er wordt stop geroepen als de fles rechtop staat, de bal achter de lijn is en het
                                    volledige spelende team achter de lijn staat. Zodra er stop geroepen wordt moet er
                                    direct gestopt worden met drinken en moet het flesje binnen 2 seconden weer op de
                                    grond staan. Is een speler te laat dan hangt hier straf aan.</p>
                            </div>
                        </div>
                    </a>
                    <div class="devider"></div>
                    <a class="setting" data-bs-toggle="collapse" href="#extreme" role="button"
                       aria-expanded="false"
                       aria-controls="extreme">

                        <div class="setting-text">
                            <div>
                                <h3>Flunkyball Extreme</h3>
                                <small>De spelregels van Flunkyball Extreme, de extreme versie van Original!</small>
                            </div>
                            <span class="material-symbols-rounded">expand_more</span>
                        </div>

                        <div class="collapse multi-collapse" id="extreme">
                            <div class="bg-light p-3 rounded">
                                <h4>Regelement Flunkyball Extreme</h4>
                                <p class="fst-italic">Flunkyball Original, maar dan moeilijker!</p>

                                <p>Benodigdheden:</p>
                                <ul>
                                    <li>
                                        Per persoon 1 flesje bier
                                    </li>
                                    <li>
                                        1 petfles van 1,5 liter, met een bodem van water
                                    </li>
                                    <li>
                                        1 bal
                                    </li>
                                    <li>
                                        1 pan
                                    </li>
                                    <li>
                                        Stoepkrijt
                                    </li>
                                    <li>
                                        Fluitje
                                    </li>
                                </ul>

                                <p>De opstelling van het veld lijkt op die van Flunkyball Original, maar er zijn een
                                    paar toevoegingen. De spelers staan achter een lijn, die door middel van stoepkrijt
                                    is getekend. De spelers mogen nooit achter deze lijn vandaan, tenzij de bal uit de
                                    handen van het andere team is. De fles staan op een pan in het midden, daaromheen is
                                    met stoepkrijt een cirkel getekend.</p>

                                <p>Alle regels van Flunkyball Original gelden ook bij Extreme, maar met een paar
                                    aanpassingen:</p>
                                <ul>
                                    <li>Alle straffen bestaan uit nieuw bier, er zijn geen waarschuwingen.</li>
                                    <li>Er wordt geen stop geroepen, maar op een fluitje geblazen.</li>
                                    <li>Er wordt pas gefloten wanneer de pan in de crikel, met de fles erbovenop staat.
                                    </li>
                                    <li>De bal moet binnen 10 seconden gegooid worden als een team deze in handen heeft,
                                        te laat is nieuw bier voor de speler die gooit.
                                    </li>
                                    <li>Als er gefloten wordt moet het bier binnen een halve seconde op de grond staan,
                                        te laat is nieuw bier.
                                    </li>
                                    <li>De spelers krijgen om de beurt de bal, er mag niet zelf gekozen worden wie er gooit.</li>
                                    <li>Het flesje moet 5 seconden boven het hoofd gehouden worden in plaats van 3</li>
                                    <li>Wanneer een speler klaar is, moet deze terug gaan staan achter de lijn met het lege flesje en
                                        speelt deze mee als een normale speler (die dus ook nog steeds moet gooien). Als het lege flesje omgaat, moet er
                                        gewoon nieuw bier gepakt worden. De speler kan ook nog steeds straf krijgen.
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </a>
                    <div class="devider"></div>
                    <a class="setting" data-bs-toggle="collapse" href="#1v1" role="button"
                       aria-expanded="false"
                       aria-controls="1v1">

                        <div class="setting-text">
                            <div>
                                <h3>Flunkyball 1v1</h3>
                                <small>De spelregels als er maar 2 spelers in totaal zijn.</small>
                            </div>
                            <span class="material-symbols-rounded">expand_more</span>
                        </div>

                        <div class="collapse multi-collapse" id="1v1">
                            <div class="bg-light p-3 rounded">
                                <h4>Regelement Flunkyball 1v1</h4>
                                <p class="fst-italic">Flunkyball voor 2 mensen</p>

                                <p>Flunkyball 1v1 wordt gespeeld met maar 1 speler per team.</p>
                                <p>Alle regels van Flunkyball Original gelden ook bij 1v1, maar met een paar
                                    aanpassingen:</p>
                                <ul>
                                    <li>Er wordt stop geroepen wanneer de fles rechtop staat en de speler achter de lijn is. De bal hoeft dus niet gehaald te worden.</li>
                                    <li>De scheids zorgt ervoor dat de bal bij de juiste speler beland.</li>
                                </ul>

                            </div>
                        </div>
                    </a>

                </div>


@endsection
