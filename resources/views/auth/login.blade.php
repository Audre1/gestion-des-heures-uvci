@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
    <h2>Bienvenue</h2>
    <p class="subtitle">Connectez-vous à votre espace de travail UVCI.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('authenticate') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="login">Identifiant / Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" class="form-control" id="login" name="login"
                    placeholder="ex : k.kouassi@uvci.edu.ci" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control border-end-0" id="password" name="password"
                    placeholder="••••••••" required>
                <span class="input-group-text bg-white" style="cursor:pointer" onclick="togglePwd()">
                    <i class="fa-solid fa-eye" id="pwdIcon"></i>
                </span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-uvci-purple fw-semibold">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn btn-uvci w-100 py-2 mb-3">
            <i class="fa-solid fa-right-to-bracket me-1"></i> Se connecter
        </button>
    </form>

    <p class="text-center text-muted small mb-0 mt-4">
        © {{ date('Y') }} Université Virtuelle de Côte d'Ivoire
    </p>
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
