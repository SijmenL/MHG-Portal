@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Notificaties</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings') }}">Instellingen</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notificaties</li>
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

        @endphp

        <style>
            .form-switch {
                padding-left: 0rem;
                /* padding-right: 2rem; */
            }

            td {
                max-width: 250px;
            }
        </style>


        <div class="bg-light rounded-2 p-4 row">
            <div>
                <h2 class="d-flex flex-row gap-1 mb-3 align-items-center"><span
                        class="material-symbols-rounded">person</span>Account</h2>
                <form method="POST" action="{{ route('settings.edit-notifications.store') }}"
                      enctype="multipart/form-data">
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
                            <td>Je account gegevens zijn aangepast.</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_account_change' id='app_account_change'
                                           @if( !isset($notification_settings['app_account_change'])) checked @endif>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='mail_account_change' id='mail_account_change'
                                           @if( !isset($notification_settings['mail_account_change'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Je wachtwoord is aangepast.</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_password_change' id='app_password_change'
                                           @if( !isset($notification_settings['app_password_change'])) checked @endif>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='mail_password_change' id='mail_password_change'
                                           @if( !isset($notification_settings['mail_password_change'])) checked @endif>
                                </div>
                            </td>
                        </tr>


                        </tbody>
                    </table>
                </form>

            </div>

            <div class="mt-4">
                <h2 class="d-flex flex-row gap-1 align-items-center mb-3"><span class="material-symbols-rounded">local_post_office</span>Posts,
                    Nieuws & Activiteiten</h2>
                <form method="POST" action="{{ route('settings.edit-notifications.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="hidden_form_field">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td>Iemand heeft een nieuwe post geplaatst</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_new_post' id='app_new_post'
                                           @if( !isset($notification_settings['app_new_post'])) checked @endif>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='mail_new_post' id='mail_new_post'
                                           @if( !isset($notification_settings['mail_new_post'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Je nieuws item is goedgekeurd</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_news_accepted' id='app_news_accepted'
                                           @if( !isset($notification_settings['app_news_accepted'])) checked @endif>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='mail_news_accepted' id='mail_news_accepted'
                                           @if( !isset($notification_settings['mail_news_accepted'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Iemand heeft op je post gereageerd</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_comment_post' id='app_comment_post'
                                           @if( !isset($notification_settings['app_comment_post'])) checked @endif>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='mail_comment_post' id='mail_comment_post'
                                           @if( !isset($notification_settings['mail_comment_post'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Iemand heeft op je reactie gereageerd</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_comment_comment' id='app_comment_comment'
                                           @if( !isset($notification_settings['app_comment_comment'])) checked @endif>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='mail_comment_comment' id='mail_comment_comment'
                                           @if( !isset($notification_settings['mail_comment_comment'])) checked @endif>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Iemand heeft jouw post geliked</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_liked_post' id='app_liked_post'
                                           @if( !isset($notification_settings['app_liked_post'])) checked @endif>
                                </div>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>Iemand heeft jouw reactie geliked</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                    <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                           name='app_liked_comment' id='app_liked_comment'
                                           @if( !isset($notification_settings['app_liked_comment'])) checked @endif>
                                </div>
                            </td>
                            <td></td>
                        </tr>

                        </tbody>
                    </table>
                </form>
            </div>

            @if(auth()->user() && (auth()->user()->roles->contains('role', 'Administratie')) || (auth()->user()->roles->contains('role', 'Secretaris')))
                <div class="mt-4">
                    <h2 class="d-flex mb-2 flex-row gap-1 align-items-center"><span class="material-symbols-rounded">admin_panel_settings</span>Administratie
                    </h2>
                    <form method="POST" action="{{ route('settings.edit-notifications.store') }}"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="hidden_form_field">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>Er is een nieuw contactformulier ingevuld</td>
                                <td>
                                    <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                        <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                               name='app_contact_message' id='app_contact_message'
                                               @if( !isset($notification_settings['app_contact_message'])) checked @endif>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                        <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                               name='mail_contact_message' id='mail_contact_message'
                                               @if( !isset($notification_settings['mail_contact_message'])) checked @endif>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Er wacht een nieuwsitem op goedkeuring</td>
                                <td>
                                    <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                        <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                               name='app_news_waiting' id='app_news_waiting'
                                               @if( !isset($notification_settings['app_news_waiting'])) checked @endif>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                        <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                               name='mail_news_waiting' id='mail_news_waiting'
                                               @if( !isset($notification_settings['mail_news_waiting'])) checked @endif>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Iemand heeft zich ingeschreven</td>
                                <td>
                                    <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                        <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                               name='app_new_registration' id='app_new_registration'
                                               @if( !isset($notification_settings['app_new_registration'])) checked @endif>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex align-items-end justify-content-center">
                                        <input class="form-check-input fs-4 m-0" type="checkbox" role="switch"
                                               name='mail_new_registration' id='mail_new_registration'
                                               @if( !isset($notification_settings['mail_new_registration'])) checked @endif>
                                    </div>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </form>
                </div>
            @endif


        </div>

        @if ($errors->any())
            <div class="text-danger">
                <p>Er is iets misgegaan...</p>
            </div>
        @endif
    </div>

    <script>
        document.querySelectorAll('.form-check-input').forEach(item => {
            item.addEventListener('change', async event => {
                // Get the closest form
                let form = item.closest('form');
                // Get id from the checkbox
                let id = item.getAttribute('id');
                // Set the hidden input field's value to the checkbox id
                form.querySelector('input[name="hidden_form_field"]').value = id;

                // Create FormData object from the form
                let formData = new FormData(form);

                // Send the form data with fetch
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Indicate AJAX request
                            'X-CSRF-Token': form.querySelector('input[name="_token"]').value // CSRF token
                        }
                    });

                    if (response.ok) {
                        console.log("Form submitted successfully");
                        // Optional: Add any success handling here
                    } else {
                        console.error("Error submitting form");
                        // Optional: Handle error feedback here
                    }
                } catch (error) {
                    console.error("Fetch error: ", error);
                }
            });
        });
    </script>


@endsection
