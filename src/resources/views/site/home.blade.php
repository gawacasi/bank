@extends('layouts.main_layout')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">
                <div class="row mb-3 align-items-center">
                    @include('partials.header_bar')
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{route("deposit")}}" class="btn btn-warning px-3 ms-3">
                            <i class="fa-solid fa-piggy-bank me-2"></i>Depositar Saldo
                        </a>
                        <a href="{{route("transfer")}}" class="btn btn-primary px-3 ms-3">
                            <i class="fa-solid fa-money-bill-transfer me-2"></i>Enviar Pix
                        </a>
                        <a href="{{ route('exportTransfers.download') }}" class="btn btn-success px-3 ms-3">
                            <i class="fa-solid fa-file-csv me-2"></i>Baixar CSV (Minhas Transferências)
                        </a>
                    </div>
                    <div class="row">

                    </div>
                    @include('partials.transactions')
                </div>
            </div>
        @endsection
