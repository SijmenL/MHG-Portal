@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Wijzig wachtwoord</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('settings')}}">Instellingen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wijzig wachtwoord</li>
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
            <div class="mt-4">
            <h2 class="flex-row gap-3"><span class="material-symbols-rounded me-2">key</span>Pas je wachtwoord aan</h2>
            <form method="POST" action="{{ route('settings.change-password.store') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="container">
                    <div class="">
                        <label for="old_password" class="col-md-4 col-form-label ">Oud wachtwoord</label>
                        <input name="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password">
                        @error('old_password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="">
                        <label for="new_password" class="col-md-4 col-form-label ">Nieuw wachtwoord <small>Minstens 8 tekens</small></label>
                        <input name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password">
                        @error('new_password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="">
                        <label for="new_password_confirmation" class="col-md-4 col-form-label ">Herhaal nieuw wachtwoord</label>
                        <input name="new_password_confirmation" type="password" class="form-control" id="new_password_confirmation">
                    </div>

                @if ($errors->any())
                    <div class="text-danger">
                        <p>Er is iets misgegaan...</p>
                    </div>
                @endif

                <div class="d-flex flex-row flex-wrap gap-2 mt-3">
                    <button
                        onclick="function handleButtonClick(button) {
                                 button.disabled = true;
                                button.classList.add('loading');

                                // Show the spinner and hide the text
                                button.querySelector('.button-text').style.display = 'none';
                                button.querySelector('.loading-spinner').style.display = 'inline-block';
                                button.querySelector('.loading-text').style.display = 'inline-block';

                                button.closest('form').submit();
                            }
                            handleButtonClick(this)"
                        class="btn btn-success flex flex-row align-items-center justify-content-center">
                        <span class="button-text">Opslaan</span>
                        <span style="display: none" class="loading-spinner spinner-border spinner-border-sm" aria-hidden="true"></span>
                        <span style="display: none" class="loading-text" role="status">Laden...</span>
                    </button>
                    <a href="{{ route('settings') }}"
                       class="btn btn-danger text-white">Annuleren</a>
                </div>

            </form>
            </div>
        </div>
    </div>
@endsection
