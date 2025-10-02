@extends('layouts.main_layout')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-sm-8">
                <div class="card p-5">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </div>
                    @endif
   
                    @if (session('accountCreated'))
                        <div class="text-center alert alert-success">
                            {{ session('accountCreated') }}
                        </div>
                    @endif
   
                    @if (session('loginError'))
                        <div class="alert alert-danger">
                            <li>{{ session('loginError') }}</li>
                        </div>
                    @endif

                    <div class="text-center p-3">
                        <img src="assets/images/zebra.png" style="max-height: 80px" alt="Chamados-Logo">
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-12">
                            <form action="/loginSub" method="post" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control bg-dark text-info" name="email"
                                        value="{{ old('email') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control bg-dark text-info" name="password"
                                        value="{{ old('password') }}">
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-secondary w-100">LOGIN</button>
                                </div>
                            </form>
                            <div class="mb-3">
                                <a href="{{ route('createAccount') }}" class="btn btn-warning w-100">CRIAR CONTA</a>
                            </div>
                        </div>
                    </div>

                    <div class="text-center text-secondary mt-3">
                        <small>&copy; <?= date('Y') ?> Bank-App</small>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
