@extends('layouts.app')

@include('partials.editor')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="header" style="background-image: url({{ asset('files/news/DSC00617.JPG') }})">
        <div>
            <p class="header-title">Nieuws</p>
        </div>
    </div>
    <div class="container col-md-11">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-row-responsive align-items-center gap-5" style="width: 100%">
            <div class="" style="width: 100%;">
                <h1 class="">Opties</h1>
                <p>Welkom op de nieuws pagina. Hier vind je alle posts van de vereniging en kun je zelf posts
                    insturen!</p>
                <div class="bg-light rounded-2 p-3">
                    <h2>Acties</h2>
                    <div class="quick-action-bar">

                        <a class="btn btn-info quick-action" href="{{ route('news.user') }}">
                            <span class="material-symbols-rounded">full_coverage</span>
                            <p>Mijn inzendingen</p>
                        </a>

                        <a class="btn btn-info quick-action" href="{{ route('news.new') }}">
                            <span class="material-symbols-rounded">newspaper</span>
                            <p>Publiceer een nieuwtje</p>
                        </a>

                    </div>
                </div>
            </div>
        </div>

        <h1 class="mt-4">Al het nieuws</h1>
        <p>Al het gepubliceerde nieuws van alle speltakken</p>
        @if($news->count() > 0)
            <div class="d-flex flex-row flex-wrap gap-4 justify-content-center">
                @foreach($news as $news_item)
                    <a class="text-black text-decoration-none" href="{{ route('news.item', $news_item->id) }}">
                        <div class="card">
                            <p class="badge rounded-pill bg-info text-black"
                               style="position: absolute; top: 15px; right: 15px; font-size: 1rem">{{ $news_item->category }}</p>
                            <img alt="Nieuws afbeelding" class="card-img-top"
                                 src="{{ asset('/files/news/news_images/'.$news_item->image.' ') }}">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <p style="font-weight: bolder">{{ $news_item->title }}</p>
                                    <p>{{ $news_item->description }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('news.item', $news_item->id) }}" class="d-flex flex-row gap-2 align-items-center text-decoration-none text-black"><span
                                            class="material-symbols-rounded me-2">chevron_right</span>Lees verder!</a>
                                </div>
                            </div>
                            <div class="card-footer d-flex flex-column gap-1">
                                <p>{{ $news_item->date->format('d-m-Y') }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            {{ $news->links() }}
        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">unsubscribe</span>Geen nieuws gevonden...
            </div>
        @endif

    </div>
@endsection
