@extends('layouts.app')

@section('content')
    @if(Session::has('continue'))
        <div class="popup" style="margin-top: -122px">
            <div class="popup-body">
                <h2>Weet je zeker dat je een ouder koppeling wilt aanmaken?</h2>
                <p>Je wordt gekoppeld met {{ Session::get('continue')['name'] }} {{ Session::get('continue')['infix'] }} {{ Session::get('continue')['last_name'] }}.</p>
                <p class="text-danger">De koppeling kan alleen verwijderd worden door de ouder, tenzij je Loods of After Loods bent.</p>
                <div class="button-container">
                    <a class="btn btn-success" href="{{ route('settings.link-parent.confirm', ['id' => Session::get('continue')['id']]) }}">Ja, koppel</a>
                    <a class="btn btn-outline-danger" href="{{ route('settings.link-parent') }}">Nee, annuleren</a>
                </div>
            </div>
        </div>
    @endif


    <div class="container col-md-11">
        <h1>Koppel ouder account</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings')}}">Instellingen</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings.parent')}}">Maak of koppel ouder
                        account</a></li>
                <li class="breadcrumb-item active" aria-current="page">Koppel ouder account</li>
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

        <div class="bg-light rounded-2 p-3">
            <h2>Maak een koppling</h2>
            <p>Vul het e-mail adres in van het account van de ouder. We kijken of het account bestaat en maken de
                koppeling voor je. Als ouder is het mogelijk om de persoonsgegevens van het kind te bekijken en aan te
                passen. Ook krijgt de ouder toegang tot de speltak omgeving van het kind.</p>
            <p class="text-danger">Alleen de ouder kan de koppeling weer ongedaan maken, tenzij je Loods of After Loods
                bent.</p>
            <form method="POST" action="{{ route('settings.link-parent.store') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="container">
                    <div class="">
                        <label for="parent_email" class="col-md-4 col-form-label ">E-mail adres van het ouder
                            account</label>
                        <input name="parent_email" value="{{ old('parent_email') }}" type="email"
                               class="form-control @error('parent_email') is-invalid @enderror" id="parent_email">
                        @error('parent_email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    @if ($errors->any())
                        <div class="text-danger">
                            <p>Er is iets misgegaan...</p>
                        </div>
                    @endif

                    <div class="d-flex flex-row flex-wrap gap-2 mt-3">
                        <button type="submit" class="btn btn-success">Volgende</button>
                        <a href="{{ route('settings.parent') }}"
                           class="btn btn-danger text-white">Annuleren</a>
                    </div>

            </form>
        </div>
    </div>
@endsection
