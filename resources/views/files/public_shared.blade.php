@extends('layouts.files')

@section('content')
    <div class="container py-4">
        @php
            // Haal de ID op van de huidige map binnen de publieke weergave
            $folderId = $currentFolder ? $currentFolder->id : null;

            // Bepaal of de gebruiker bestanden mag toevoegen/aanpassen op basis van de link
            $isAdmin = $sharedRoot->share_permission === 'write';
        @endphp

        <x-file-manager
            :files="$files"
            :breadcrumbs="$breadcrumbs"
            :folderId="$folderId"
            :is-admin="$isAdmin"
            :hasAdminViewers="false"
            :admin-name="'Administratie'"
            :non-admin-name="'Kijker'"
            :storageUrl="\Illuminate\Support\Facades\Storage::url('')"
            :location="$sharedRoot->location ?? 'Admin'"
            :location-id="$sharedRoot->location_id ?? 0"
            :is-public="true"
            :share-hash="$hash"
        />
    </div>
@endsection
