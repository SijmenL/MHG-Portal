@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Welkom, {{ $user->name }}</h1>
        <p>{{ $date }}</p>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-light rounded-2 p-3">
            <h2>Snelle Acties</h2>
            <div class="quick-action-bar">

                @if(auth()->user() && auth()->user()->roles->contains('role', 'Administratie'))
                <a class="btn btn-admin quick-action" href="{{ route('admin') }}">
                    <span class="material-symbols-rounded">admin_panel_settings</span>
                    <p>Administratie</p>
                </a>
                @endif

                <a class="btn btn-info quick-action" href="{{ route('account') }}">
                    <span class="material-symbols-rounded">person</span>
                    <p>Mijn account</p>
                </a>


                <a class="btn btn-info quick-action" href="">
                    <span class="material-symbols-rounded">family_restroom</span>
                    <p>Mijn kinderen</p>
                </a>


{{--                <a class="btn btn-info quick-action" href="">--}}
{{--                    <span class="material-symbols-rounded">school</span>--}}
{{--                    <p>Lessen</p>--}}
{{--                </a>--}}


{{--                <a class="btn btn-info quick-action" href="{{ route('insignes') }}">--}}
{{--                    <span class="material-symbols-rounded">award_star</span>--}}
{{--                    <p>Insignes</p>--}}
{{--                </a>--}}

{{--                <a class="btn btn-info quick-action" href="">--}}
{{--                    <span class="material-symbols-rounded">code</span>--}}
{{--                    <p>Website beheer</p>--}}
{{--                </a>--}}


                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/dolfijnen.png') }}">
                    <p>Dolfijnen</p>
                </a>
                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/zeeverkenners.png') }}">
                    <p>Zeeverkenners</p>
                </a>
                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/loodsen.png') }}">
                    <p>Loodsen</p>
                </a>
                <a class="btn btn-dark quick-action" href="">
                    <img alt="dolfijnen" src="{{ asset('img/icons/after_loodsen.png') }}">
                    <p>After Loodsen</p>
                </a>


{{--                <a class="btn btn-secondary quick-action" href="">--}}
{{--                    <span class="material-symbols-rounded">archive</span>--}}
{{--                    <p>Club archief</p>--}}
{{--                </a>--}}
{{--                <a class="btn btn-secondary quick-action" href="">--}}
{{--                    <span class="material-symbols-rounded">news</span>--}}
{{--                    <p>Nieuws</p>--}}
{{--                </a>--}}
{{--                <a class="btn btn-secondary quick-action" href="">--}}
{{--                    <span class="material-symbols-rounded">event</span>--}}
{{--                    <p>Evenementen</p>--}}
{{--                </a>--}}
            </div>
        </div>
        <h1 class="mt-2"></h1>
    </div>
@endsection
