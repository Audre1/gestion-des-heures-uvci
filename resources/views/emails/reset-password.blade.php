<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - UVCI</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6fb;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: #00a54e;
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            background: white;
            border-radius: 12px;
            padding: 15px 25px;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .logo-text {
            font-size: 24px;
            font-weight: 800;
            color: #00a54e;
            margin: 0;
        }

        .header h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin: 0;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .message {
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .code-box {
            background: linear-gradient(135deg, rgba(0, 165, 78, 0.1) 0%, rgba(145, 38, 143, 0.1) 100%);
            border: 2px solid #00a54e;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }

        .code-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .code {
            font-size: 36px;
            font-weight: 800;
            color: #91268f;
            letter-spacing: 8px;
            margin: 0;
        }

        .warning {
            background: #fef3e2;
            border-left: 4px solid #d97706;
            padding: 15px;
            border-radius: 8px;
            margin: 25px 0;
            font-size: 14px;
            color: #92400e;
        }

        .warning strong {
            color: #b45309;
        }

        .footer {
            background: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 5px 0;
        }

        .footer a {
            color: #00a54e;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #00a54e 0%, #91268f 100%);
            color: white;
            padding: 14px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo-simple.png') }}" alt="UVCI Logo" style="height: 50px;">
            </div>
            <h1>Réinitialisation de mot de passe</h1>
            <p>Gestion des Heures des Enseignants</p>
        </div>

        <div class="content">
            <p class="greeting">Bonjour {{ $nom }},</p>

            <p class="message">
                Vous avez demandé la réinitialisation de votre mot de passe pour accéder à la plateforme de gestion des
                heures des enseignants de l'Université Virtuelle de Côte d'Ivoire.
            </p>

            <p class="message">
                Voici votre code de vérification à 6 chiffres :
            </p>

            <div class="code-box">
                <p class="code-label">Code de vérification</p>
                <p class="code">{{ $code }}</p>
            </div>

            <div class="warning">
                <strong>⚠️ Important :</strong> Ce code expire dans 15 minutes. Ne le partagez avec personne. Si vous
                n'avez pas demandé cette réinitialisation, ignorez cet email.
            </div>

            <p class="message">
                Si vous avez des questions ou besoin d'assistance, n'hésitez pas à contacter le support technique.
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Université Virtuelle de Côte d'Ivoire</p>
            <p>Tous droits réservés</p>
            <p>
                <a href="#">Support Technique</a> •
                <a href="#">Politique de confidentialité</a>
            </p>
        </div>
    </div>
</body>

</html>
