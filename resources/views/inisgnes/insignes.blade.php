@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Mijn Inisgnes</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mijn Insignes</li>
            </ol>
        </nav>

        <div class="d-flex flex-row justify-content-center gap-4 flex-wrap d-none">
            <div class="card">
                <img class="card-img-top" src="{{ asset('img/insignes/61928_2.png') }}" alt="Card image cap"
                     style="filter: grayscale(1);">
                <div class="card-body">
                    <h5 class="card-title">CWO kb II</h5>
                    <p class="card-text">CWO kb II is een landelijk erkend vaarbewijs</p>
                    <p class="card-text"><strong>Theorie & praktijkexamen vereist!</strong></p>
                </div>
                <div class="card-footer">
                    <small class="text-danger">Nog niet behaald!</small>
                </div>
            </div>
        </div>

        <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
            @foreach($user_insignes as $user_insigne)
                <div class="card">
                    <img class="card-img-top" src="{{ asset('img/insignes/' . $user_insigne->image) }}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">{{ $user_insigne->title }}</h5>
                        <p class="card-text">{{ $user_insigne->description }}</p>
                        @if($user_insigne->requirements !== null)
                            <p class="card-text"><strong>{{ $user_insigne->requirements }}</strong></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <small class="text-muted"><strong>Behaald op: </strong>{{ \Carbon\Carbon::parse($user_insigne->pivot->date)->format('d-m-Y') }}</small>
                    </div>
                </div>
            @endforeach

            @foreach($insignes as $insigne)
                <div class="card">
                    <img class="card-img-top" src="{{ asset('img/insignes/' . $insigne->image) }}" alt="Card image cap"
                         style="filter: grayscale(1);">
                    <div class="card-body">
                        <h5 class="card-title">{{ $insigne->title }}</h5>
                        <p class="card-text">{{ $insigne->description }}</p>
                        @if($insigne->requirements !== null)
                            <p class="card-text"><strong>{{ $insigne->requirements }}</strong></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <small class="text-danger">Nog niet behaald!</small>
                    </div>
                </div>
            @endforeach
        </div>
@endsection
