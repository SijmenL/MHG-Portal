@extends('layouts.dolfijnen')

@section('content')
    <div class="container col-md-11">
        <h1>Speltak Dolfijnen</h1>
        <p></p>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

    </div>
@endsection
