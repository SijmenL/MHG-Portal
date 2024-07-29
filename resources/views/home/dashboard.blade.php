@extends('layouts.app')

@section('content')
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
            <a class="btn btn-outline-dark d-flex align-items-center justify-content-center" style="border: none">
                <span class="material-symbols-rounded" style="font-size: xx-large">help</span>
            </a>
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
                <a class="btn btn-secondary quick-action" href="">
                    <span class="material-symbols-rounded">event</span>
                    <p>Agenda</p>
                </a>
            </div>
        </div>
        <h1 class="mt-2"></h1>
    </div>
@endsection
