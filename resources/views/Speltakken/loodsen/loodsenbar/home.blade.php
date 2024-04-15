@extends('layouts.loodsen')

@section('content')

{{-- tijdelijk even style hier voor makkelijkheid --}}
<style>
    .product-card
    {
        width: 40vw !important;
    }

</style>


    <div class="loodsenbar-background">
        <div class="py-4 container col-md-11">
            <h1>Loodsenbar</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Loodsenbar</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
                <div class="w-100">
                    {{-- Beheerder acties --}}
                    @if(auth()->user() && (auth()->user()->roles->contains('role', 'Loodsen Stamoudste') || auth()->user()->roles->contains('role', 'Administratie')))
                    <div class="">
                        <h2 class="text-center">Admin actions</h2>
                        <div class="quick-action-bar">
                            {{-- <a class="btn btn-info quick-action" > --}}
                            <a class="btn btn-info quick-action" href="{{ route('loodsenbar.menage.products') }}">
                                <span class="material-symbols-rounded">Category</span>
                                <p>Products</p>
                            </a>
                            <a class="btn btn-info quick-action" >
                            {{-- <a class="btn btn-info quick-action" href="{{ route('loodsenbar.orders') }}"> --}}
                                <span class="material-symbols-rounded">Summarize</span>
                                <p>Orders</p>
                            </a>
                            <a class="btn btn-info quick-action" >
                            {{-- <a class="btn btn-info quick-action" href="{{ route('loodsenbar.devices') }}"> --}}
                                <span class="material-symbols-rounded">smartphone</span>
                                <p>Devices</p>
                            </a>
                            <a class="btn btn-info quick-action" >
                            {{-- <a class="btn btn-info quick-action" href="{{ route('loodsenbar.check-out') }}"> --}}
                                <span class="material-symbols-rounded">Point_Of_Sale</span>
                                <p>Check-out</p>
                            </a>
                        </div>
                        <hr>
                    </div>
                    @endif
                    {{-- snel lopende producten --}}
                    <h2 class="text-center">Fast moving products</h2>
                    <div class="d-flex align-items-center flex-wrap justify-content-center">
                        @for ($x = 1; $x <= 8; $x+=1) 
                        <div class="p-1">
                            <div class="card product-card">
                                <div class="card-body">
                                    <h5 class="card-title">Product {{ $x }}</h5>
                                    <p class="card-text">Product {{ $x }} description</p>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    <hr>
                    {{-- Categorien --}}
                    <h2 class="text-center">Categories</h2>
                    <div class="d-flex align-items-center flex-wrap justify-content-center">
                        @php if(count($categories) == 0) {print "No categories found";} @endphp
                        @foreach ($categories as $category)
                        <div class="p-1">
                            <div class="card product-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $category->name }}</h5>
                                    <p class="card-text">{{ $category->description }} description</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
