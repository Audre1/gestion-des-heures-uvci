@extends('layouts.auth')

@section('title', 'Réinitialisation du mot de passe')

@section('content')
    <a href="{{ route('password.request') }}" class="text-muted small d-inline-flex align-items-center gap-1 mb-4">
        <i class="fa-solid fa-arrow-left"></i> Retour
    </a>

    <div class="mb-3 d-inline-flex align-items-center justify-content-center"
         style="width:60px;height:60px;border-radius:16px;background:var(--uvci-green-light);color:var(--uvci-green);font-size:1.5rem">
        <i class="fa-solid fa-shield-halved"></i>
    </div>

    <h2>Vérification du code</h2>
    <p class="subtitle">
        Saisissez le code à 6 chiffres envoyé à
        <strong class="text-uvci-purple">k.k***@uvci.edu.ci</strong>, puis définissez
        votre nouveau mot de passe.
    </p>

    <form action="{{ route('login') }}" method="GET" id="resetForm">
        {{-- Code OTP en digits --}}
        <label class="form-label d-block text-center">Code de vérification</label>
        <div class="otp-group" id="otpGroup">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autofocus>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]">
        </div>

        <p class="text-center small text-muted mb-4">
            Vous n'avez pas reçu de code ?
            <a href="#" class="text-uvci-green fw-semibold" id="resendLink">Renvoyer</a>
            <span id="timer" class="ms-1"></span>
        </p>

        <hr class="my-4">

        <div class="mb-3">
            <label class="form-label" for="password">Nouveau mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="••••••••" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="password_confirm">Confirmer le mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                       placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-uvci-purple w-100 py-2">
            <i class="fa-solid fa-check me-1"></i> Réinitialiser le mot de passe
        </button>
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
            const digits = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, inputs.length);
            digits.split('').forEach((d, i) => {
                inputs[i].value = d;
                inputs[i].classList.add('filled');
            });
            if (digits.length) inputs[Math.min(digits.length, inputs.length - 1)].focus();
        });
    });

    // Minuteur de renvoi
    let seconds = 60;
    const timerEl = document.getElementById('timer');
    const resend = document.getElementById('resendLink');
    function tick() {
        if (seconds > 0) {
            resend.classList.add('pe-none', 'opacity-50');
            timerEl.textContent = '(' + seconds + 's)';
            seconds--;
            setTimeout(tick, 1000);
        } else {
            resend.classList.remove('pe-none', 'opacity-50');
            timerEl.textContent = '';
        }
    }
    tick();
    resend.addEventListener('click', (e) => {
        e.preventDefault();
        if (seconds <= 0) { seconds = 60; tick(); }
    });
</script>
@endsection
