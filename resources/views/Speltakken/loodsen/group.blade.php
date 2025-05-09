@extends('layouts.loodsen')

@vite('resources/js/user-export.js')

@section('content')
    <div id="popUp" class="popup d-none" style="margin-top: -98px">
        <div class="popup-body">
            <h2>Exporteer leden</h2>
            <p>Alle loodsen die aan je zoekopdracht voldoen worden geëxporteerd.</p>
            <div class="bg-light rounded-2 p-3">
                <h2>Opties</h2>
                <div class="quick-action-bar">
                    <form class="m-0 p-0 quick-action" action="{{ route('loodsen.group.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_ids" value="{{ json_encode($user_ids) }}">
                        <input type="hidden" name="type" value="{{ $selected_role }}">

                        <button type="submit" class="btn btn-success quick-action">
                            <span class="material-symbols-rounded">table_view</span>
                            <p>Excel</p>
                        </button>
                    </form>
                    <a class="btn btn-info quick-action" href="mailto:?bcc=@foreach($users as $user_adres){{$user_adres->email}}@unless($loop->last),@endunless @endforeach">
                        <span class="material-symbols-rounded">mail</span>
                        <p>Mail</p>
                    </a>
                </div>
            </div>
            <div class="button-container">
                <a id="cancelButton" class="btn btn-outline-danger">Annuleren</a>
            </div>
        </div>
    </div>

    <div class="py-4 container col-md-11">
        <h1>Groep</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Groep</li>
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

        <form id="auto-submit" method="GET">
            <div class="d-flex">
                <div class="d-flex flex-row-responsive gap-2 align-items-center mb-3 w-100">
                    <div class="input-group">
                        <label for="search" class="input-group-text" id="basic-addon1">
                            <span class="material-symbols-rounded">search</span></label>
                        <input id="search" name="search" type="text" class="form-control"
                               placeholder="Zoeken op naam, email, adres etc."
                               aria-label="Zoeken" aria-describedby="basic-addon1" value="{{ $search }}" onchange="this.form.submit();">
                        <a @if($users->count() > 0) id="export-button" @endif class="input-group-text @if($users->count() < 1)disabled @endif" style="text-decoration: none; cursor: pointer">
                            <span class="material-symbols-rounded">ios_share</span></a>
                    </div>
                </div>
            </div>
        </form>

        @if($users->count() > 0)
            <div class="overflow-scroll no-scrolbar" style="max-width: 100vw">
                <table class="table table-striped">
                    <thead class="thead-dark table-bordered table-hover">
                    <tr>
                        <th class="no-mobile" scope="col">Profielfoto</th>
                        <th scope="col">Naam</th>
                        <th class="no-mobile" scope="col">Email</th>
                        <th class="no-mobile" scope="col">Telefoonnummer</th>
                        @if(auth()->user() && (auth()->user()->roles->contains('role', 'Dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')) || auth()->user()->roles->contains('role', 'Loodsen Mentor'))
                        <th scope="col">Opties</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($users as $all_user)
                        <tr id="{{ $all_user->id }}">
                            <th class="no-mobile">
                                @if($all_user->profile_picture)
                                    <img alt="profielfoto" class="profle-picture zoomable-image"
                                         src="{{ asset('/profile_pictures/' .$all_user->profile_picture) }}">
                                @else
                                    <img alt="profielfoto" class="profle-picture"
                                         src="{{ asset('img/no_profile_picture.webp') }}">
                                @endif
                            </th>
                            <th>{{ $all_user->name .' '. $all_user->infix.' '. $all_user->last_name }}</th>
                            <th class="no-mobile"><a href="mailto:{{ $all_user->email }}">{{ $all_user->email }}</a></th>
                            <th class="no-mobile"><a href="tel:{{ $all_user->phone }}">{{ $all_user->phone }}</a></th>
                            <th>
                                @if(auth()->user() && (auth()->user()->roles->contains('role', 'Dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')) || auth()->user()->roles->contains('role', 'Loodsen Mentor'))
                                <div class="d-flex flex-row flex-wrap gap-2">
                                    <a href="{{ route('loodsen.groep.details', ['id' => $all_user->id]) }}"
                                       class="btn btn-info">Details</a>
                                </div>
                                @endif
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">person_off</span>Geen loodsen gevonden...
            </div>
        @endif
    </div>
@endsection
