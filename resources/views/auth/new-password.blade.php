@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')

@section('content')
    <a href="{{ route('password.reset', ['email' => $email]) }}" class="text-muted small d-inline-flex align-items-center gap-1 mb-4">
        <i class="fa-solid fa-arrow-left"></i> Retour
    </a>

    <h2>Nouveau mot de passe</h2>
    <p class="subtitle">
        Définissez votre nouveau mot de passe.
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

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="code" value="{{ $code }}">

        <div class="mb-3">
            <label class="form-label" for="password">Nouveau mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control border-end-0" id="password" name="password"
                    placeholder="••••••••" required>
                <span class="input-group-text bg-white" style="cursor:pointer" onclick="togglePwd()">
                    <i class="fa-solid fa-eye" id="pwdIcon"></i>
                </span>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-uvci-purple w-100 py-2">
            <i class="fa-solid fa-check me-1"></i> Modifier le mot de passe
        </button>
    </form>
@endsection

@section('scripts')
    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            const icon = document.getElementById('pwdIcon');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
        }
    </script>
@endsection
