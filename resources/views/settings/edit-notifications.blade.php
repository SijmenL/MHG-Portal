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
            // jou account gegevens zijn aangepast --DONE
            // jouw wachtwoord is aangepast --DONE

            // iemand heeft een post geplaatst --DONE
            // jou nieuws item is goedgekeurd --DONE
            // heeft je post geliked --DONE
            // heeft je reactie geliked --DONE
            // heeft op je post gereageerd --DONE
            // heeft op jou reactie gereageerd --DONE

            // NOTIFICATIES VOOR ROL ADMINISTRATIE:
            // nieuw contactformulier is ingevuld --DONE
            // nieuwe aanmelding is binnen --DONE
            // nieuw nieuws item is geplaatst --DONE
        @endphp

        <style>
            .form-switch {
                padding-left: 0rem;
                /* padding-right: 2rem; */
            }

            td{
                max-width: 250px;
            }
        </style>


        <div class="bg-light rounded-2 p-3 row">
            <div class="notification-settings-header text-center">
                <h2>Account</h2>
            </div>
            <form method="POST" action="{{ route('settings.edit-notifications.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="hidden_form_field">
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
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_account_change' id='app_account_change' @if( !isset($notification_settings['app_account_change'])) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_account_change' id='mail_account_change' @if( !isset($notification_settings['mail_account_change'])) checked @endif>                                    
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Wachtwoord is aangepast</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_password_change' id='app_password_change' @if( !isset($notification_settings['app_password_change'])) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_password_change' id='mail_password_change' @if( !isset($notification_settings['mail_password_change'])) checked @endif>
                                </div>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </form>

            <div class="notification-settings-header text-center">
                <h2>Post & nieuws</h2>
            </div>
            <form method="POST" action="{{ route('settings.edit-notifications.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="hidden_form_field">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Er is een nieuwe post</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_new_post' id='app_new_post' @if( !isset($notification_settings['app_new_post'])) checked @endif>                                   
                                 </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_new_post' id='mail_new_post' @if( !isset($notification_settings['mail_new_post'])) checked @endif>                                    
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Jouw nieuws item is goedgekeurd</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_news_accepted' id='app_news_accepted' @if( !isset($notification_settings['app_news_accepted'])) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_news_accepted' id='mail_news_accepted' @if( !isset($notification_settings['mail_news_accepted'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Iemand heeft jouw post geliked</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_liked_post' id='app_liked_post' @if( !isset($notification_settings['app_liked_post'])) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_liked_post' id='mail_liked_post' @if( !isset($notification_settings['mail_liked_post'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Iemand heeft jouw reactie geliked</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_liked_comment' id='app_liked_comment' @if( !isset($notification_settings['app_liked_comment'])) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_liked_comment' id='mail_liked_comment' @if( !isset($notification_settings['mail_liked_comment'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Er is gereageerd op jouw post</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_comment_post' id='app_comment_post' @if( !isset($notification_settings['app_comment_post'])) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_comment_post' id='mail_comment_post' @if( !isset($notification_settings['mail_comment_post'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Er is gereageerd op jouw reactie</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_comment_comment' id='app_comment_comment' @if( !isset($notification_settings['app_comment_comment'])) checked @endif>                                    
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_comment_comment' id='mail_comment_comment' @if( !isset($notification_settings['mail_comment_comment'])) checked @endif>
                                </div>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </form>

            @php
                @endphp
                @if(auth()->user() && (auth()->user()->roles->contains('role', 'Administratie')))
                    <div class="notification-settings-header text-center">
                        <h2>Administratie</h2>
                    </div>
                    <form method="POST" action="{{ route('settings.edit-notifications.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="hidden_form_field">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Nieuw contactformulier ingevuld</td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                            <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_contact_message' id='app_contact_message' @if( !isset($notification_settings['app_contact_message'])) checked @endif>                                    
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                            <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_contact_message' id='mail_contact_message' @if( !isset($notification_settings['mail_contact_message'])) checked @endif>                                    
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Nieuws item wacht op goedkeuring</td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                            <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_news_waiting' id='app_news_waiting' @if( !isset($notification_settings['app_news_waiting'])) checked @endif>                                   
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                            <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_news_waiting' id='mail_news_waiting' @if( !isset($notification_settings['mail_news_waiting'])) checked @endif>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Nieuwe aanmelding ontvangen</td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                            <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='app_new_registration' id='app_new_registration' @if( !isset($notification_settings['app_new_registration'])) checked @endif>                                   
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                            <input class="form-check-input fs-4 m-0"  type="checkbox" role="switch" name='mail_new_registration' id='mail_new_registration' @if( !isset($notification_settings['mail_new_registration'])) checked @endif>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </form>
                @endif
            

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
                // get closest form
                let form = item.closest('form');
                // get id from the checkbox
                let id = item.getAttribute('id');
                // get the hidden input field
                let hidden = form.querySelector('input[name="hidden_form_field"]');
                // set the value of the hidden input field to the id of the checkbox
                hidden.value = id;
                // submit the form
                form.submit();
            });
        });
    </script>

@endsection
