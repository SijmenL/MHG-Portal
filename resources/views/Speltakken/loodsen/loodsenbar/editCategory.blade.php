@extends('layouts.loodsen')

@section('content')

{{-- tijdelijk even style hier voor makkelijkheid --}}
<style>


</style>


    <div class="loodsenbar-background">
        <div class="py-4 container col-md-11">
            <h1>Loodsenbar</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.loodsenbar') }}">Loodsenbar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsenbar.menage.products') }}">Menage products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit category</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
                <div class="w-100">
                    <h2 class="text-center">Edit category</h2>
                    <form action="{{ route('loodsenbar.edit.category', ['id' => $category->id]) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required value="{{ $category->name }}">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $category->description }}</textarea>
                        </div>
                        {{-- upload image disabled for now --}}
                        <div class="form-group d-none">
                            <label for="image">Image</label>
                            <input type="file" class="form-control-file" id="image" name="image" disabled>
                        </div>

                        <button type="submit" class="btn btn-primary my-4">Save</button>
                        <a href="{{ route('loodsenbar.menage.products') }}" class="btn btn-danger my-4">Cancel</a>
                </div>
            </div>
        </div>
    </div>
@endsection