@extends('layouts.contact')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh; width: 100vw; background: white">
        <div class="contact d-flex gap-4 shadow m-4">
            <div class="contact-image" style="background-image: url({{ asset('img/general/DSC_1785.JPG') }})">
            </div>
            <div class="d-flex flex-column p-3 contact-text justify-content-center">
                <h1>Contact</h1>
                <p>Neem contact op met de Matthijs Heldt Groep!</p>

                <form method="POST" action="{{ route('contact.submit') }}" class="p-3 border-2 border-info-subtle"
                      style="border: solid; border-radius: 15px;">
                    @csrf

                    <div class="row mb-3">
                        <label for="name" class="col-md-4 col-form-label text-md-end">Naam <span class="text-danger">*</span></label>

                        <div class="col-md-7">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="email" class="col-md-4 col-form-label text-md-end">E-mail <span class="text-danger">*</span></label>

                        <div class="col-md-7">
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
                        <label for="phone" class="col-md-4 col-form-label text-md-end">Telefoonnummer</label>

                        <div class="col-md-7">
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone') }}" autocomplete="phone" autofocus>

                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="message" class="col-md-4 col-form-label text-md-end">Bericht <span class="text-danger">*</span></label>

                        <div class="col-md-7">
                      <textarea id="message" name="message" autofocus required rows="2" class="form-control" style="max-height: 250px">{{ old('message') }}</textarea>

                            @error('message')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>


                    <div class="mb-0">
                        <div class="d-flex flex-row-responsive gap-2 justify-content-center">
                            <button type="submit" class="btn btn-primary text-white">
                                Versturen!
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
