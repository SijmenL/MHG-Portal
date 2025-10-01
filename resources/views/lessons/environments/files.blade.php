@extends('layouts.lessons')

@section('content')
    <div class="container col-md-11">
        <div class="d-flex flex-row align-items-center justify-content-between">
            <h1>Bestanden</h1>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
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
                :is-admin="$isTeacher"
                :hasAdminViewers="true"
                :admin-name="'Praktijkbegeleiders'"
                :non-admin-name="'Deelnemers'"
                :storageUrl="Storage::url('')"
                :location="'Lesson'"
                :location-id="$lesson->id"
            />
@endsection
