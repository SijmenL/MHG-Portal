@extends('layouts.app')

@include('partials.editor')

@vite('resources/js/calendar.js')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="header" style="background-image: url({{ asset('files/agenda/banner.jpg') }})">
        <div>
            <p class="header-title">Agenda</p>
        </div>
    </div>
    <div class="container col-md-11">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-row-responsive align-items-center gap-5" style="width: 100%">
            <div class="" style="width: 100%;">
                <h1 class="">Agenda</h1>

                <p>Welkom in de MHG agenda! Omdat je leiding of vrijwilliger bent kun je activiteiten aanmaken en bewerken!</p>

                <div class="bg-light rounded-2 p-3">
                    <h2>Acties</h2>
                    <div class="quick-action-bar">
                        <a class="btn btn-info quick-action" href="{{ route('agenda.month') }}">
                            <span class="material-symbols-rounded">event_upcoming</span>
                            <p>Alle activiteiten</p>
                        </a>
                        <a class="btn btn-info quick-action" href="{{ route('agenda.new') }}">
                            <span class="material-symbols-rounded">calendar_add_on</span>
                            <p>Nieuwe activiteit</p>
                        </a>
                        <a class="btn btn-secondary quick-action" href="{{ route('agenda.edit') }}">
                            <span class="material-symbols-rounded">edit_calendar</span>
                            <p>Activiteiten bewerken</p>
                        </a>
                        <a class="btn btn-secondary quick-action" href="{{ route('agenda.presence') }}">
                            <span class="material-symbols-rounded">perm_contact_calendar</span>
                            <p>Inschrijvingen en aanwezigheid</p>
                        </a>
                    </div>
                </div>

            </div>
        </div>
@endsection
