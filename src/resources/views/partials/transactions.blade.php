<div class="row">
    @forelse($transactions as $tx)
        @php
            $me = auth()->id();
            $typeLabel = '';
            $badgeClass = 'secondary';
            if ($tx->type === 'deposit') {
                $typeLabel = 'Depósito';
                $badgeClass = 'success';
            } elseif ($tx->type === 'transfer' && $tx->from_user_id == $me) {
                $typeLabel = 'Envio';
                $badgeClass = 'warning';
            } elseif ($tx->type === 'transfer' && $tx->to_user_id == $me) {
                $typeLabel = 'Recebimento';
                $badgeClass = 'primary';
            } else {
                $typeLabel = ucfirst($tx->type);
            }
            $sign = $tx->from_user_id == $me && $tx->type === 'transfer' ? '-' : '+';
        @endphp

        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title mb-1">{{ $typeLabel }}
                            <span class="badge bg-{{ $badgeClass }} ms-2">{{ $tx->status }}</span>
                        </h6>
                        <p class="mb-1 text-muted small">
                            {{ $tx->created_at->format('d/m/Y H:i') ?? $tx->created_at }}
                            @if ($tx->type === 'transfer')
                                • @if ($tx->from_user_id == $me)
                                    Para:
                                @else
                                    De:
                                @endif
                                {{ $tx->from_user_id == $me ? $tx->to_user->name ?? '—' : $tx->from_user->name ?? '—' }}
                            @endif
                        </p>
                        @if (!empty($tx->meta['note']))
                            <p class="mb-0 small text-muted">Obs: {{ $tx->meta['note'] }}</p>
                        @endif
                    </div>

                    <div class="text-end">
                        <div class="h5 mb-1 {{ $sign === '-' ? 'text-danger' : 'text-success' }}">
                            {{ $sign }} R$ {{ number_format($tx->amount, 2, ',', '.') }}
                        </div>
                        <small class="text-muted">ID: {{ $tx->id }}</small>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light text-center">Nenhuma movimentação encontrada.</div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-3">
    {!! $transactions->links() !!}
</div>
