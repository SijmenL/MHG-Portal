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

    .form-search{
        position: relative;
    }

    .form-search .material-symbols-rounded{
        position: absolute;
        top:15px;
        left: 15px;
        color: #9ca3af;
    }

    .left-pan{
        padding-left: 7px;
    }

    .left-pan i{
        padding-left: 10px;
    }

    .form-input{
        height: 55px;
        text-indent: 33px;
        border-radius: 10px;
    }

    .form-input:focus{
        box-shadow: none;
        border:none;
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
            <div class="alert alert-danger justify-content-between">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session()->has('message'))
                <div class="alert alert-info d-flex justify-content-between">
                    {{ session()->get('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    <div class="container mb-3">
                        <div class="row height d-flex justify-content-center align-items-center">
                            <div class="col-md-6">
                                <div class="form-search">
                                    <span class="material-symbols-rounded">search</span>
                                    <input type="text" class="form-control form-input product-search" placeholder="Search..."  onkeyup="searchProducts()">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="d-flex align-items-center flex-wrap justify-content-center">
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
                     {{-- test categories --}}
                     <h2 class="text-center">Categories</h2>
                     <div class="d-flex align-items-center flex-wrap justify-content-center">
                        @php if(count($categories) == 0){print "First add categories!";} @endphp
                        <div class="d-flex align-items-center flex-wrap justify-content-center">
                            @foreach ($categories as $category)
                            <div class="p-1">
                                <div class="card product-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $category->name }}</h5>
                                        <p class="card-text">{{ $category->description }}</p>
                                        <div class="d-flex justify-content-between">
                                            <span class="material-symbols-rounded text-warning cursor-pointer">edit</span>
                                            <span class="material-symbols-rounded text-danger cursor-pointer">delete</span>
                                         </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                     </div>
                     <hr>
                </div>
            </div>
        </div>
    </div>


    <script>
        function searchProducts()
        {
            var input, filter, cards, cardContainer, title, i;
            input = document.getElementsByClassName('product-search')[0];
            filter = input.value.toUpperCase();
            cardContainer = document.getElementsByClassName('product-edit-card');
            for (i = 0; i < cardContainer.length; i++) {
                title = cardContainer[i].querySelector(".card-title");
                if (title.innerText.toUpperCase().indexOf(filter) > -1) {
                    cardContainer[i].style.display = "";
                } else {
                    cardContainer[i].style.display = "none";
                }
            }
        }
    </script>
@endsection