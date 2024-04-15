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
                    <li class="breadcrumb-item active" aria-current="page">Add Product</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
                <div class="w-100">
                    
                </div>
            </div>
        </div>
    </div>
@endsection
