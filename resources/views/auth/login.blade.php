@extends('layouts.login')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh; width: 100vw">
        <div class="login d-flex gap-4 shadow m-4">
            <div class="login-image" style="background-image: url({{ asset('img/general/MHG_vloot.jpg') }})">
{{--                <img class="login-logo" alt="logo" src="{{ asset('img/logo/MHGlogoalgemeen.png') }}">--}}
            </div>
            <div class="d-flex flex-column p-3 login-text justify-content-center">
                <h1>Log In</h1>
                <p>Log in met je MHG Account.</p>
                <p> Heb je nog geen account of ben je je wachtwoord vergeten? Stuur een <a
                        href="mailto:administratie@waterscoutingmhg.nl">mailtje</a> naar team Admin!
                </p>
                <form method="POST" action="{{ route('login') }}" class="p-3 border-2 border-info-subtle"
                      style="border: solid; border-radius: 15px;">
                    @csrf

                    <div class="row mb-3">
                        <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-mail') }}</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Wachtwoord') }}</label>

                        <div class="col-md-6">
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember"
                                       id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Aangemeld blijven') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-outline-primary">
                                {{ __('Inloggen') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection