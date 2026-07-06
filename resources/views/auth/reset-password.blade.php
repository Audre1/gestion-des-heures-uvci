@extends('layouts.auth')

@section('title', 'Réinitialisation du mot de passe')

@section('content')
    <a href="{{ route('password.request') }}" class="text-muted small d-inline-flex align-items-center gap-1 mb-4">
        <i class="fa-solid fa-arrow-left"></i> Retour
    </a>

    <h2>Réinitialisation du mot de passe</h2>
    <p class="subtitle">
        Définissez votre nouveau mot de passe.
    </p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <div class="mb-3">
            <label class="form-label" for="email">Adresse email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ $email ?? old('email') }}" readonly>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Nouveau mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
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
            <i class="fa-solid fa-check me-1"></i> Réinitialiser le mot de passe
        </button>
    </form>
@endsection
