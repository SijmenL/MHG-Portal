@extends('layouts.loodsen')

@section('content')
    <div class="flunky-background">
        <div class="py-4 container col-md-11">
            <h1>Muziek Beheer</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.flunkyball') }}">Flunkyball</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Muziek Beheer</li>
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

            <p>Pas de muziek aan die in de FlunkyDJ zit, of voeg een nieuw nummer toe!</p>
            <div class="d-flex mb-4">
                <a href="{{ route('loodsen.flunkyball.music.add') }}" class="btn btn-outline-dark make-role-button">Voeg een nummer toe!</a>
            </div>



        @if($all_music->count() > 0)
            <div class="overflow-scroll no-scrolbar">
                <table class="table table-striped">
                    <thead class="thead-dark table-bordered table-hover">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Naam</th>
                        <th class="no-mobile" scope="col">Afbeelding</th>
                        <th scope="col">Opties</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($all_music as $music)
                        <tr id="{{ $music->id }}">
                            <th>{{ $music->id }}</th>
                            <th> {{ $music->display_title }}</th>
                            <th class="no-mobile"><img alt="cover" class="cover"
                                     src="{{ asset('files/loodsen/flunkyball/music_images/' .$music->image) }}"></th>
                            <th>
                                <div class="d-flex flex-row flex-wrap gap-2">
                                    <a href="{{ route('loodsen.flunkyball.music.edit', ['id' => $music->id]) }}"
                                       class="btn btn-dark">Bewerk</a>
                                </div>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $all_music->links() }}
        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">no_accounts</span>Geen muziek gevonden...
            </div>
        @endif
    </div>
    </div>

@endsection
