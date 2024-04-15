@extends('layouts.loodsen')

@section('content')

{{-- tijdelijk even style hier voor makkelijkheid --}}
<style>
    .product-card
    {
        width: 40vw !important;
        -webkit-user-select: none; /* Safari */
        -ms-user-select: none; /* IE 10 and IE 11 */
        user-select: none; /* Standard syntax */
    }

    .product-card > .card-body > p.card-text
    {
        margin-bottom: 0px;
    }

    a.card-link
    {
        text-decoration: none;
    }

    .counter-btns
    {
        visibility: hidden;
    }

    .btn-choose-users
    {
        display: flex;
        gap: 10px;
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
                                {{-- <a class="btn btn-info quick-action" href="{{ route('loodsenbar.orders') }}"> --}}
                                    <span class="material-symbols-rounded">inventory_2</span>
                                    <p>Vooraad</p>
                                </a>
                            <a class="btn btn-info quick-action" >
                            {{-- <a class="btn btn-info quick-action" href="{{ route('loodsenbar.devices') }}"> --}}
                                <span class="material-symbols-rounded">smartphone</span>
                                <p>Devices</p>
                            </a>
                            <a class="btn btn-info quick-action" >
                            {{-- <a class="btn btn-info quick-action" href="{{ route('loodsenbar.check-out') }}"> --}}
                                <span class="material-symbols-rounded">Point_Of_Sale</span>
                                <p>Check-out orders</p>
                            </a>
                            <a class="btn btn-info quick-action" >
                            {{-- <a class="btn btn-info quick-action" href="{{ route('loodsenbar.check-out') }}"> --}}
                                <span class="material-symbols-rounded">settings</span>
                                <p>Settings</p>
                            </a>
                        </div>
                    </div>
                    @endif
                    {{-- choose users button --}}
                    <div class="counter-btns">
                        <div class="btn-choose-users w-100 mt-4">
                            <button type="button" class="btn btn-warning w-50 me-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                Kies gebruikers
                            </button>
                            <button class="btn btn-success w-50" type="button" href="#">
                                Alleen ik
                            </button>
                            {{-- undo button --}}
                        </div>
                        <button class="btn btn-danger w-100 mt-2" type="button" onclick="resetCount()">
                            Anuleer
                        </button>
                    </div>
                    <hr>
                    <div id="loodsenbarHomeItems" class="mb-5">
                        {{-- snel lopende producten --}}
                        <h2 class="text-center">Vaak besteld</h2>
                        <div class="d-flex align-items-center flex-wrap justify-content-center">
                            @php if(count($products) == 0){print "No products found";} @endphp
                            @foreach ($products as $product) 
                            <a onclick="addProduct({{ $product->id }})" class="card-link">
                                <div class="p-1">
                                    <div class="card product-card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $product->name }}</h5>
                                            <p class="card-text">Categorie: {{ $product->category->name }}</p>
                                            <p class="card-text">{{ $product->description }}</p>
                                            <p class="card-text">€ {{ $product->price }}</p>
                                        </div>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none" product-id="{{ $product->id }}" data-count="0">
                                            0
                                            <span class="visually-hidden">selected items</span>
                                          </span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <hr>
                        {{-- Categorien --}}
                        <h2 class="text-center">Categorieën</h2>
                        <div class="d-flex align-items-center flex-wrap justify-content-center">
                            @php if(count($categories) == 0) {print "No categories found";} @endphp
                            @foreach ($categories as $category)
                            <a href="{{ route('loodsenbar.products', ['id' => $category->id]) }}" class="card-link">
                                <div class="p-1">
                                    <div class="card product-card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $category->name }}</h5>
                                            <p class="card-text">{{ $category->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ...
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Understood</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        function addProduct(id)
        {
            let badge = document.querySelector(`span[product-id="${id}"]`);
            let count = parseInt(badge.getAttribute('data-count'));
            count++;
            badge.setAttribute('data-count', count);
            badge.classList.remove('d-none');
            badge.innerHTML = count;

            // if count is > 0 show button
            let button = document.querySelector('.counter-btns');
            if(count > 0)
            {
                button.style.visibility = 'visible';
            }else{
                button.style.visibility = 'hidden';
            }

        }

        function resetCount()
        {
            // reset all counts
            let badges = document.querySelectorAll('span[product-id]');
            badges.forEach(badge => {
                badge.setAttribute('data-count', 0);
                badge.classList.add('d-none');
            });

            // hide button
            let button = document.querySelector('.counter-btns');
            button.style.visibility = 'hidden';
        }

    </script>
@endsection
