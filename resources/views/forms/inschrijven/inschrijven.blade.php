@extends('layouts.contact')

@section('content')
    <script>
        window.addEventListener('load', init);

        let step1;
        let step2;
        let step3;

        let stepDisplay1;
        let stepDisplay2;
        let stepDisplay3;

        let previous;
        let next;

        let page = 0;

        function init() {
            step1 = document.getElementById('step1');
            step2 = document.getElementById('step2');
            step3 = document.getElementById('step3');

            stepDisplay1 = document.getElementById('step-display-1')
            stepDisplay2 = document.getElementById('step-display-2')
            stepDisplay3 = document.getElementById('step-display-3')

            previous = document.getElementById('previous')
            next = document.getElementById('next')

            let checkbox = document.getElementById('avg');

            checkbox.addEventListener('change', function() {
                if (checkbox.checked) {
                    checkbox.value = 1;
                } else {
                    checkbox.value = 0;
                }
            });

            next.addEventListener('click', function () {
                if (page < 2) {
                    page++
                }

                console.log(page)

                if (page === 1) {
                    stepDisplay1.classList.remove('form-step-active')
                    stepDisplay2.classList.add('form-step-active')
                    stepDisplay3.classList.remove('form-step-active')

                    step1.classList.add('d-none')
                    step2.classList.remove('d-none')
                    step3.classList.add('d-none')

                    previous.classList.remove('disabled')
                }

                if (page === 2) {
                    stepDisplay1.classList.remove('form-step-active')
                    stepDisplay2.classList.remove('form-step-active')
                    stepDisplay3.classList.add('form-step-active')

                    step1.classList.add('d-none')
                    step2.classList.add('d-none')
                    step3.classList.remove('d-none')

                    next.classList.add('disabled')
                }
            })

            previous.addEventListener('click', function () {
                if (page > 0) {
                    page--
                }

                console.log(page)

                if (page === 1) {
                    stepDisplay1.classList.remove('form-step-active')
                    stepDisplay2.classList.add('form-step-active')
                    stepDisplay3.classList.remove('form-step-active')

                    step1.classList.add('d-none')
                    step2.classList.remove('d-none')
                    step3.classList.add('d-none')

                    next.classList.remove('disabled')
                }

                if (page === 0) {
                    stepDisplay1.classList.add('form-step-active')
                    stepDisplay2.classList.remove('form-step-active')
                    stepDisplay3.classList.remove('form-step-active')

                    step1.classList.remove('d-none')
                    step2.classList.add('d-none')
                    step3.classList.add('d-none')

                    previous.classList.add('disabled')
                }
            })
        }
    </script>

    <div class="d-flex justify-content-start align-items-start"
         style="height: 100vh; width: 100vw; background: white">
        <div class="contact d-flex gap-4 shadow m-4">
            <div class="contact-image" style="background-image: url({{ asset('img/general/inschrijven.jpg') }})">
            </div>
            <div class="d-flex flex-column p-3 contact-text justify-content-center">
                <h1>Schrijf je in!</h1>

                <div class="d-flex flex-row justify-content-evenly align-items-start">
                    <div class="form-label">
                        <div id="step-display-1" class="form-step form-step-active">
                            <p>1</p>
                        </div>
                        <p>Algemene Gegevens</p>
                    </div>
                    <div class="form-line"></div>
                    <div class="form-label">
                        <div id="step-display-2" class="form-step"><p>2</p></div>
                        <p>Contact Gegevens</p>
                    </div>
                    <div class="form-line"></div>
                    <div class="form-label">
                        <div id="step-display-3" class="form-step"><p>3</p></div>
                        <p>Algemene Voorwaarden</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('inschrijven.submit') }}"
                      class="p-3 border-2 border-info-subtle d-flex flex-column"
                      style="border: solid; border-radius: 15px; justify-content: space-between">
                    @csrf

                    <div id="step1">
                        <h2>Algemene Gegevens</h2>
                        <div>
                            <label for="sex" class="col-md-4 col-form-label ">Geslacht <span
                                    class="text-danger">*</span></label>

                            <select id="sex" type="text" required
                                    class="w-100 form-select @error('sex') is-invalid @enderror"
                                    name="sex">
                                <option>Man</option>
                                <option>Vrouw</option>
                                <option>Anders</option>
                            </select>
                            @error('sex')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="d-flex flex-row-responsive gap-3 w-100" style="justify-content: space-between">
                            <div class="d-flex flex-column w-100">
                                <label for="name" class="col-md-4 col-form-label d-flex flex-row gap-1">Voornaam <span
                                        class="text-danger">*</span></label>

                                <input id="name" required type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex flex-column w-100">
                                <label for="infix" class="col-md-4 col-form-label ">Tussenvoegsel</label>

                                <input id="infix" type="text" class="form-control @error('infix') is-invalid @enderror"
                                       name="infix" value="{{ old('infix') }}" autocomplete="infix" autofocus>
                                @error('infix')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex flex-column w-100">
                                <label for="last_name" class="col-md-4 col-form-label d-flex flex-row gap-1">Achternaam
                                    <span class="text-danger">*</span></label>

                                <input id="last_name" required type="text"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       name="last_name" value="{{ old('last_name') }}" autocomplete="last_name"
                                       autofocus>
                                @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="birth_date" class="col-md-4 col-form-label ">Geboortedatum <span
                                    class="text-danger">*</span></label>
                            <input id="birth_date" required value="{{ old('birth_date') }}" type="date"
                                   class="form-control @error('birth_date') is-invalid @enderror" name="birth_date">
                            @error('birth_date')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div>
                            <label for="speltak" class="col-md-4 col-form-label ">Speltak <span
                                    class="text-danger">*</span></label>

                            <select id="speltak" type="text" required
                                    class="w-100 form-select @error('speltak') is-invalid @enderror"
                                    name="speltak">
                                <option value="dolfijnen">Dolfijnen (5 t/m 11)</option>
                                <option value="zeeverkenners">Zeeverkenners (11 t/m 17)</option>
                                <option value="loodsen">Loodsen (alleen als je Zeeverkenner bent geweest)</option>
                                <option value="afterloodsen">Afterloodsen (alleen als je Loods bent geweest)</option>
                            </select>
                            @error('speltak')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        @if ($errors->any())
                            <div class="text-danger">
                                <p>Er is iets misgegaan...</p>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>

                    <div id="step2" class="d-none">
                        <h2>Contact Gegevens</h2>
                        <div>
                            <label for="street" class="col-form-label ">Straat & huisnummer <span
                                    class="text-danger">*</span></label>
                            <input id="street" value="{{ old('street') }}" type="text" placeholder="Sluisweg 4" required
                                   class="form-control @error('street') is-invalid @enderror" name="street">
                            @error('street')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div>
                            <label for="postal_code" class="col-md-4 col-form-label ">Postcode <span
                                    class="text-danger">*</span></label>
                            <input id="postal_code" value="{{ old('postal_code') }}" type="text" placeholder="4758PT"
                                   required
                                   class="form-control @error('postal_code') is-invalid @enderror" name="postal_code">
                            @error('postal_code')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div>
                            <label for="city" class="col-md-4 col-form-label ">Woonplaats <span
                                    class="text-danger">*</span></label>
                            <input id="city" value="{{ old('city') }}" type="text" placeholder="Moerdijk" required
                                   class="form-control @error('city') is-invalid @enderror" name="city">
                            @error('city')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="col-md-4 col-form-label ">E-mail <span
                                    class="text-danger">*</span></label>
                            <input id="email" value="{{ old('email') }}" type="email" required
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   autocomplete="email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="col-md-4 col-form-label ">Telefoonnummer <span
                                    class="text-danger">*</span></label>
                            <input id="phone" value="{{ old('phone') }}" type="text" required
                                   class="form-control @error('phone') is-invalid @enderror" name="phone">
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div id="step3" class="d-none">
                        <h2>Algemene voorwaarden</h2>

                        <div class="d-flex flex-row-responsive gap-3 w-100 align-items-end" style="justify-content: space-between">
                            <div class="d-flex flex-column w-100">
                                <label for="new_password" class="col-md-4 col-form-label ">Wachtwoord <small>Minstens 8 tekens</small></label>
                                <input name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password">
                                @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex flex-column w-100">
                                <label for="new_password_confirmation" class="col-md-4 col-form-label ">Herhaal wachtwoord</label>
                                <input name="new_password_confirmation" type="password" class="form-control" id="new_password_confirmation">
                                @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mt-4">
                            <p class="m-0">Akkoord met de voorwaarden <span class="text-danger">*</span></p>
                            <input class="form-check-input mt-0" type="checkbox" value="1" id="voorwaarden" name="voorwaarden" required>
                            <label class="form-check-label" for="voorwaarden">
                                Ik ga akkoord met de <a href="https://waterscoutingmhg.nl/over-onze-club/regels-wetgeving-en-privacybeleid/gedragscode/">gedragsregels</a>
                                en de <a href="https://waterscoutingmhg.nl/over-onze-club/regels-wetgeving-en-privacybeleid/privacyverklaring/">privacyverklaring</a>
                                en heb deze gelezen.
                            </label>
                        </div>

                        <div class="form-check mt-4">
                            <p class="m-0">Avg wetgeving</p>
                            <input class="form-check-input" type="checkbox" value="0" id="avg" name="avg" checked>
                            <label class="form-check-label" for="avg">
                                Ik geef toestemming om foto's en video's waar ik op sta te gebruiken in online
                                materiaal, zoals Facebook of de website
                            </label>
                        </div>


                        <div class="d-flex flex-row flex-wrap gap-2 mt-4">
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
                                class="btn btn-primary text-white flex flex-row align-items-center justify-content-center">
                                <span class="button-text">Opslaan</span>
                                <span style="display: none" class="loading-spinner spinner-border spinner-border-sm" aria-hidden="true"></span>
                                <span style="display: none" class="loading-text" role="status">Laden...</span>
                            </button>
                        </div>
                    </div>


                    <div class="mt-4 d-flex flex-row justify-content-evenly">
                        <a id="previous" class="btn btn-outline-primary disabled">Vorige</a>
                        <a id="next" class="btn btn-outline-primary">Volgende</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
