@extends(
    auth()->user()->roles->contains('role', 'Dolfijnen Leiding') ||
    auth()->user()->roles->contains('role', 'Zeeverkenners Leiding') ||
    auth()->user()->roles->contains('role', 'Loodsen Stamoudste') ||
    auth()->user()->roles->contains('role', 'Afterloodsen Organisator') ||
    auth()->user()->roles->contains('role', 'Vrijwilliger') ||
    auth()->user()->roles->contains('role', 'Administratie') ||
    auth()->user()->roles->contains('role', 'Bestuur') ||
    auth()->user()->roles->contains('role', 'Praktijkbegeleider') ||
    auth()->user()->roles->contains('role', 'Ouderraad')
    ? 'layouts.leiding'
    : 'layouts.app'
)


@section('content')
    <div class="container col-md-11">
        <h1>Leiding & Organisatie</h1>

        @if(auth()->user() &&
                    (auth()->user()->roles->contains('role', 'Dolfijnen Leiding') ||
                    auth()->user()->roles->contains('role', 'Zeeverkenners Leiding') ||
                    auth()->user()->roles->contains('role', 'Loodsen Stamoudste') ||
                    auth()->user()->roles->contains('role', 'Afterloodsen Organisator') ||
                    auth()->user()->roles->contains('role', 'Vrijwilliger') ||
                    auth()->user()->roles->contains('role', 'Administratie') ||
                    auth()->user()->roles->contains('role', 'Bestuur') ||
                    auth()->user()->roles->contains('role', 'Praktijkbegeleider') ||
                    auth()->user()->roles->contains('role', 'Ouderraad'))
                    )
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('leiding') }}">Leiding</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leiding & Organisatie</li>
                </ol>
            </nav>
        @else
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leiding & Organisatie</li>
                </ol>
            </nav>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <p>Iedereen die de vereniging mogelijk maakt, overzichtelijk op een pagina.</p>

        <ul>
            <li><a href="#bestuur">Bestuur</a></li>
            <li><a href="#administratie">Team Administratie</a></li>
            <li><a href="#dolfijnen">Dolfijnen</a></li>
            <li><a href="#zeeverkenners">Zeeverkenners</a></li>
            <li><a href="#loodsen">Loodsen</a></li>
            <li><a href="#afterloodsen">Afterloodsen</a></li>
            <li><a href="#praktijkbegeleiding">Praktijkbegeleiding</a></li>
            <li><a href="#ouderraad">Ouderraad</a></li>
            <li><a href="#vrijwilligers">Vrijwilligers</a></li>
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
                            <div class="card-body card-body-leiding">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    @if($leiding_individual->roles->contains('role', 'Voorzitter'))
                                        <h4 class="m-0">Voorzitter</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Penningmeester'))
                                        <h4 class="m-0">Penningmeester</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Secretaris'))
                                        <h4 class="m-0">Secretaris</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Vice-voorzitter'))
                                        <h4 class="m-0">Vice-voorzitter</h4>
                                    @endif
                                    @if(!$leiding_individual->roles->contains('role', 'Voorzitter') && !$leiding_individual->roles->contains('role', 'Vice-voorzitter') && !$leiding_individual->roles->contains('role', 'Penningmeester') && !$leiding_individual->roles->contains('role', 'Secretaris'))
                                        <h4 class="m-0">Bestuur</h4>
                                    @endif
                                </div>

                            </div>
                            <div class="card-footer d-flex flex-column gap-1">
                                @if($leiding_individual->roles->contains('role', 'Penningmeester'))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="mailto:penningmeester@waterscoutingmhg.nl"><span
                                            class="material-symbols-rounded">email</span><span
                                            >penningmeester@waterscoutingmhg.nl</span></a>
                                @endif
                                @if($leiding_individual->roles->contains('role', 'Secretaris'))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="mailto:secretaris@waterscoutingmhg.nl"><span
                                            class="material-symbols-rounded">email</span><span
                                            >secretaris@waterscoutingmhg.nl</span></a>
                                @endif
                                @if(!$leiding_individual->roles->contains('role', 'Penningmeester') && !$leiding_individual->roles->contains('role', 'Secretaris') && $leiding_individual->roles->contains('role', 'Bestuur'))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="mailto:bestuur@waterscoutingmhg.nl"><span
                                            class="material-symbols-rounded">email</span><span
                                            >bestuur@waterscoutingmhg.nl</span></a>
                                @endif
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
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
                            <div class="card-body card-body-leiding">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    <h4 class="m-0">Team Administratie</h4>
                                </div>
                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                   href="mailto:administratie@waterscoutingmhg.nl"><span
                                        class="material-symbols-rounded">email</span><span
                                        >administratie@waterscoutingmhg.nl</span></a>
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
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
                            <div class="card-body card-body-leiding">

                                @if(auth()->user()->roles->contains('role', 'Dolfijn'))
                                    <h2 class="card-title">{{ $leiding_individual->dolfijnen_name }}</h2>
                                @else
                                    <h2 class="card-title">{{ $leiding_individual->dolfijnen_name }}</h2>
                                    <p class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</p>
                                @endif
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    @if($leiding_individual->roles->contains('role', 'Dolfijnen Hoofdleiding'))
                                        <h4 class="m-0">Hoofdleiding</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Dolfijnen Penningmeester'))
                                        <h4 class="m-0">Penningmeester</h4>
                                    @endif
                                    @if(!$leiding_individual->roles->contains('role', 'Dolfijnen Penningmeester') && !$leiding_individual->roles->contains('role', 'Dolfijnen Hoofdleiding'))
                                        <h4 class="m-0">Leiding</h4>
                                    @endif
                                </div>

                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                   href="mailto:dolfijnen@waterscoutingmhg.nl"><span
                                        class="material-symbols-rounded">email</span><span
                                        >dolfijnen@waterscoutingmhg.nl</span></a>
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
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
                            <div class="card-body card-body-leiding">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>

                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    @if($leiding_individual->roles->contains('role', 'Zeeverkenners Hoofdleiding'))
                                        <h4 class="m-0">Hoofdleiding</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Zeeverkenners Penningmeester'))
                                        <h4 class="m-0">Penningmeester</h4>
                                    @endif
                                    @if(!$leiding_individual->roles->contains('role', 'Zeeverkenners Penningmeester') && !$leiding_individual->roles->contains('role', 'Zeeverkenners Hoofdleiding'))
                                        <h4 class="m-0">Leiding</h4>
                                    @endif
                                </div>

                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                   href="mailto:zeeverkenners@waterscoutingmhg.nl"><span
                                        class="material-symbols-rounded">email</span><span
                                        >zeeverkenners@waterscoutingmhg.nl</span></a>
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
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
                            <div class="card-body card-body-leiding">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    @if($leiding_individual->roles->contains('role', 'Loodsen Mentor'))
                                        <h4 class="m-0">Loodsen Mentor</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Loodsen Stamoudste'))
                                        <h4 class="m-0">Stamoudste</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Loodsen Penningmeester'))
                                        <h4 class="m-0">Penningmeester</h4>
                                    @endif
                                </div>
                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                   href="mailto:loodsen@waterscoutingmhg.nl"><span
                                        class="material-symbols-rounded">email</span><span
                                        >loodsen@waterscoutingmhg.nl</span></a>
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
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
                            <div class="card-body card-body-leiding">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    @if($leiding_individual->roles->contains('role', 'Afterloodsen Voorzitter'))
                                        <h4 class="m-0">Voorzitter</h4>
                                    @endif
                                    @if($leiding_individual->roles->contains('role', 'Afterloodsen Penningmeester'))
                                        <h4 class="m-0">Penningmeester</h4>
                                    @endif
                                    @if(!$leiding_individual->roles->contains('role', 'Afterloodsen Penningmeester') && !$leiding_individual->roles->contains('role', 'Afterloodsen Voorzitter'))
                                        <h4 class="m-0">Organisator</h4>
                                    @endif
                                </div>
                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                   href="mailto:afterloodsen@waterscoutingmhg.nl"><span
                                        class="material-symbols-rounded">email</span><span
                                        >afterloodsen@waterscoutingmhg.nl</span></a>
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="praktijkbegeleiding" class="bg-light rounded p-2">
                <h2>Praktijkbegeleiding</h2>
                <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                    @foreach($praktijkbgeleiding as $leiding_individual)
                        <div class="card">
                            @if($leiding_individual->profile_picture)
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                            @else
                                <img alt="foto leiding" class="card-img-top"
                                     src="{{ asset('img/no_profile_picture.webp') }}">
                            @endif
                            <div class="card-body card-body-leiding">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    <h4 class="m-0">Praktijkbegeleider</h4>
                                </div>
                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                   href="mailto:praktijkbegeleider@waterscoutingmhg.nl"><span
                                        class="material-symbols-rounded">email</span><span
                                        >praktijkbegeleider@waterscoutingmhg.nl</span></a>
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
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
                            <div class="card-body card-body-leiding">

                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    <h4 class="m-0">Ouderraad</h4>
                                </div>
                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                   href="mailto:ouderraad@waterscoutingmhg.nl"><span
                                        class="material-symbols-rounded">email</span><span
                                        >ouderraad@waterscoutingmhg.nl</span></a>
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
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
                            <div class="card-body card-body-leiding">
                                <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                                <div
                                    class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                                    <h4 class="m-0">Vrijwilligers</h4>
                                </div>
                            </div>
                             <div class="card-footer d-flex flex-column gap-1">
                                @if(isset($leiding_individual->phone))
                                    <a class="btn btn-outline-dark btn-text d-flex flex-row gap-1 align-items-center"
                                       href="tel:{{ $leiding_individual->phone }}"><span
                                            class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection
