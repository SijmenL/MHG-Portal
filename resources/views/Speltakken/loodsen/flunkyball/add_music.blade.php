@extends('layouts.loodsen')

@section('content')
    <div class="flunky-background">
        <div class="py-4 container col-md-11">
            <h1>Voeg nummer toe</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.flunkyball') }}">Flunkyball</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.flunkyball.music') }}">Muziek Beheer</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Voeg nummer toe</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <p>Voeg een nieuw nummer toe aan de FlunkyDJ die voor iedere Loods toegangkelijk is. Een nummer heeft verschillende waardes, zoals de afbeelding, het bestand, maar ook of het een geluidseffect is, een loop moet zijn, etc.</p>


            <form method="POST" action="{{ route('loodsen.flunkyball.music.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="display_title">Naam van de knop</label>
                    <input class="form-control" id="display_title" type="text" name="display_title" placeholder="Muziek!"
                           value="{{ old('display_title') }}">
                    @error('display_title')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="music_title">Titel van het geluid</label>
                    <input class="form-control" id="music_title" type="text" name="music_title" placeholder="Funky Town"
                           value="{{ old('music_title') }}">
                    @error('music_title')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="image">Afbeelding</label>
                    <input class="form-control" id="image" type="file" name="image" accept="image/*">
                    @error('image')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="music_file">Audio</label>
                    <input class="form-control" id="music_file" type="file" name="music_file" accept="audio/*">
                    @error('music_file')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="fade_in">Fade in</label>
                    <input class="form-control" id="fade_in" type="number" name="fade_in" placeholder="10"
                           value="{{ old('fade_in') }}">
                    @error('fade_in')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="fade_out">Fade out vorig nummer</label>
                    <input class="form-control" id="fade_out" type="number" name="fade_out" placeholder="10"
                           value="{{ old('fade_out') }}">
                    @error('fade_out')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="play_type">Type geluid</label>
                    <select class="form-control" id="play_type" name="play_type">
                        <option class='' value="1">Muziek (loop, fade, onthou positie)</option>
                        <option class='' value="2">Muziek (loop, fade)</option>
                        <option class='' value="3">Sound Effect (speel zonder de muziek te stoppen)</option>
                    </select>
                    @error('play_type')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-dark mt-4" type="submit">Toevoegen aan de FlunkyDJ</button>
            </form>
        </div>
    </div>
@endsection
