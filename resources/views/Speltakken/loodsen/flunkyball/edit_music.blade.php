@extends('layouts.loodsen')

@section('content')
    <div class="flunky-background">
        <div class="py-4 container col-md-11">
            <h1>Edit {{ $music->display_title }}</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.flunkyball') }}">Flunkyball</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.flunkyball.music') }}">Muziek Beheer</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit {{ $music->display_title }}</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif


            <form method="POST" action="{{ route('loodsen.flunkyball.music.save', $music->id) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="display_title">Naam van de knop</label>
                    <input class="form-control" id="display_title" type="text" name="display_title"
                           placeholder="Muziek!"
                           value="{{ $music->display_title }}">
                    @error('display_title')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="music_title">Titel van het geluid</label>
                    <input class="form-control" id="music_title" type="text" name="music_title" placeholder="Funky Town"
                           value="{{ $music->music_title }}">
                    @error('music_title')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="image">Afbeelding</label>
                    <div class="d-flex flex-row-responsive gap-4 align-items-center justify-content-center">
                        <img alt="cover" class="form-file"
                             src="{{ asset('files/loodsen/flunkyball/music_images/' .$music->image) }}">
                        <input class="form-control" id="image" type="file" name="image" accept="image/*">
                    </div>
                    @error('image')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="music_file">Audio</label>
                    <div class="d-flex flex-row-responsive gap-4 align-items-center justify-content-center">
                        <audio controls class="form-file"
                               src="{{ asset('files/loodsen/flunkyball/music_files/' .$music->music_file) }}"></audio>
                        <input class="form-control" id="music_file" type="file" name="music_file" accept="audio/*">
                    </div>
                    @error('music_file')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="fade_in">Fade in</label>
                    <input class="form-control" id="fade_in" type="number" name="fade_in" placeholder="10"
                           value="{{ $music->fade_in }}">
                    @error('fade_in')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="fade_out">Fade out vorig nummer</label>
                    <input class="form-control" id="fade_out" type="number" name="fade_out" placeholder="10"
                           value="{{ $music->fade_out }}">
                    @error('fade_out')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="play_type">Type geluid</label>
                    <select class="form-control" id="play_type" name="play_type">
                        <option @if($music->music_type === 1) selected @endif class='' value="1">Muziek (loop, fade,
                            onthou positie)
                        </option>
                        <option @if($music->music_type === 2) selected @endif class='' value="2">Muziek (loop, fade)
                        </option>
                        <option @if($music->music_type === 3) selected @endif class='' value="3">Sound Effect (speel
                            zonder de muziek te stoppen)
                        </option>
                    </select>
                    @error('play_type')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-row flex-wrap gap-2 align-items-center mt-4">
                    <button class="btn btn-dark " type="submit">Opslaan</button>
                    <a class="delete-button btn btn-outline-danger"
                       data-id="{{ $music->id }}"
                       data-name="{{ $music->music_title }}"
                       data-link="{{ route('loodsen.flunkyball.music.delete', $music->id) }}">Verwijderen</a>
                </div>
            </form>
        </div>
    </div>
@endsection
