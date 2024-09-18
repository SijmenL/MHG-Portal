@extends('layouts.app')

@php
    use Carbon\Carbon;
    Carbon::setLocale('nl');
@endphp

@section('content')
    <div class="container col-md-11">
        <h1>Notificaties</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notificaties</li>
            </ol>
        </nav>

        <div class="bg-light rounded-2 p-3">
            <h2>Notificaties</h2>
            @if($notifications->count() > 0)
            <div class="settings-container">
                <div class="d-flex flex-column gap-3 justify-content-center">
                    @foreach($notifications as $notification)
                        <div
                            class="notification w-100 @if($notification->seen === 0) bg-secondary-subtle @else bg-dark-subtle @endif">
                            <div
                                class="d-flex align-items-end justify-content-center flex-row-responsive gap-2"
                                style="height: 50px">
                                @if($notification->location !== null)

                                    <div class="notification-icon">
                                        @if($notification->location === 'dolfijnen')
                                            <img class="w-100" alt="dolfijnen"
                                                 src="{{ asset('img/icons/dolfijnen.png') }}">
                                        @endif
                                        @if($notification->location === 'zeeverkenners')
                                            <img class="w-100" alt="zeeverkenners"
                                                 src="{{ asset('img/icons/zeeverkenners.png') }}">
                                        @endif
                                        @if($notification->location === 'loodsen')
                                            <img class="w-100" alt="loodsen" src="{{ asset('img/icons/loodsen.png') }}">
                                        @endif
                                        @if($notification->location === 'afterloodsen')
                                            <img class="w-100" alt="afterloodsen"
                                                 src="{{ asset('img/icons/afterloodsen.png') }}">
                                        @endif
                                        @if($notification->location === 'leiding')
                                            <span
                                                class="material-symbols-rounded notification-icon">supervisor_account</span>
                                        @endif
                                        @if(!$notification->sender)
                                            <span
                                                class="material-symbols-rounded notification-icon">instant_mix</span>
                                        @endif
                                    </div>

                                    <div class="notification-profile-picture">
                                        @if($notification->sender)
                                            @if($notification->sender->profile_picture)
                                                <img alt="profielfoto" class="profle-picture"
                                                     src="{{ asset('/profile_pictures/' .$notification->sender->profile_picture) }}">
                                            @else
                                                <img alt="profielfoto" class="profle-picture"
                                                     src="{{ asset('img/no_profile_picture.webp') }}">
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    @if($notification->sender)
                                        @if($notification->sender->profile_picture)
                                            <img alt="profielfoto" class="profle-picture"
                                                 src="{{ asset('/profile_pictures/' .$notification->sender->profile_picture) }}">
                                        @else
                                            <img alt="profielfoto" class="profle-picture"
                                                 src="{{ asset('img/no_profile_picture.webp') }}">
                                        @endif
                                    @endif
                                @endif
                            </div>


                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <div class="d-flex flex-column">
                                    <div class="d-flex flex-row-responsive" style="margin-bottom: -15px;">
                                        <p>
                                            @if($notification->sender)
                                                @if(auth()->user()->roles->contains('role', 'Dolfijn'))
                                                    <span class="fw-bold">
                                                {{ $notification->sender->dolfijnen_name }}
                                    </span>
                                                @else
                                                    <span class="fw-bold">
                                                {{ $notification->sender->name.' '.$notification->sender->infix.' '.$notification->sender->last_name }}
                                    </span>
                                                @endif
                                            @else
                                                <span class="fw-bold">Systeem</span>
                                            @endif
                                        </p>
                                        <p class="ms-1 comment-time">
                                            <span>{{ Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                        </p>
                                    </div>
                                    <span>{{ $notification->display_text }}</span>
                                </div>

                                @if($notification->link)
                                    <a class="btn btn-dark" href="{{ $notification->link }}"><span
                                            class="material-symbols-rounded">open_in_new</span></a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <span class="material-symbols-rounded me-2">notifications_off</span>Geen notificaties...
                    </div>
                @endif
            </div>

            <div class="mt-3">
                {{$notifications->links()}}
            </div>
        </div>
@endsection
