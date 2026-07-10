<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Récapitulatif des activités</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }
    </style>
</head>

<body>

<h2>Récapitulatif des activités pédagogiques</h2>

<table>
    <thead>
        <tr>
            <th>Cours</th>
            <th>Niveau</th>
            <th>Volume horaire</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
    </thead>

    <tbody>
        @foreach($activites as $activite)
            <tr>
                <td>
                    {{ $activite->affectationCours->cours->code_cours ?? '-' }}
                </td>

                <td>
                    {{ $activite->niveauComplexite->libelle ?? '-' }}
                </td>

                <td>
                    {{ $activite->volume_horaire ?? '-' }} h
                </td>

                <td>
                    {{ $activite->date_activite?->format('d/m/Y') ?? '-' }}
                </td>

                <td>
                    {{ $activite->statut ?? '-' }}
                </td>
            </tr>
        @endforeach
    </tbody>

</table>

</body>
</html>