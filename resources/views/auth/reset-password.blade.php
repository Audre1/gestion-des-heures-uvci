@extends('layouts.auth')

@section('title', 'Vérification du code')

@section('content')
    <a href="{{ route('password.request') }}" class="text-muted small d-inline-flex align-items-center gap-1 mb-4">
        <i class="fa-solid fa-arrow-left"></i> Retour
    </a>

    <h2>Vérification du code</h2>
    <p class="subtitle">
        Saisissez le code à 6 chiffres envoyé à votre adresse email.
    </p>

    @if (session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.verify') }}" method="POST" id="verifyForm">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
        <input type="hidden" name="code" id="fullCode" value="">

        {{-- Code OTP en digits --}}
        <label class="form-label d-block text-center">Code de vérification</label>
        <div class="otp-group" id="otpGroup">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]"
                autofocus>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" name="code[]">
        </div>

        <p class="text-center small text-muted mb-4">
            Vous n'avez pas reçu de code ?
            <a href="#" onclick="resendCode(); return false;" class="text-uvci-green fw-semibold">Renvoyer le code</a>
        </p>

        <button type="submit" class="btn btn-uvci-purple w-100 py-2">
            <i class="fa-solid fa-check me-1"></i> Vérifier le code
        </button>
    </form>

    <form action="{{ route('password.resend') }}" method="POST" id="resendForm" style="display: none;">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
    </form>
@endsection

@section('scripts')
    <script>
        // Navigation automatique entre les cases du code
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
                const digits = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0,
                    inputs.length);
                digits.split('').forEach((d, i) => {
                    inputs[i].value = d;
                    inputs[i].classList.add('filled');
                });
                if (digits.length) inputs[Math.min(digits.length, inputs.length - 1)].focus();
            });
        });

        // Pré-remplir les champs OTP avec les anciennes valeurs si elles existent
        @if (old('code'))
            const oldCode = '{{ old('code') }}';
            if (oldCode && oldCode.length === 6) {
                inputs.forEach((input, index) => {
                    input.value = oldCode[index] || '';
                    if (input.value) {
                        input.classList.add('filled');
                    }
                });
            }
        @endif

        // Combiner les digits en un seul code avant soumission
        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            const codeInputs = document.querySelectorAll('.otp-input');
            const code = Array.from(codeInputs).map(input => input.value).join('');

            // Mettre à jour le champ caché avec le code complet
            document.getElementById('fullCode').value = code;

            // Supprimer les champs individuels
            codeInputs.forEach(input => input.remove());

            console.log('Code soumis:', code);
        });
    </script>
@endsection
