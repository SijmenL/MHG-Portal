@extends('layouts.contact')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh; width: 100vw; background: white">
        <div class="contact d-flex gap-4 shadow m-4">
            <div class="contact-image" style="background-image: url({{ asset('img/general/DSC_1785.JPG') }})">
            </div>
            <div class="d-flex flex-column p-3 contact-text justify-content-center">
                <h1>Contact</h1>
                <p>Neem contact op met de Matthijs Heldt Groep!</p>
                    <div class="p-3 border-2 border-info-subtle" style="border: solid; border-radius: 15px;">
                        <p>Bedankt voor het insturen! We komen zo snel mogelijk bij je terug!</p>
                    </div>
            </div>
        </div>
    </div>
@endsection
