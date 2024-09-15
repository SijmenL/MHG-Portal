@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Notificaties</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings')}}">Instellingen</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings.edit-notifications')}}">Notificaties</a></li>
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

        <form method="POST" action="{{ route('settings.edit-notifications.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="bg-light rounded-2 p-3">
                <h2>App notificaties</h2>
                <p>Hier kun je instellen welke notificaties je wilt ontvangen via de app.</p>

                <div class="container">
                    <div class="row">
                        <div class="col">
                            
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-light rounded-2 p-3 mt-3">
                <h2>Email notificaties</h2>
                <p>Hier kun je instellen welke notificaties je wilt ontvangen via de mail.</p>


                <div class="container">
                    <div class="row">
                        <div class="col">
                            
                        </div>
                    </div>
                </div>
                
            </div>  
        
            @if ($errors->any())
                <div class="text-danger">
                    <p>Er is iets misgegaan...</p>
                </div>
            @endif

            <div class="d-flex flex-row flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-success">Opslaan</button>
                <a href="{{ route('settings') }}"
                    class="btn btn-danger text-white">Annuleren</a>
            </div>

        </form>
    </div>
@endsection
