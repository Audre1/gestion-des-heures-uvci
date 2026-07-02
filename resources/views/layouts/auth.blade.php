<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Connexion') — UVCI</title>
    <link rel="icon" href="{{ asset('images/logo-simple.png') }}">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome 6 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Thème UVCI --}}
    <link href="{{ asset('css/uvci.css') }}" rel="stylesheet">
</head>

<body>
    <div class="auth-wrapper">
        {{-- Panneau illustratif --}}
        <div class="auth-visual" id="authVisual">
            <div class="brand-badge">
                <img src="{{ asset('images/logo-long.png') }}" alt="UVCI">
            </div>
            <h1 class="mt-5">Gestion des Heures<br>des Enseignants</h1>
            <p class="mt-3 mb-0" style="max-width:420px;position:relative;opacity:.9">
                Plateforme officielle de l'Université Virtuelle de Côte d'Ivoire pour
                l'automatisation du calcul des volumes horaires et des états de paiement.
            </p>
            <ul class="feature-list">
                <li><i class="fa-solid fa-calculator"></i> Calcul automatique des volumes horaires</li>
                <li><i class="fa-solid fa-clock"></i> Suivi des heures complémentaires</li>
                <li><i class="fa-solid fa-file-export"></i> États de paiement exportables (PDF / Excel)</li>
            </ul>
        </div>

        {{-- Formulaire --}}
        <div class="auth-form-side">
            <div class="auth-card">
                <div class="auth-logo">
                    <img src="{{ asset('images/logo-long.png') }}" alt="UVCI">
                </div>
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const authVisual = document.getElementById('authVisual');

            if (authVisual) {
                const randomBg = Math.floor(Math.random() * 7) + 1;

                authVisual.style.backgroundImage = `
                linear-gradient(
                    90deg,
                    rgba(10, 20, 35, 0.88) 0%,
                    rgba(10, 20, 35, 0.65) 45%,
                    rgba(10, 20, 35, 0.35) 100%
                ),
                url('/images/auth-bg-${randomBg}.jpg')
            `;
                authVisual.style.backgroundSize = 'cover';
                authVisual.style.backgroundPosition = 'center';
                authVisual.style.backgroundRepeat = 'no-repeat';
            }
        });
    </script>

    @yield('scripts')
</body>

</html>
