@extends('layouts.loodsen')

@section('content')

{{-- tijdelijk even style hier voor makkelijkheid --}}
<style>
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


    <div class="flunky-background">
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

           <p></p>
        </div>
        <div class="container">
            <div class="row height d-flex justify-content-center align-items-center">
                <div class="col-md-6">
                    <div class="form-search">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" class="form-control form-input" placeholder="Search...">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
