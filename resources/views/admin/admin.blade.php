@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <h1>Administratie</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Administratie</li>
            </ol>
        </nav>

        <div class="bg-light rounded-2 p-3">
            <h2>Admin Acties</h2>
            <div class="quick-action-bar">


                <a class="btn btn-admin quick-action" href="{{ route('admin.account-management') }}">
                    <span class="material-symbols-rounded">manage_accounts</span>
                    <p>Gebruikers</p>
                </a>
                <a class="btn btn-admin quick-action" href="{{ route('admin.create-account') }}">
                    <span class="material-symbols-rounded">person_add</span>
                    <p>Maak account</p>
                </a>
                <a class="btn btn-admin quick-action" href="{{ route('admin.role-management') }}">
                    <span class="material-symbols-rounded">account_circle</span>
                    <p>Rollen beheer</p>
                </a>
                <a class="btn btn-admin quick-action" href="{{ route('admin.forum-management.posts') }}">
                    <span class="material-symbols-rounded">forum</span>
                    <p>Prikbord beheer</p>
                </a>

                <a class="btn btn-admin quick-action" href="{{ route('admin.logs') }}">
                    <span class="material-symbols-rounded">topic</span>
                    <p>Logs</p>
                </a>
            </div>
        </div>

    </div>
@endsection
