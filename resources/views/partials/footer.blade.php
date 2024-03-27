@section('footer')
    <div class="footer bg-primary shadow-sm p-4 mb-0 d-flex align-items-center flex-column">
        <p class="text-light text-center">&copy;Waterscouting Matthijs Heldt Groep 2023 - {{ date("Y") }}</p>
        <p class="text-light text-center">Versie 1.0</p>

        <div class="text-light text-center mb-4">
            <a class="text-light" href="{{ route('changelog') }}">Changelog</a>
            <a class="text-light" href="https://waterscoutingmhg.nl/over-onze-club/regels-wetgeving-en-privacybeleid/privacyverklaring/" target="_blank">Privacyverklaring</a>
            <a class="text-light" href="{{ route('credits') }}">Credits</a>
        </div>

        <p class="text-light text-center">Speciaal ontwikkeld door Team Administratie voor de Matthijs Heldt Groep. Fouten en bugs graag mailen naar:
                <a class="text-light text-center" href="mailto:administratie@waterscoutingmhg.nl">administratie@waterscoutingmhg.nl</a>
        </p>
    </div>

@endsection
