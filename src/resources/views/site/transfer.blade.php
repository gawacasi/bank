@extends('layouts.main_layout')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    @if (session('passwordError'))
                        <div class="text-center alert alert-danger">
                            {{ session('passwordError') }}
                        </div>
                    @endif

                    @if (session('amountError'))
                        <div class="text-center alert alert-danger">
                            {{ session('amountError') }}
                        </div>
                    @endif

                    <h5 class="mb-3 text-center">Transferencia</h5>

                    <div class="text-center mb-4">
                        <small class="text-muted">Saldo atual</small>
                        <div class="h4 fw-bold">R$ {{ $wallet['balance'] }}</div>
                    </div>

                    <form method="POST" action="/transferSub/{{ Crypt::encrypt($wallet['user_id']) }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Valor do depósito</label>
                            <input name="amount" type="number" step="0.01" min="0.01" class="form-control"
                                placeholder="0,00" required value="{{ old('amount') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email do destinatario</label>
                            <input name="destinatario_email" type="text" class="form-control"
                                placeholder="destinatario@email.com" required value="{{ old('destinatario_email') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirme sua senha</label>
                            <input name="password" type="password" class="form-control" placeholder="Senha" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Confirmar Transferencia</button>
                            <a href="{{ route('home') }}" class="btn btn-outline-danger">Voltar</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
