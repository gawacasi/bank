<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
    </style>
    <title>Transações</title>
</head>
<body>
    <div class="header">
        <h1>Transações da Carteira #{{ $wallet['id'] }}</h1>
        <p>Saldo: R$ {{ number_format($wallet['balance'], 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ $t->created_at }}</td>
                    <td>{{ $t->type }}</td>
                    <td>R$ {{ number_format($t->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
