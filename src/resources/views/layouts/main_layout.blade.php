<!DOCTYPE html>
<html lang="pt">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank-App</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href= "{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href= "{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/zebra.png')}}" type="image/png">
</head>
<body>

    @yield('content')

    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="js/home.js"></script>
</body>
</html>

