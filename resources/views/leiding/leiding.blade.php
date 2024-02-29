@extends('layouts.leiding')

@section('content')
    <div class="container col-md-11">
        <h1>Leiding & Organisatie</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('leiding') }}">Leiding</a></li>
                <li class="breadcrumb-item active" aria-current="page">Leiding & Organisatie</li>
            </ol>
        </nav>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <p>Iedereen die de vereniging mogelijk maakt, overzichtelijk op een pagina.</p>

        <ul>
            <li><a href="#bestuur">Bestuur</a></li>
            <li><a href="#dolfijnen">Dolfijnen</a></li>
            <li><a href="#zeeverkenners">Zeeverkenners</a></li>
            <li><a href="#loodsen">Loodsen</a></li>
            <li><a href="#afterloodsen">Afterloodsen</a></li>
            <li><a href="#ouderraad">Ouderraad</a></li>
            <li><a href="#vrijwilligers">Vrijwilligers</a></li>
            <li><a href="#administratie">Team Administratie</a></li>
        </ul>

        <div class="d-flex flex-column gap-4">
            <div id="bestuur" class="bg-light rounded p-2">
                <h2>Bestuur</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($bestuur as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="card-body">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                @if($leiding_individual->roles->contains('role', 'Voorzitter'))
                                    <h3>Voorzitter</h3>
                                @endif
                                @if($leiding_individual->roles->contains('role', 'Penningmeester'))
                                    <h3>Penningmeester</h3>
                                @endif
                                @if($leiding_individual->roles->contains('role', 'Secretaris'))
                                    <h3>Secretaris</h3>
                                @endif
                                @if(!$leiding_individual->roles->contains('role', 'Voorzitter') && !$leiding_individual->roles->contains('role', 'Penningmeester') && !$leiding_individual->roles->contains('role', 'Secretaris'))
                                    <h3>Bestuur</h3>
                                @endif

                            </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="dolfijnen" class="bg-light rounded p-2">
                <h2>Dolfijnen Leiding</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($dolfijnen as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="card-body">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>

                                @if($leiding_individual->roles->contains('role', 'Dolfijnen Hoofdleiding'))
                                    <h3>Hoofdleiding</h3>
                                @endif
                                @if($leiding_individual->roles->contains('role', 'Dolfijnen Penningmeester'))
                                    <h3>Penningmeester</h3>
                                @endif
                                @if(!$leiding_individual->roles->contains('role', 'Dolfijnen Penningmeester') && !$leiding_individual->roles->contains('role', 'Dolfijnen Hoofdleiding'))
                                    <h3>Leiding</h3>
                                @endif

                            </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="zeeverkenners" class="bg-light rounded p-2">
                <h2>Zeeverkenners Leiding</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($zeeverkenners as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="card-body">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>

                                @if($leiding_individual->roles->contains('role', 'Zeeverkenners Hoofdleiding'))
                                    <h3>Hoofdleiding</h3>
                                @endif
                                @if($leiding_individual->roles->contains('role', 'Zeeverkenners Penningmeester'))
                                    <h3>Penningmeester</h3>
                                @endif
                                @if(!$leiding_individual->roles->contains('role', 'Zeeverkenners Penningmeester') && !$leiding_individual->roles->contains('role', 'Zeeverkenners Hoofdleiding'))
                                    <h3>Leiding</h3>
                                @endif

                            </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="loodsen" class="bg-light rounded p-2">
                <h2>Loodsen Leiding</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($loodsen as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                                <div class="card-body">

                                    <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                    @if($leiding_individual->roles->contains('role', 'Loodsen Stamoudste'))
                                        <h3>Stamoudste</h3>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Loodsen Penningmeester'))
                                        <h3>Penningmeester</h3>
                                    @endif

                                    <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                                </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="afterloodsen" class="bg-light rounded p-2">
                <h2>Afterloodsen Organisatie</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($afterloodsen as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                                <div class="card-body">

                                    <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                    @if($leiding_individual->roles->contains('role', 'Afterloodsen Voorzitter'))
                                        <h3>Voorzitter</h3>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Afterloodsen Penningmeester'))
                                        <h3>Penningmeester</h3>
                                    @endif
                                    @if(!$leiding_individual->roles->contains('role', 'Afterloodsen Penningmeester') && !$leiding_individual->roles->contains('role', 'Afterloodsen Voorzitter'))
                                        <h3>Organisator</h3>
                                    @endif
                                </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="ouderraad" class="bg-light rounded p-2">
                <h2>Ouderraad</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($ouderraad as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="card-body">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>

                            </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="vrijwilligers" class="bg-light rounded p-2">
                <h2>Vrijwilligers</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($vrijwilliger as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="card-body">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>

                            </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="administratie" class="bg-light rounded p-2">
                <h2>Team Administratie</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($admin as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="card-body">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>

                            </div>
                            <div class="card-footer">
                                <a href="mailto:{{ $leiding_individual->email }}">{{ $leiding_individual->email }}</a>
                                <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection
