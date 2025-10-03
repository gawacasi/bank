<div class="row">
    @php
        $myWalletId = $wallet['id'] ?? (auth()->user()->wallet->id ?? null);
    @endphp
    @if (session('error'))
        <div class="text-center alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @forelse($transactions as $tx)
        @php

            $label = $tx->type === 'DEP' ? 'Depósito' : 'Transferência';
            $badge = $tx->type === 'DEP' ? 'success' : 'secondary';
            $direction = 'neutral';

            if ($tx->type !== 'DEP' && $myWalletId) {
                if ($tx->sender_wallet_id == $myWalletId) {
                    $direction = 'out';
                    $label = 'Envio';
                    $badge = 'warning';
                } elseif ($tx->receiver_wallet_id == $myWalletId) {
                    $direction = 'in';
                    $label = 'Recebimento';
                    $badge = 'primary';
                }
            }

            $sign = $direction === 'out' ? '-' : '+';
            $amountClass = $direction === 'out' ? 'text-danger' : 'text-success';

            $counterpart = '—';

            if ($direction === 'out') {
                $counterpart = optional($tx->receiverWallet->user)->name ?? '—';
            } elseif ($direction === 'in') {
                $counterpart = optional($tx->senderWallet->user)->name ?? '—';
            }

            $date = optional($tx->created_at)->format('d/m/Y H:i') ?? $tx->created_at;
        @endphp

        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title mb-1">
                            {{ $label }}
                            <span class="badge bg-{{ $badge }} ms-2">{{ $tx->type }}</span>
                        </h6>

                        <p class="mb-1 text-muted small">
                            {{ $date }}
                            @if ($direction === 'out')
                                • Para: {{ $counterpart }}
                            @elseif($direction === 'in')
                                • De: {{ $counterpart }}
                            @endif
                        </p>
                    </div>

                    <div class="text-end">
                        <div class="h5 mb-1 {{ $amountClass }}">
                            {{ $sign }} R$ {{ number_format($tx->amount, 2, ',', '.') }}
                        </div>
                        <small class="text-muted">ID: {{ $tx->id }}</small>

                        @if ($tx->type === 'TRA')
                            <form method="POST" action="{{ route('revert', Crypt::encrypt($tx->id)) }}"
                                onsubmit="return confirm('Deseja reverter esta transação?');" class="d-inline-block">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 m-0 text-danger" title="Reverter">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-secondary text-center">Nenhuma movimentação encontrada.</div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-3">
    {!! $transactions->links() !!}
</div>
