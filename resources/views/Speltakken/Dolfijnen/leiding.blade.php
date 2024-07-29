@extends('layouts.dolfijnen')

@section('content')
    <div class="container col-md-11">
        <h1>Dolfijnen leiding</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dolfijnen') }}">Dolfijnen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dolfijnen leiding</li>
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

                            @if(auth()->user()->roles->contains('role', 'Dolfijn'))
                                <h2 class="card-title">{{ $leiding_individual->dolfijnen_name }}</h2>
                            @else
                                <h2 class="card-title">{{ $leiding_individual->dolfijnen_name }}</h2>
                                <p class="card-title">{{ $leiding_individual->name.' '.$leiding_individual->infix.' '.$leiding_individual->last_name }}</p>
                            @endif
                                <div class="bg-info text-dark rounded d-flex align-items-center justify-content-center role-badge">
                            @if($leiding_individual->roles->contains('role', 'Dolfijnen Hoofdleiding'))
                                <h4 class="m-0">Hoofdleiding</h4>
                            @endif
                            @if($leiding_individual->roles->contains('role', 'Dolfijnen Penningmeester'))
                                <h4 class="m-0">Penningmeester</h4>
                            @endif
                            @if(!$leiding_individual->roles->contains('role', 'Dolfijnen Penningmeester') && !$leiding_individual->roles->contains('role', 'Dolfijnen Hoofdleiding'))
                                <h4 class="m-0">Leiding</h4>
                            @endif
                                </div>

                        </div>
                        <div class="card-footer">
                            @if(isset($leiding_individual->phone))
                                <a class="btn btn-outline-dark d-flex flex-row gap-1 align-items-center"
                                   href="tel:{{ $leiding_individual->phone }}"><span
                                        class="material-symbols-rounded">phone</span> {{ $leiding_individual->phone }}
                                </a>
                            @endif
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
