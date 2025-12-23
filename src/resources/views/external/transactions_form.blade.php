<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulário externo - Cadastrar Transação</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        .container { max-width: 520px; margin: auto; }
        label { display:block; margin-top:12px; }
        input, select { width:100%; padding:8px; box-sizing:border-box; }
        button { margin-top:16px; padding:10px 16px; }
        .msg { margin-top:12px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Cadastrar Transação (externo)</h2>
    <form id="txForm">
        <label>Data e hora (opcional)
            <input type="datetime-local" id="created_at" name="created_at">
        </label>

        <label>Tipo
            <select id="type" name="type">
                <option value="DEP">DEP</option>
                <option value="TRA">TRA</option>
                <option value="REV">REV</option>
                <option value="INA">INA</option>
            </select>
        </label>

        <label>Valor
            <input type="text" id="amount" name="amount" placeholder="1234.56" required>
        </label>

        <label>Sender Wallet ID
            <input type="number" id="sender_wallet_id" name="sender_wallet_id" required>
        </label>

        <label>Receiver Wallet ID
            <input type="number" id="receiver_wallet_id" name="receiver_wallet_id" required>
        </label>

        <button type="submit">Cadastrar</button>
    </form>

    <div class="msg" id="msg"></div>
</div>

<script>
    function formatDatetimeLocal(value) {
        if (!value) return '';
        // value is like 2025-12-23T05:44
        const dt = new Date(value);
        if (isNaN(dt)) return '';
        const pad = n => String(n).padStart(2, '0');
        return dt.getFullYear() + '-' + pad(dt.getMonth()+1) + '-' + pad(dt.getDate()) + ' ' + pad(dt.getHours()) + ':' + pad(dt.getMinutes()) + ':' + pad(dt.getSeconds());
    }

    document.getElementById('txForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const msg = document.getElementById('msg');
        msg.textContent = 'Enviando...';

        const payload = {
            created_at: formatDatetimeLocal(document.getElementById('created_at').value),
            type: document.getElementById('type').value,
            amount: document.getElementById('amount').value,
            sender_wallet_id: document.getElementById('sender_wallet_id').value,
            receiver_wallet_id: document.getElementById('receiver_wallet_id').value,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        try {
            const res = await fetch('/external/transactions', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
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
</body>
</html>
