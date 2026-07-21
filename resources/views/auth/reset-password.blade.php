@extends('layouts.auth')

@section('title', 'Vérification du code')

@section('content')
    <h2>Vérification du code</h2>
    <p class="subtitle">
        Saisissez le code à 6 chiffres envoyé à votre adresse email.
    </p>

    <div id="alertContainer">
        @if (session('status'))
            <div class="alert alert-success text-center alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif
    </div>

    <form action="{{ route('password.verify') }}" method="POST" id="verifyForm">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
        <input type="hidden" name="code" id="fullCode" value="">

        <label class="form-label d-block text-center">Code de vérification</label>
        <div class="otp-group" id="otpGroup">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]" autofocus>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
        </div>

        <button type="submit" class="btn btn-uvci-purple w-100 py-2 mb-3 btn-with-spinner" id="verifyBtn">
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
            <span class="btn-text"><i class="fa-solid fa-check me-1"></i> Vérifier le code</span>
        </button>
    </form>

    <form action="{{ route('password.resend') }}" method="POST" id="resendForm" style="display: none;">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
    </form>

    <div class="text-center">
        <p class="small text-muted mb-1">Vous n'avez pas reçu de code ?</p>
        <button type="button" id="resendBtn" class="btn btn-link text-uvci-green fw-semibold p-0 border-0"
            onclick="handleResendCode()">
            <span class="spinner-border spinner-border-sm me-1 d-none" id="resendSpinner" role="status" aria-hidden="true"></span>
            <span id="resendBtnText">Renvoyer le code</span>
        </button>
        <div id="resendCooldown" class="text-muted small d-none mt-1"></div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        // ─── Navigation OTP ─────────────────────────────────────────────────
        const inputs = [...document.querySelectorAll('.otp-input')];
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/[^0-9]/g, '');
                input.classList.toggle('filled', input.value !== '');
                if (input.value && index < inputs.length - 1) inputs[index + 1].focus();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
            });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const digits = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, inputs.length);
                digits.split('').forEach((d, i) => {
                    inputs[i].value = d;
                    inputs[i].classList.add('filled');
                });
                if (digits.length) inputs[Math.min(digits.length, inputs.length - 1)].focus();
            });
        });

        // ─── Soumission AJAX du formulaire de vérification ───────────────────
        const verifyForm = document.getElementById('verifyForm');
        const verifyBtn = document.getElementById('verifyBtn');
        const alertContainer = document.getElementById('alertContainer');

        if (verifyForm) {
            verifyForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Récupérer le code complet
                const codeInputs = document.querySelectorAll('.otp-input');
                const code = Array.from(codeInputs).map(input => input.value).join('');

                if (code.length !== codeInputs.length) {
                    showAlert('warning', 'Veuillez saisir les 6 chiffres du code de vérification.');
                    return;
                }

                // Désactiver le bouton
                verifyBtn.disabled = true;
                verifyBtn.classList.add('disabled');
                const spinner = verifyBtn.querySelector('.spinner-border');
                if (spinner) spinner.classList.remove('d-none');

                // Masquer les anciennes alertes
                alertContainer.innerHTML = '';

                const email = document.querySelector('input[name="email"]').value;

                fetch(verifyForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: new URLSearchParams({ email: email, code: code }),
                })
                .then(response => response.json().then(data => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (status === 200 || status === 201 || status === 302) {
                        // Redirection vers la page nouveau mot de passe
                        window.location.href = '{{ route('password.new', ['email' => '__EMAIL__', 'code' => '__CODE__']) }}'
                            .replace('__EMAIL__', encodeURIComponent(email))
                            .replace('__CODE__', encodeURIComponent(code));
                    } else {
                        showAlert('danger', data.message || 'Code invalide.');
                        resetVerifyButton();
                    }
                })
                .catch(() => {
                    // En cas d'erreur réseau, on soumet normalement
                    verifyForm.submit();
                });
            });
        }

        // ─── Renvoi du code avec cooldown ──────────────────────────────────
        let resendTimer = null;

        function handleResendCode() {
            const resendBtn = document.getElementById('resendBtn');
            const spinner = document.getElementById('resendSpinner');
            const btnText = document.getElementById('resendBtnText');

            // Afficher le spinner
            if (spinner) spinner.classList.remove('d-none');
            const cooldownDiv = document.getElementById('resendCooldown');

            // Désactiver le bouton immédiatement
            resendBtn.disabled = true;
            resendBtn.classList.add('text-muted');
            resendBtn.classList.remove('text-uvci-green');

            const email = document.querySelector('input[name="email"]').value;
            const token = document.querySelector('input[name="_token"]').value;

            fetch('{{ route('password.resend') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: new URLSearchParams({ email: email }),
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                if (status === 200 || status === 201) {
                    showAlert('success', data.message);
                    if (data.cooldown > 0) {
                        startResendCooldown(data.cooldown);
                    }
                } else {
                    showAlert('danger', data.message || 'Impossible de renvoyer le code.');
                    if (status === 429 && data.cooldown) {
                        startResendCooldown(data.cooldown);
                    } else {
                        resetResendButton();
                    }
                }
            })
            .catch(() => {
                // Fallback : soumission classique
                document.getElementById('resendForm').submit();
            });
        }

        function startResendCooldown(seconds) {
            const resendBtn = document.getElementById('resendBtn');
            const cooldownDiv = document.getElementById('resendCooldown');
            const spinner = document.getElementById('resendSpinner');
            const origText = 'Renvoyer le code';

            // Masquer le spinner
            if (spinner) spinner.classList.add('d-none');

            cooldownDiv.classList.remove('d-none');

            if (resendTimer) clearInterval(resendTimer);

            resendTimer = setInterval(() => {
                if (seconds > 0) {
                    cooldownDiv.textContent = `Veuillez attendre ${seconds} seconde(s) avant de renvoyer un code.`;
                    seconds--;
                } else {
                    clearInterval(resendTimer);
                    resendTimer = null;
                    cooldownDiv.classList.add('d-none');
                    resetResendButton();
                }
            }, 1000);
        }

        function resetResendButton() {
            const resendBtn = document.getElementById('resendBtn');
            const spinner = document.getElementById('resendSpinner');
            if (spinner) spinner.classList.add('d-none');
            resendBtn.disabled = false;
            resendBtn.classList.remove('text-muted');
            resendBtn.classList.add('text-uvci-green');
        }

        function resetVerifyButton() {
            verifyBtn.disabled = false;
            verifyBtn.classList.remove('disabled');
            const spinner = verifyBtn.querySelector('.spinner-border');
            if (spinner) spinner.classList.add('d-none');
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} text-center alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            `;
            alertContainer.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.classList.add('d-none');
            }, 5000);
        }

        // ─── Démarrer le cooldown si présent au chargement ─────────────────
        document.addEventListener('DOMContentLoaded', function () {
            const initialCooldown = {{ $cooldown ?? 0 }};
            if (initialCooldown > 0) {
                startResendCooldown(initialCooldown);
            }
        });
    </script>
@endsection