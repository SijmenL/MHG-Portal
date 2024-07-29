@extends('layouts.app')
@include('partials.editor')

@vite(['resources/js/texteditor.js', 'resources/css/texteditor.css'])

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">
        <h1>Mijn inzendingen</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('news') }}">Nieuws</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mijn inzendingen</li>
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

        <form id="auto-submit" method="GET" class="user-select-forum-submit">
            <div class="d-flex">
                <div class="d-flex flex-row-responsive gap-2 align-items-center mb-3 w-100">
                    <div class="input-group">
                        <label for="search" class="input-group-text" id="basic-addon1">
                            <span class="material-symbols-rounded">search</span></label>
                        <input id="search" name="search" type="text" class="form-control"
                               placeholder="Zoeken op nieuws."
                               aria-label="Zoeken" aria-describedby="basic-addon1" value="{{ $search }}"
                               onchange="this.form.submit();">
                    </div>
                </div>
            </div>
        </form>


        @if($news_unaccepted->count() > 0 || $news_accepted->count() > 0)
            @if($news_unaccepted->count() > 0)
                <div class="bg-danger-subtle rounded p-3 m-3">
                    <h2>Ongeaccepteerd nieuws</h2>
                    <p>De nieuwtjes die je hebt ingestuurd die nog niet geaccepteerd zijn.</p>
                    <p> We kijken elk nieuwtje dat je instuurt na, om er voor te zorgen dat de
                        kwaliteit aan onze standaarden voldoet. Wees niet bang als het lang duurt voordat je nieuwtje
                        gepubliceerd is, het kan zijn dat er al te veel nieuws online is gekomen deze periode! We kunnen
                        (wanneer nodig) je nieuwtje aanpassen voordat we deze publiceren, wees daar dus niet door
                        verrast.</p>
                    <div class="d-flex flex-row flex-wrap gap-4 justify-content-center">
                        @foreach($news_unaccepted as $news_item)
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
                                        <a href="{{ route('news.user.edit', $news_item->id) }}"
                                           class="d-flex flex-row gap-2 align-items-center text-decoration-none btn btn-dark"><span
                                                class="material-symbols-rounded me-2">edit</span>Bewerk</a>
                                    </div>
                                </div>
                                <div class="card-footer d-flex flex-column gap-1">
                                    <p>{{ $news_item->date->format('d-m-Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($news_accepted->count() > 0)
                <div class="bg-success-subtle rounded p-3 m-3">
                    <h2>Geaccepteerd nieuws</h2>
                    <p>Je nieuwtjes die online staan! Deze nieuwtjes kun je niet meer bewerken!</p>
                    <div class="d-flex flex-row flex-wrap gap-4 justify-content-center">
                        @foreach($news_accepted as $news_item)
                            <a class="card text-black text-decoration-none" href="{{ route('news.item', $news_item->id) }}">
                                <p class="badge rounded-pill bg-info text-black"
                                   style="position: absolute; top: 15px; right: 15px; font-size: 1rem">{{ $news_item->category }}</p>
                                <img alt="Nieuws afbeelding" class="card-img-top"
                                     src="{{ asset('/files/news/news_images/'.$news_item->image.' ') }}">

                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <p style="font-weight: bolder">{{ $news_item->title }}</p>
                                        <p>{{ $news_item->description }}</p>
                                    </div>
                                </div>
                                <div class="card-footer d-flex flex-column gap-1">
                                    <p>{{ $news_item->date->format('d-m-Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            @if($search === null)
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <span class="material-symbols-rounded me-2">unsubscribe</span>Je hebt nog geen nieuws ingestuurd!
                </div>
            @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <span class="material-symbols-rounded me-2">unsubscribe</span>We hebben geen nieuws gevonden!
                </div>
            @endif
        @endif
    </div>


    </div>
@endsection

