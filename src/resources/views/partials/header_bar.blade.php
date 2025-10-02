<div class="col">
    <img src="{{ asset('assets/images/zebra.png') }}" style="max-width: 70px" alt="Chamados logo">
</div>
<div class="col">
    <div class="d-flex justify-content-end align-items-center">
        <span class="me-3"><i
                class="fa-solid fa-user-circle fa-lg text-secondary me-3"></i>{{ $user['name'] }}
        </span>
        
        <button id="toggle-balance" type="button" class="btn btn-sm btn-outline-secondary" aria-pressed="false"
            title="Ocultar/mostrar saldo">
            <i id="toggle-icon" class="fa-solid fa-eye"></i>
        </button>

        <span id="wallet-balance" class="fw-bold me-1 ms-1" data-hidden="0"
            data-value-display="R$ {{ $wallet['balance'] }}">
            R$ {{ $wallet['balance'] }}
        </span>

        <a href={{ route('logout') }} class="btn btn-outline-danger px-3 ms-3">
            Logout<i class="fa-solid fa-arrow-right-from-bracket ms-2"></i>
        </a>
    </div>
</div>
</div>

<hr>
