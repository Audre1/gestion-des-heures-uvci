<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>État des heures</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
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
            text-align: center;
        }

        th {
            background-color: #eeeeee;
        }
    </style>
</head>

<body>

<h2>État des heures de l'enseignant</h2>

<h4>
    Enseignant :
    {{ $enseignant->prenom }} {{ $enseignant->nom }}
</h4>

<p>
    Matricule : {{ $enseignant->matricule }}
</p>

<hr>

<h3>Détail des activités réalisées</h3>

<table>

    <thead>
        <tr>
            <th>Date</th>
            <th>Cours</th>
            <th>Volume horaire</th>
            <th>Statut</th>
        </tr>
    </thead>

    <tbody>

    @foreach($activites as $activite)

        <tr>
            <td>
                {{ $activite->date_activite }}
            </td>

            <td>
                {{ $activite->affectationCours->cours->intitule ?? 'Non défini' }}
            </td>

            <td>
                {{ $activite->volume_horaire }} h
            </td>

            <td>
                {{ $activite->statut }}
            </td>
        </tr>

    @endforeach

    </tbody>

</table>


<h3>Synthèse horaire</h3>

<table>

<tr>
    <th>Service statutaire</th>
    <td>{{ $serviceStatutaire }} h</td>
</tr>

<tr>
    <th>Volume horaire réalisé</th>
    <td>{{ $volumeRealise }} h</td>
</tr>

<tr>
    <th>Heures complémentaires</th>
    <td>{{ $heuresComplementaires }} h</td>
</tr>

</table>


</body>
</html>