@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
    <a href="{{ route('login') }}" class="text-muted small d-inline-flex align-items-center gap-1 mb-4">
        <i class="fa-solid fa-arrow-left"></i> Retour à la connexion
    </a>

    <h2>Mot de passe oublié ?</h2>
    <p class="subtitle">
        Saisissez l'adresse email associée à votre compte. Un lien de réinitialisation
        vous sera envoyé par email.
    </p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger text-center">
            {{-- @foreach ($errors->all() as $error) --}}
            <div>{{ $errors->first() }}</div>
            {{-- @endforeach --}}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label" for="email">Adresse email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="ex : k.kouassi@uvci.edu.ci" value="{{ old('email') }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-uvci w-100 py-2 mb-3">
            <i class="fa-solid fa-paper-plane me-1"></i> Envoyer le code
        </button>
    </form>

    <p class="text-center text-muted small mb-0 mt-4">
        Vous vous souvenez de votre mot de passe ?
        <a href="{{ route('login') }}" class="text-uvci-green fw-semibold">Se connecter</a>
    </p>
@endsection
