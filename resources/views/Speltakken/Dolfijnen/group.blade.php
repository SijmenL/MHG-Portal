@extends('layouts.dolfijnen')

@section('content')
    <div class="container col-md-11">
        <h1>Groep</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dolfijnen') }}">Dolfijnen</a></li>
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

        <form id="auto-submit" method="POST" action="{{ route('dolfijnen.group.search') }}">
            @csrf
            <div class="d-flex">
                <div class="d-flex flex-row-responsive gap-2 align-items-center mb-3 w-100">
                    <div class="input-group">
                        <label for="search" class="input-group-text" id="basic-addon1">
                            <span class="material-symbols-rounded">search</span></label>
                        <input id="search" name="search" type="text" class="form-control"
                               placeholder="Zoeken op naam, email, adres etc."
                               aria-label="Zoeken" aria-describedby="basic-addon1" value="{{ $search }}" onchange="this.form.submit();">
                    </div>
                </div>
            </div>
        </form>

        @if($users->count() > 0)
            <div class="overflow-scroll no-scrolbar" style="max-width: 100vw">
                <table class="table table-striped">
                    <thead class="thead-dark table-bordered table-hover">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Profielfoto</th>
                        <th scope="col">Naam</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefoonnummer</th>
                        <th scope="col">Opties</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($users as $all_user)
                        <tr id="{{ $all_user->id }}">
                            <th>{{ $all_user->id }}</th>
                            <th>
                                @if($all_user->profile_picture)
                                    <img alt="profielfoto" class="profle-picture"
                                         src="{{ asset('/profile_pictures/' .$all_user->profile_picture) }}">
                                @else
                                    <img alt="profielfoto" class="profle-picture"
                                         src="{{ asset('img/no_profile_picture.webp') }}">
                                @endif
                            </th>
                            <th>{{ $all_user->name .' '. $all_user->infix.' '. $all_user->last_name }}</th>
                            <th><a href="mailto:{{ $all_user->email }}">{{ $all_user->email }}</a></th>
                            <th><a href="tel:{{ $all_user->phone }}">{{ $all_user->phone }}</a></th>
                            <th>
                                <div class="d-flex flex-row flex-wrap gap-2">
                                    <a href="{{ route('dolfijnen.groep.details', ['id' => $all_user->id]) }}"
                                       class="btn btn-info">Details</a>
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
                <span class="material-symbols-rounded me-2">person_off</span>Geen dolfijnen gevonden...
            </div>
        @endif
    </div>
@endsection
