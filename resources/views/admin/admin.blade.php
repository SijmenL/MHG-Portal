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
        <div class="bg-light rounded-2 p-3">
            <h2>Content</h2>
            <div class="quick-action-bar">
                <a class="btn btn-info quick-action" href="{{ route('admin.contact') }}">
                    <div style="margin-bottom: -10px; position: relative">
                        <span class="material-symbols-rounded">call</span>
                        @if($contact >= 1)
                            <pre style="position: absolute"
                                 class="badge badge-pill bg-danger dashboard-notification">{{ $contact }}</pre>
                        @endif
                    </div>
                    <p>Contact</p>
                </a>

                <a class="btn btn-info quick-action" href="{{ route('admin.signup') }}">
                    <div style="margin-bottom: -10px; position: relative">
                        <span class="material-symbols-rounded">app_registration</span>
                        @if($signup >= 1)
                            <pre style="position: absolute"
                                 class="badge badge-pill bg-danger dashboard-notification">{{ $signup }}</pre>
                        @endif
                    </div>
                    <p>Inschrijvingen</p>
                </a>

                <a class="btn btn-info quick-action" href="{{ route('admin.news') }}">
                    <div style="margin-bottom: -10px; position: relative">
                        <span class="material-symbols-rounded">newspaper</span>
                        @if($news >= 1)
                            <pre style="position: absolute"
                                 class="badge badge-pill bg-danger dashboard-notification">{{ $news }}</pre>
                        @endif
                    </div>
                    <p>Nieuwsitems</p>
                </a>
            </div>
        </div>


        <div class="bg-light rounded-2 p-3 mt-4">
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

        <div class="bg-light rounded-2 p-3 mt-4">
            <h2>Debug</h2>
            <div class="quick-action-bar">
                <a class="btn btn-secondary quick-action" href="{{ route('admin.debug.mail') }}">
                    <span class="material-symbols-rounded">email</span>
                    <p>Mail</p>
                </a>
            </div>
        </div>

    </div>
@endsection
