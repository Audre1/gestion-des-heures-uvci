<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fiche individuelle</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }

        .section {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
        }
    </style>
</head>

<body>

<h2>Fiche individuelle</h2>

<p style="text-align:center;">
    Vos informations et charge horaire consolidée
</p>


<div class="section">

<h3>Informations de l'enseignant</h3>

<table>
    <tr>
        <th>Nom</th>
        <td>{{ $enseignant->nom }}</td>
    </tr>

    <tr>
        <th>Prénom</th>
        <td>{{ $enseignant->prenom }}</td>
    </tr>

    <tr>
        <th>Email</th>
        <td>{{ $enseignant->email }}</td>
    </tr>

    <tr>
        <th>Matricule</th>
        <td>{{ $enseignant->matricule }}</td>
    </tr>
</table>
</div>



<div class="section">

<h3>Charge horaire consolidée</h3>

<table border="1" width="100%" cellpadding="5">
    <tr>
        <th>Service statutaire</th>
        <td>{{ $serviceStatutaire }} heures</td>
    </tr>

    <tr>
        <th>Volume horaire réalisé</th>
        <td>{{ $volumeRealise }} heures</td>
    </tr>

    <tr>
        <th>Heures complémentaires</th>
        <td>{{ $heuresComplementaires }} heures</td>
    </tr>
</table>

</div>


</body>
</html>