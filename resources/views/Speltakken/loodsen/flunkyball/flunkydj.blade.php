@extends('layouts.loodsen')

@vite('resources/js/flunkydj.js')

@section('content')
    <div class="flunky-background">
        <div class="py-4 container col-md-11">
            <h1>FlunkyDJ</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.flunkyball') }}">Flunkyball</a></li>
                    <li class="breadcrumb-item active" aria-current="page">FlunkyDJ</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif



        </div>
        <div class="buttons">
            <div id="loading" class="w-100 h-100 d-flex justify-content-center align-items-center"><span class="material-symbols-rounded rotating" style="font-size: xxx-large">progress_activity</span></div>
            <div class="buttons" id="button-display">
                @foreach($music as $audio)
                    <a class="music-button clickable-button" data-play-type="{{ $audio["play_type"] }}" data-fade-in="{{ $audio["fade_in"] }}"
                       data-fade-out="{{ $audio["fade_out"] }}"
                       data-audio="{{ asset('files/loodsen/flunkyball/music_files/' . $audio['music_file'])  }}"
                       style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.9)), url('{{ asset('files/loodsen/flunkyball/music_images/' . $audio['image'])}}');">
                        <p class="button-text-main">{{ $audio->display_title }}</p>
                        <p class="button-text">{{ $audio->music_title }}</p>
                    </a>
                @endforeach
                <a id="stop-music" class="clickable-button" style="background-image: linear-gradient(to bottom, #fdaaaa, #8a1a1a)">
                    <p class="button-text-main">Stop de muziek!</p>
                </a>
            </div>
        </div>
    </div>
@endsection
