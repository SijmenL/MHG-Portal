@extends('layouts.app')

@section('content')
    <div class="container col-md-11">
        <div class="d-flex flex-row align-items-center justify-content-between">
            <h1>Bestanden</h1>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{route('admin')}}">Administratie</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bestanden</li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <x-file-manager
            :files="$files"
            :breadcrumbs="$breadcrumbs"
            :folderId="$folderId"
            :hasAdminViewers="false"
            :is-admin="$isAdmin"
            :admin-name="'Administratie'"
            :non-admin-name="'Administratie'"
            :storageUrl="Storage::url('')"
            :location="'Admin'"
            :location-id="0"
        />
@endsection
