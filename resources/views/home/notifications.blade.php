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

        <div class="d-flex flex-column gap-3 justify-content-center">
            @foreach($notifications as $notification)
                <div
                    class="notification w-100 @if($notification->seen === 0) bg-secondary-subtle @else bg-dark-subtle @endif">
                    <div class="h-100 d-flex align-items-center justify-content-center">
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

        <div class="mt-3">
            {{$notifications->links()}}
        </div>
    </div>
@endsection
