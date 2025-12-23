@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Exportar transações em PDF</h2>
        <p>Carteira: {{ $wallet['id'] }} - Saldo: {{ $wallet['balance'] }}</p>

        <a href="{{ route('exportPdf.download') }}" class="btn btn-primary">Baixar PDF</a>
        <a href="{{ route('exportCsv.download') }}" class="btn btn-secondary">Baixar CSV</a>

        <hr />
        <h4>Transações recentes</h4>
        <ul>
            @foreach ($transactions as $t)
                <li>{{ $t->created_at }} — {{ $t->type }} — R$ {{ number_format($t->amount, 2) }}</li>
            @endforeach
        </ul>
    </div>
@endsection
