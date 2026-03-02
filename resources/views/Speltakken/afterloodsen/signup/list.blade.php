@extends('layouts.afterloodsen')

@vite('resources/js/user-export.js')

@section('content')
    <div id="popUp" class="popup d-none" style="margin-top: -122px">
        <div class="popup-body">
            <h2>Exporteer gebruikers</h2>
            <p>Alle leden die aan de criteria voldoen worden geëxporteerd.</p>
            <div class="bg-light rounded-2 p-3">
                <h2>Opties</h2>
                <div class="quick-action-bar">
                    <form class="m-0 p-0 quick-action" action="{{ route('admin.account-management.export') }}"
                          method="POST">
                        @csrf
                        <input type="hidden" name="user_ids" value="{{ json_encode($user_ids) }}">

                        <button type="submit" class="btn btn-success quick-action">
                            <span class="material-symbols-rounded">table_view</span>
                            <p>Excel</p>
                        </button>
                    </form>
                    <a class="btn btn-info quick-action"
                       href="mailto:?bcc=@foreach($users as $user_adres){{$user_adres->email}}@unless($loop->last),@endunless @endforeach">

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

    @if(isset($acceptedUser))
    <div id="popUpDetails" class="popup" style="margin-top: -122px">
        <div class="popup-body">
            <h2>{{ $acceptedUser->name }} is geaccepteerd!</h2>
            <p>{{ $acceptedUser->name }} is toegevoegd aan de ledenlijsten en heeft nu toegang tot portal.</p>
            <p>Vergeet geen welkomstmailtje met alle informatie te sturen!</p>

            <a class="btn btn-info quick-action"
               href="mailto:{{$acceptedUser->email}}">

                <span class="material-symbols-rounded">mail</span>
                <p>Mail</p>
            </a>

            <div class="button-container">
                <a id="cancelButtonDetails" class="btn btn-outline-danger">Sluiten</a>
            </div>
        </div>

        <script>
            let button = document.getElementById("cancelButtonDetails")
            let popup = document.getElementById("popUpDetails")

            button.addEventListener("click", function () {
                popup.classList.add("d-none")
            })
        </script>
    </div>
    @endif

    <div class="container col-md-11">
        <h1>Nieuwe inschrijvingen</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('afterloodsen')}}">Afterloodsen</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('afterloodsen.inbox')}}">Inbox</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inschrijvingen</li>
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

        <div class="d-flex flex-row-reverse pb-3">
            <a @if($users->count() > 0) id="export-button"
               @endif class="input-group-text @if($users->count() < 1)disabled @endif"
               style="text-decoration: none; cursor: pointer">
                <span class="material-symbols-rounded">ios_share</span></a>
        </div>

        @if($users->count() > 0)
            <div style="max-width: 100vw">
                <table class="table table-striped">
                    <thead class="thead-dark table-bordered table-hover">
                    <tr>
                        <th scope="col">#</th>
                        <th class="no-mobile" scope="col">Profielfoto</th>
                        <th scope="col">Naam</th>
                        <th class="no-mobile" scope="col">Email</th>
                        <th class="no-mobile" scope="col">Speltak</th>
                        <th scope="col">Opties</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($users as $all_user)
                        <tr id="{{ $all_user->id }}">
                            <th>{{ $all_user->id }}</th>
                            <th class="no-mobile">
                                @if($all_user->profile_picture)
                                    <img alt="profielfoto" class="profle-picture"
                                         src="{{ asset('/profile_pictures/' .$all_user->profile_picture) }}">
                                @else
                                    <img alt="profielfoto" class="profle-picture"
                                         src="{{ asset('img/no_profile_picture.webp') }}">
                                @endif
                            </th>
                            <th>{{ $all_user->name .' '. $all_user->infix.' '. $all_user->last_name }}</th>
                            <th class="no-mobile"><a href="mailto:{{ $all_user->email }}">{{ $all_user->email }}</a>
                            </th>
                            <th class="no-mobile">
                                <div class="d-flex flex-row gap-1 flex-wrap">
                                    @foreach ($all_user->roles as $role)
                                        <span title="{{ $role->description }}"
                                              class="badge rounded-pill text-bg-primary text-white fs-6 p-2">{{ $role->role }}</span>
                                    @endforeach
                                </div>
                            </th>
                            <th>
                                <div class="dropdown position-relative">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        Opties
                                    </button>
                                    <ul class="dropdown-menu"
                                        style="z-index: 10050; transform: translate3d(0, 10px, 0);">
                                        <li>
                                            <a href="{{ route('afterloodsen.signup.details', ['id' => $all_user->id]) }}"
                                               class="dropdown-item">Details</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('afterloodsen.signup.accept', ['id' => $all_user->id]) }}"
                                               class="dropdown-item text-success">Accepteer</a>
                                        </li>
                                        <li>
                                            <a class="delete-button dropdown-item cursor-pointer text-danger"
                                               data-id="{{ $all_user->id }}"
                                               data-name="{{ $all_user->name . ' ' . $all_user->infix . ' ' . $all_user->last_name }}"
                                               data-link="{{ route('afterloodsen.signup.delete', $all_user->id) }}">Verwijderen</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">person_off</span>Geen nieuwe inschrijvingen gevonden...
            </div>
        @endif
    </div>
@endsection
