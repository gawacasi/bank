@extends('layouts.main_layout')

@section('content')
<div class="container" style="max-width:720px; padding:24px;">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Cadastrar Transação (externo)</h3>

            <form id="txForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Data e hora (opcional)</label>
                    <input class="form-control" type="datetime-local" id="created_at" name="created_at">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" id="type" name="type">
                        <option value="DEP">DEP</option>
                        <option value="TRA">TRA</option>
                        <option value="REV">REV</option>
                        <option value="INA">INA</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valor</label>
                    <input class="form-control" type="text" id="amount" name="amount" placeholder="1234.56" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sender Wallet ID</label>
                    <input class="form-control" type="number" id="sender_wallet_id" name="sender_wallet_id" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Receiver Wallet ID</label>
                    <input class="form-control" type="number" id="receiver_wallet_id" name="receiver_wallet_id" required>
                </div>

                <button class="btn btn-primary" type="submit">Cadastrar</button>
            </form>

            <div class="mt-3" id="msg"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatDatetimeLocal(value) {
        if (!value) return '';
        const dt = new Date(value);
        if (isNaN(dt)) return '';
        const pad = n => String(n).padStart(2, '0');
        return dt.getFullYear() + '-' + pad(dt.getMonth()+1) + '-' + pad(dt.getDate()) + ' ' + pad(dt.getHours()) + ':' + pad(dt.getMinutes()) + ':' + pad(dt.getSeconds());
    }

    document.getElementById('txForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const msg = document.getElementById('msg');
        msg.textContent = 'Enviando...';

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const payload = {
            created_at: formatDatetimeLocal(document.getElementById('created_at').value),
            type: document.getElementById('type').value,
            amount: document.getElementById('amount').value,
            sender_wallet_id: document.getElementById('sender_wallet_id').value,
            receiver_wallet_id: document.getElementById('receiver_wallet_id').value,
            _token: token
        };

        try {
            const res = await fetch('/external/transactions', {
                method: 'POST',
                headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': token},
                body: JSON.stringify(payload)
            });

            const json = await res.json();
            if (res.ok) {
                msg.style.color = 'green';
                msg.textContent = 'Transação criada: ID ' + json.transaction_id;
                document.getElementById('txForm').reset();
            } else {
                msg.style.color = 'red';
                if (json.errors) {
                    msg.innerHTML = Object.values(json.errors).map(e => e.join(', ')).join('<br>');
                } else if (json.message) {
                    msg.textContent = json.message;
                } else {
                    msg.textContent = 'Erro ao criar transação';
                }
            }
        } catch (err) {
            msg.style.color = 'red';
            msg.textContent = 'Erro de rede';
        }
    });
</script>
</script>
@endpush

@endsection
