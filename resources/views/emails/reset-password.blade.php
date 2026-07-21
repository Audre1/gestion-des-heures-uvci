<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>

    <style>
        body {
            margin: 0;
            padding: 25px 0;
            background: #f5f6f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #374151;
        }

        .wrapper {
            width: 100%;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }

        .header {
            text-align: center;
            padding: 35px 30px 25px;
            border-bottom: 4px solid #00a54e;
        }

        .header img {
            height: 60px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1f2937;
            font-weight: 600;
        }

        .header p {
            margin-top: 8px;
            color: #6b7280;
            font-size: 15px;
        }

        .content {
            padding: 35px;
            line-height: 1.7;
            font-size: 15px;
        }

        .content p {
            margin: 0 0 18px;
        }

        .code-box {
            margin: 30px 0;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 25px;
        }

        .code-title {
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .code {
            font-size: 34px;
            font-weight: bold;
            color: #00a54e;
            letter-spacing: 8px;
        }

        .info {
            background: #f9fafb;
            border-left: 4px solid #00a54e;
            padding: 16px;
            margin: 25px 0;
            color: #4b5563;
            font-size: 14px;
        }

        .signature {
            margin-top: 30px;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            background: #fafafa;
            padding: 25px 30px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
        }

        .footer a {
            color: #00a54e;
            text-decoration: none;
        }

        @media only screen and (max-width:600px) {

            .content,
            .header,
            .footer {
                padding: 25px;
            }

            .code {
                font-size: 28px;
                letter-spacing: 6px;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <div class="container">

            <div class="header">
                <img src="{{ asset('images/logo-simple.png') }}" alt="UVCI">

                <h1>Réinitialisation de votre mot de passe</h1>

                <p>
                    Plateforme de gestion des heures des enseignants
                </p>
            </div>

            <div class="content">

                <p>Bonjour <strong>{{ $nom }}</strong>,</p>

                <p>
                    Nous avons reçu une demande de réinitialisation du mot de passe associé à votre compte.
                </p>

                <p>
                    Pour poursuivre cette opération, veuillez saisir le code de vérification ci-dessous sur la page de réinitialisation.
                </p>

                <div class="code-box">

                    <div class="code-title">
                        Code de vérification
                    </div>

                    <div class="code">
                        {{ $code }}
                    </div>

                </div>

                <div class="info">
                    <strong>À noter :</strong> ce code est valable pendant
                    <strong>15 minutes</strong>.
                    Si vous n'êtes pas à l'origine de cette demande,
                    vous pouvez simplement ignorer cet email.
                    Votre mot de passe ne sera pas modifié sans la saisie de ce code.
                </div>

                {{-- <p>
                    Si vous rencontrez des difficultés pour accéder à votre compte,
                    vous pouvez contacter l'équipe en charge de la plateforme.
                </p> --}}

            </div>

            <div class="footer">

                <p>
                    Cet email a été envoyé automatiquement par la plateforme de gestion des heures des enseignants.
                </p>

                <p>
                    © {{ date('Y') }} Université Virtuelle de Côte d'Ivoire
                </p>

            </div>

        </div>

    </div>

</body>

</html>
