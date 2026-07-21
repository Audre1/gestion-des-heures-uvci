@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
    <a href="{{ route('login') }}" class="text-muted small d-inline-flex align-items-center gap-1 mb-4">
        <i class="fa-solid fa-arrow-left"></i> Retour à la connexion
    </a>

    <h2>Mot de passe oublié ?</h2>
    <p class="subtitle">
        Saisissez l'adresse email associée à votre compte. Un code de vérification
        vous sera envoyé par email.
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
                <div>{{ $errors->first() }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif
    </div>

    <form action="{{ route('password.email') }}" method="POST" id="forgotPasswordForm">
        @csrf
        <div class="mb-4">
            <label class="form-label" for="email">Adresse email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="ex : k.kouassi@uvci.edu.ci" value="{{ old('email') }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-uvci w-100 py-2 mb-3 btn-with-spinner" id="submitBtn">
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
            <span class="btn-text"><i class="fa-solid fa-paper-plane me-1"></i> Envoyer le code</span>
        </button>
    </form>

    <div id="cooldownMessage" class="text-center text-muted small d-none mb-3"></div>

    <p class="text-center text-muted small mb-0 mt-4">
        Vous vous souvenez de votre mot de passe ?
        <a href="{{ route('login') }}" class="text-uvci-green fw-semibold">Se connecter</a>
    </p>
@endsection

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('forgotPasswordForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');
            const cooldownMsg = document.getElementById('cooldownMessage');

            if (!form) return;

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled');
                const spinner = submitBtn.querySelector('.spinner-border');
                if (spinner) spinner.classList.remove('d-none');

                // Masquer les anciennes alertes
                alertContainer.innerHTML = '';
                cooldownMsg.classList.add('d-none');

                const email = document.getElementById('email').value;

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: new URLSearchParams({ email: email }),
                })
                .then(response => response.json().then(data => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (status === 200 || status === 201) {
                        // Succès : rediriger vers la page de saisie du code OTP
                        const email = document.getElementById('email').value;
                        window.location.href = '{{ route('password.reset', ['email' => '__EMAIL__']) }}'
                            .replace('__EMAIL__', encodeURIComponent(email));
                    } else {
                        showAlert('danger', data.message || 'Une erreur est survenue.');
                        resetButton();
                    }
                })
                .catch(() => {
                    showAlert('danger', 'Une erreur réseau est survenue. Veuillez réessayer.');
                    resetButton();
                });
            });

            function showAlert(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} text-center alert-dismissible fade show`;
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                `;
                alertContainer.appendChild(alertDiv);

                // Auto-dismiss après 5 secondes
                setTimeout(() => {
                    alertDiv.classList.add('d-none');
                }, 5000);
            }

            function startCooldown(seconds) {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled');
                const spinner = submitBtn.querySelector('.spinner-border');
                if (spinner) spinner.classList.add('d-none');
                const btnText = submitBtn.querySelector('.btn-text');
                cooldownMsg.classList.remove('d-none');

                const interval = setInterval(() => {
                    cooldownMsg.textContent = `Veuillez attendre ${seconds} seconde(s) avant de renvoyer un code.`;
                    seconds--;
                    if (seconds < 0) {
                        clearInterval(interval);
                        cooldownMsg.classList.add('d-none');
                        resetButton();
                    }
                }, 1000);
            }

            function resetButton() {
                submitBtn.disabled = false;
                submitBtn.classList.remove('disabled');
                const spinner = submitBtn.querySelector('.spinner-border');
                if (spinner) spinner.classList.add('d-none');
            }
        });
    </script>
@endsection