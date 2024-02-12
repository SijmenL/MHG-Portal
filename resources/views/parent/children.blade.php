@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Mijn Kinderen</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mijn Kinderen</li>
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

        <div class="d-flex flex-row justify-content-center gap-4 flex-wrap">
            @foreach($children as $child)
                <div class="card">
                    @if($child->profile_picture)
                        <img alt="foto kind" class="card-img-top"
                             src="{{ asset('/profile_pictures/' . $child->profile_picture) }}">
                    @else
                        <img alt="foto kind" class="card-img-top"
                             src="{{ asset('img/no_profile_picture.webp') }}">
                    @endif
                    <div class="card-body">
                        <h2 class="card-title">{{ $child->name.' '.$child->infix.' '.$child->last_name }}</h2>

                        <div class="d-flex flex-row gap-1 flex-wrap">
                            @foreach ($child->roles as $role)
                                <span title="{{ $role->description }}"
                                      class="badge rounded-pill text-bg-primary text-white fs-6 p-2">{{ $role->role }}</span>
                            @endforeach
                        </div>
                    </div>
                        <div class="mt-4 card-footer d-flex gap-2 flex-column">
{{--                            <a class="btn btn-info">Bekijk details</a>--}}
                            <a class="btn btn-outline-dark" href="{{ route('children.edit', ['id' => $child->id]) }}">Bewerk persoonlijke informatie</a>
                        </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
