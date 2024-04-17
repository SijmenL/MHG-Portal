@extends('layouts.login')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh; width: 100vw">
        <div class="login d-flex gap-4 shadow m-4">
            <div class="login-image" style="background-image: url({{ asset('img/general/MHG_vloot.jpg') }})">
                {{--                <img class="login-logo" alt="logo" src="{{ asset('img/logo/MHGlogoalgemeen.png') }}">--}}
            </div>
            <div class="d-flex flex-column p-3 login-text justify-content-center">
                <h1>Contact</h1>
                <p>Neem contact op met de Matthijs Heldt Groep!</p>
            </div>
        </div>
    </div>
@endsection
