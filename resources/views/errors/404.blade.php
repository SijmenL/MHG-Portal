@extends('layouts.app')

@section('content')
    <div class="header" style="background-image: url({{ asset('img/general/MHG_vloot.jpg') }})">
        <div>
            <p class="header-title">Hier gaat iets mis...</p>
        </div>
    </div>

    <div class="container col-md-11">
        <h1>404 error</h1>
        <p>Het lijkt erop dat de pagina die je probeert te bezoeken niet meer bestaat of is verplaatst.</p>
        <p class="text-danger">{{ $exception->getMessage() }}</p>

        <a class="btn btn-primary text-white" href="{{ route('dashboard') }}">Ga Terug Naar De Homepagina</a>



    </div>
@endsection