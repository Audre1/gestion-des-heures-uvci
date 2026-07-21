@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')

@section('content')
    <h2>Nouveau mot de passe</h2>
    <p class="subtitle">
        Définissez votre nouveau mot de passe.
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

    <form action="{{ route('password.update') }}" method="POST" id="newPasswordForm">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="code" value="{{ $code }}">

        <div class="mb-3">
            <label class="form-label" for="password">Nouveau mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control border-end-0" id="password" name="password"
                    placeholder="••••••••" required minlength="8">
                <span class="input-group-text bg-white" style="cursor:pointer" onclick="togglePwd()">
                    <i class="fa-solid fa-eye" id="pwdIcon"></i>
                </span>
            </div>
            <div class="form-text text-muted">Minimum 8 caractères.</div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="••••••••" required minlength="8">
            </div>
        </div>

        <div id="passwordMismatch" class="alert alert-warning text-center d-none" role="alert">
            Les mots de passe ne correspondent pas.
        </div>

        <button type="submit" class="btn btn-uvci-purple w-100 py-2 btn-with-spinner" id="submitBtn">
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
            <span class="btn-text"><i class="fa-solid fa-check me-1"></i> Modifier le mot de passe</span>
        </button>
    </form>

    <p class="text-center text-muted small mb-0 mt-4">
        <a href="{{ route('login') }}" class="text-uvci-green fw-semibold">Se connecter</a>
    </p>
@endsection

@section('scripts')
    @parent
    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            const icon = document.getElementById('pwdIcon');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
        }

        // Validation côté client avant soumission
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('newPasswordForm');
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const mismatchAlert = document.getElementById('passwordMismatch');
            const submitBtn = document.getElementById('submitBtn');

            if (!form) return;

            // Vérifier en temps réel la correspondance des mots de passe
            function checkMatch() {
                if (confirmation.value.length > 0 && password.value !== confirmation.value) {
                    mismatchAlert.classList.remove('d-none');
                    return false;
                } else {
                    mismatchAlert.classList.add('d-none');
                    return true;
                }
            }

            password.addEventListener('input', checkMatch);
            confirmation.addEventListener('input', checkMatch);

            form.addEventListener('submit', function (e) {
                if (!checkMatch()) {
                    e.preventDefault();
                    return;
                }

                // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled');
                const spinner = submitBtn.querySelector('.spinner-border');
                if (spinner) spinner.classList.remove('d-none');
            });
        });
    </script>
@endsection