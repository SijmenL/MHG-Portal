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
        @php
            // NOTIFICATIES VOOR IEDEREEN:
            // jou account gegevens zijn aangepast
            // jouw wachtwoord is aangepast
            
            // iemand heeft een post geplaatst
            // jou nieuws item is geplaatst
            // heeft je post geliked
            // heeft je reactie geliked
            // heeft op je post gereageerd
            // heeft op jou reactie gereageerd

            // NOTIFICATIES VOOR ROL KINDEREN:
            // {{ naam ouder }} heeft je gegevens aangepast
            // {{ naam ouder }} heeft je als kind verwijderd
            

            // NOTIFICATIES VOOR ROL OUDERS:
            // {{ naam kind }} heeft je als ouder toegevoegd
            // {{ naam kind }} heeft je als ouder verwijderd

            // NOTIFICATIES VOOR ROL ADMINISTRATIE:
            // nieuw contactformulier is ingevuld
            // nieuwe aanmelding is binnen
            // nieuw nieuws item is geplaatst
        @endphp

        <style>
            .form-switch {
                padding-left: 0rem;
                /* padding-right: 2rem; */
            }
        </style>


        <div class="bg-light rounded-2 p-3 row">
            <div class="notification-settings-header text-center">
                <h2>Account notificaties</h2>
            </div>
            <form method="POST" action="{{ route('settings.edit-notifications.store') }}" enctype="multipart/form-data">
                @csrf
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Notificatie</th>
                            <th scope="col" class="text-center">App</th>
                            <th scope="col" class="text-center">Mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Account gegevens zijn aangepast</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_account_change' id='app_account_change' @if(old('app_account_change')) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_account_change' id='mail_account_change' @if(old('mail_account_change')) checked @endif>                                    
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Wachtwoord is aangepast</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_password_change' id='app_password_change' @if(old('app_password_change')) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_password_change' id='mail_password_change' @if(old('mail_password_change')) checked @endif>
                                </div>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </form>

            <div class="notification-settings-header text-center">
                <h2>Post & nieuws notificaties</h2>
            </div>
            <form method="POST" action="{{ route('settings.edit-notifications.store') }}" enctype="multipart/form-data">
                @csrf
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Er is een nieuwe post</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_new_post' id='app_new_post' @if(old('app_new_post')) checked @endif>                                    </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_new_post' id='mail_new_post' @if(old('mail_new_post')) checked @endif>                                    </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Jouw nieuwsitem is goedgekeurd</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_password_change' id='app_password_change' @if(old('app_password_change')) checked @endif>                                    </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_password_change' id='mail_password_change' @if(old('mail_password_change')) checked @endif>
                                </div>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </form>

            <div class="notification-settings-header text-center">
                <h2>''Per rol'' notificaties</h2>
            </div>
            <form method="POST" action="{{ route('settings.edit-notifications.store') }}" enctype="multipart/form-data">
                @csrf
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Account gegevens zijn aangepast</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_account_change' id='app_account_change' @if(old('app_account_change')) checked @endif>                                    </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_account_change' id='mail_account_change' @if(old('mail_account_change')) checked @endif>                                    </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Wachtwoord is aangepast</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_password_change' id='app_password_change' @if(old('app_password_change')) checked @endif>                                    </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_password_change' id='mail_password_change' @if(old('mail_password_change')) checked @endif>
                                </div>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </form>
            

        </div>

        @if ($errors->any())
            <div class="text-danger">
                <p>Er is iets misgegaan...</p>
            </div>
        @endif
    </div>
 
    <script>
        // onchange of the checkbox, submit the form
        document.querySelectorAll('.form-check-input').forEach(item => {
            item.addEventListener('change', event => {
                item.closest('form').submit();
            });
        });
    </script>

@endsection
