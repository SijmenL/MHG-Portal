@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Details @if($account !== null)
                {{$account->name}} {{$account->infix}} {{$account->last_name}}
            @endif</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin')}}">Administratie</a></li>
                <li class="breadcrumb-item" aria-current="page"><a
                        href="{{route('admin.account-management')}}">Leden</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details @if($account !== null)
                        {{$account->name}} {{$account->infix}} {{$account->last_name}}
                    @endif</li>
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

        @if($account !== null)
            @if($account->is_associate == 1)
                <div class="alert alert-primary"> {{$account->name}} staat in het systeem als relatie.
                </div>
            @endif

            @if($account->member_date_end !== null && $account->accepted == false)
                    <div class="alert alert-primary"> {{$account->name}} is uitgeschreven.</div>
            @endif

            <x-user_details
                :hide="[]"
                :user="$account"
            />

        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">person_off</span>Geen account gevonden...
            </div>
        @endif

        <div class="d-flex flex-row flex-wrap gap-2">
            <a href="{{ route('admin.account-management') }}" class="btn btn-info">Terug</a>
            @if($account !== null && !$user->roles->contains('role', 'Gegevensadministratie'))
                <a href="{{ route('admin.account-management.edit', ['id' => $account->id]) }}"
                   class="btn btn-dark">Bewerk</a>
            @endif
        </div>
    </div>
@endsection
