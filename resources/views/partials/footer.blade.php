@section('footer')
    <div class="footer mt-3 bg-primary shadow-sm p-4 mb-0 d-flex align-items-center flex-column">
        <p class="text-light text-center">&copy;Waterscouting Matthijs Heldt Groep 2023 - {{ date("Y") }}</p>
        <p class="text-light text-center">Versie 2.1</p>

        <div class="text-light text-center mb-4">
            <a class="text-light" href="{{ route('changelog') }}">Changelog</a>
            <a class="text-light" href="https://waterscoutingmhg.nl/over-onze-club/regels-wetgeving-en-privacybeleid/privacyverklaring/" target="_blank">Privacyverklaring</a>
            <a class="text-light" href="{{ route('credits') }}">Credits</a>
        </div>

        <p class="text-light text-center">Speciaal ontwikkeld door Team Administratie voor de Matthijs Heldt Groep.</p>
        <p class="text-light text-center">Bugs graag toevoegen aan de <a class="text-light" target="_blank" href="https://github.com/SijmenL/MHG-Portal/issues">bugtracker</a>.</p>
    </div>

@endsection
