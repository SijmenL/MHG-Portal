@extends('layouts.loodsen')

@section('content')

{{-- tijdelijk even style hier voor makkelijkheid --}}
<style>
    .product-card
    {
        width: 40vw !important;
    }

    a.card-link
    {
        text-decoration: none;
    
    }

</style>


    <div class="loodsenbar-background">
        <div class="py-4 container col-md-11">
            <h1>Loodsenbar</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.loodsenbar') }}">Loodsenbar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
                <div class="w-100">
                    <div id="loodsenbarHomeItems">
                        {{-- snel lopende producten --}}
                        <h2 class="text-center">Products of category {{ $category->name }}</h2>
                        <hr>
                        <div class="d-flex align-items-center flex-wrap justify-content-center">
                            @php if(count($products) == 0){print "No products found";} @endphp
                            @foreach ($products as $product) 
                            <div class="p-1">
                                <div class="card product-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text">category: {{ $product->category->name }}</p>
                                        <p class="card-text">{{ $product->description }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
