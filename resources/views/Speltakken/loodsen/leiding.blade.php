@extends('layouts.loodsen')

@section('content')
    <div class=" py-4 container col-md-11">
        <h1>Loodsen Leiding</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Loodsen Leiding</li>
            </ol>
        </nav>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($leiding->count() > 0)
            <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
                @foreach($leiding as $leiding_individual)
                    <div class="card">
                        @if($leiding_individual->profile_picture)
                            <img alt="foto leiding" class="card-img-top"
                                 src="{{ asset('/profile_pictures/' . $leiding_individual->profile_picture) }}">
                        @else
                            <img alt="foto leiding" class="card-img-top"
                                 src="{{ asset('img/no_profile_picture.webp') }}">
                        @endif
                            <div class="card-body card-body-leiding">

                            <h2 class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</h2>
                            <div class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                            @if($leiding_individual->roles->contains('role', 'Loodsen Stamoudste'))
                                <h4 class="m-0">Stamoudste</h4>
                            @endif
                            @if($leiding_individual->roles->contains('role', 'Loodsen Penningmeester'))
                                <h4 class="m-0">Penningmeester</h4>
                            @endif
                            </div>

                        </div>
                        <div class="card-footer">
                            <a href="tel:{{ $leiding_individual->phone }}">{{ $leiding_individual->phone }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">person_off</span>Geen leiding gevonden...
            </div>
        @endif

    </div>
@endsection
