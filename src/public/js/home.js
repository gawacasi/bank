    (function() {
        const btn = document.getElementById('toggle-balance');
        const bal = document.getElementById('wallet-balance');
        const icon = document.getElementById('toggle-icon');
        if (!btn || !bal || !icon) return;

        btn.addEventListener('click', function() {
            const hidden = bal.getAttribute('data-hidden') === '1';
            if (hidden) {
                // show
                bal.textContent = bal.getAttribute('data-value-display');
                bal.setAttribute('data-hidden', '0');
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                btn.setAttribute('aria-pressed', 'false');
            } else {
                // hide
                bal.setAttribute('data-value-display', bal.textContent);
                bal.textContent = 'R$ ****';
                bal.setAttribute('data-hidden', '1');
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                btn.setAttribute('aria-pressed', 'true');
            }
        });
    })();