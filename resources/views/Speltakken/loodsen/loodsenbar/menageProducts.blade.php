@extends('layouts.loodsen')

@section('content')

{{-- tijdelijk even style hier voor makkelijkheid --}}
<style>
    .product-edit-card
    {
        width: 40vw !important;
    }

    .cursor-pointer
    {
        cursor: pointer;
    }

</style>


    <div class="loodsenbar-background">
        <div class="py-4 container col-md-11">
            <h1>Loodsenbar</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loodsen.loodsenbar') }}">Loodsenbar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Menage products</li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex flex-row-responsive justify-content-center align-items-center gap-5">
                <div class="w-100">
                    {{-- actions --}}
                    <div class="">
                        <h2 class="text-center">Actions</h2>
                        <div class="quick-action-bar">
                            <a class="btn btn-info quick-action" href="{{ route('loodsenbar.view.add.product') }}">
                                <span class="material-symbols-rounded">add_box</span>
                                <p>Add product</p>
                            </a>
                            <a class="btn btn-info quick-action" href="{{ route('loodsenbar.view.add.category') }}">
                                <span class="material-symbols-rounded">add_box</span>
                                <p>Add category</p>
                            </a>
                        </div>
                        <hr>
                    </div>
                     {{-- test producten --}}
                     <h2 class="text-center">Products</h2>
                     <div class="d-flex align-items-center flex-wrap justify-content-start">
                         @for ($x = 1; $x <= 8; $x+=1) 
                         <div class="p-1">
                             <div class="card product-edit-card">
                                 <div class="card-body">
                                     <h5 class="card-title">Product {{ $x }}</h5>
                                     <p class="card-text">Product {{ $x }} description</p>
                                     <div class="d-flex justify-content-between">
                                        <span class="material-symbols-rounded text-warning cursor-pointer">edit</span>
                                        <span class="material-symbols-rounded text-danger cursor-pointer">delete</span>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         @endfor
                     </div>
                     <hr>
                </div>
            </div>
        </div>
    </div>
@endsection
