@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Logs</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin')}}">Administratie</a></li>
                <li class="breadcrumb-item active" aria-current="page">Logs</li>
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

        <form id="auto-submit" method="GET">
            <div class="d-flex">
                <div class="d-flex flex-row-responsive gap-2 align-items-center mb-3 w-100">
                    <div class="input-group">
                        <label for="search" class="input-group-text" id="basic-addon1">
                            <span class="material-symbols-rounded">search</span></label>
                        <input id="search" name="search" type="text" class="form-control"
                               placeholder="Zoeken op actie, locatie of opmerking."
                               aria-label="Zoeken" aria-describedby="basic-addon1" value="{{ $search }}" onchange="this.form.submit();">
                    </div>
                    <div class="input-group">
                        <label for="user" class="input-group-text" id="basic-addon1">
                            <span class="material-symbols-rounded">person</span></label>
                        <input id="user" name="user" type="number" class="form-control"
                               placeholder="Zoeken op gebruikers id"
                               aria-label="user" aria-describedby="basic-addon1" value="{{ $search_user }}" onchange="this.form.submit();">
                    </div>
                </div>
            </div>
        </form>

        @if($logs->count() > 0)
        <div class="overflow-scroll no-scrolbar mw-100">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Gebruiker</th>
                    <th scope="col">Actie</th>
                    <th scope="col">Referentie</th>
                    <th scope="col">Locatie</th>
                    <th scope="col">Opmerkingen</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr class="@if($log->action === 0) table-danger @endif @if($log->action === 1) table-warning @endif">
                        <th scope="row">{{ $log->id }}</th>
                        <td>
                            <a target="_blank"
                               href="{{ route('admin.account-management.details', ['id' => $log->user->id]) }}">
                                {{ $log->user->name.' '.$log->user->infic.' '.$log->user->last_name }}
                            </a>
                        </td>
                        <td>{{ $log->type }}</td>
                        <td>{{ $log->reference }}</td>
                        <td>{{ $log->location }}</td>
                        <td>{{ $log->display_text }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @else
            <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                <span class="material-symbols-rounded me-2">folder_off</span>Geen logs gevonden...
            </div>
        @endif
    </div>
@endsection
