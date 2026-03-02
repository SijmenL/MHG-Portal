@extends('layouts.afterloodsen')

@section('content')
    <div class="container col-md-11">
        <h1>Inbox</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('afterloodsen') }}">Afterloodsen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inbox</li>
            </ol>
        </nav>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-light rounded-2 p-3">
            <h2 class="">Inbox</h2>
            <div class="settings-container">
                <a class="setting" href="{{ route('afterloodsen.signup') }}">
                    <div class="setting-text">
                        <div>
                            <h3>Nieuwe inschrijvingen @if($signup >= 1)<span class="badge badge-pill bg-danger">{{ $signup }}</span>@endif</h3>
                            <small>Accepteer of verwijder nieuwe inschrijvingen die binnengekomen zijn voor de Afterloodsen</small>
                        </div>
                        <span class="material-symbols-rounded">arrow_forward_ios</span>
                    </div>
                </a>
        </div>

    </div>
@endsection
