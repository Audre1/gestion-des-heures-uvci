<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Accès interdit — UVCI</title>
    <link rel="icon" href="{{ asset('images/logo-simple.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --uvci-green: #00a54e;
            --uvci-green-dark: #008a41;
            --uvci-green-light: #e6f7ee;
            --uvci-purple: #91268f;
            --uvci-purple-dark: #741d72;
            --uvci-gradient: linear-gradient(135deg, #00a54e 0%, #91268f 100%);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(160deg, #f4f6fb 0%, #e8ecf4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1f2937;
        }

        .error-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.08);
            max-width: 520px;
            width: 100%;
            padding: 60px 48px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .logo {
            height: 48px;
            margin-bottom: 32px;
        }

        .error-code {
            font-size: 120px;
            font-weight: 800;
            background: var(--uvci-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }

        .error-icon {
            font-size: 48px;
            color: #dc2626;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        p {
            color: #6b7280;
            margin-bottom: 32px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: var(--uvci-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            text-decoration: none;
        }

        .btn-home:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: white;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: transparent;
            color: var(--uvci-purple);
            border: 2px solid var(--uvci-purple);
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
            margin-left: 8px;
        }

        .btn-login:hover {
            background: var(--uvci-purple);
            color: white;
        }

        .help-text {
            margin-top: 28px;
            font-size: 0.82rem;
            color: #9ca3af;
        }

        .help-text a {
            color: var(--uvci-green);
            text-decoration: none;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .error-card { padding: 40px 24px 32px; }
            .error-code { font-size: 80px; }
            .btn-login { margin-left: 0; margin-top: 8px; }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <img src="{{ asset('images/logo-long.png') }}" alt="UVCI" class="logo">

        <div class="error-icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="error-code">403</div>

        <h1>Accès interdit</h1>

        <p>
            @if ($exception?->getMessage() && $exception->getMessage() !== '')
                {{ $exception->getMessage() }}
            @else
                Vous n'avez pas les permissions nécessaires pour accéder à cette page.
                Si vous pensez qu'il s'agit d'une erreur, contactez l'administrateur.
            @endif
        </p>

        <div class="d-flex justify-content-center">
            <a href="{{ route('dashboard') }}" class="btn-home">
                <i class="fa-solid fa-house"></i> Retour à l'accueil
            </a>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Connexion
            </a>
        </div>

        <div class="help-text">
            <i class="fa-regular fa-circle-question me-1"></i>
            Besoin d'aide ?
            <a href="{{ route('profil.index') }}">Contactez le support</a>
        </div>
    </div>
</body>
</html>