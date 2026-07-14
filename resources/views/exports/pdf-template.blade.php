<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 20px;
            size: landscape;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #00A54E;
        }

        .logo {
            max-height: 50px;
        }

        .title-section {
            text-align: center;
            flex: 1;
        }

        .title-section h1 {
            margin: 0;
            color: #00A54E;
            font-size: 18px;
            font-weight: bold;
        }

        .title-section p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 10px;
        }

        .table-container {
            width: 100%;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #00A54E;
            color: white;
        }

        th {
            padding: 8px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #008F42;
        }

        td {
            padding: 6px 12px;
            border: 1px solid #DDDDDD;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background-color: #F9F9F9;
        }

        tbody tr:hover {
            background-color: #F0F8F0;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #DDDDDD;
            text-align: center;
            color: #666;
            font-size: 9px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-green {
            background-color: #D4EDDA;
            color: #155724;
        }

        .badge-red {
            background-color: #F8D7DA;
            color: #721C24;
        }

        .badge-blue {
            background-color: #D1ECF1;
            color: #0C5460;
        }

        .badge-purple {
            background-color: #E2D9F3;
            color: #6F42C1;
        }

        .badge-amber {
            background-color: #FFF3CD;
            color: #856404;
        }

        .badge-gray {
            background-color: #E2E3E5;
            color: #383D41;
        }
    </style>
</head>

<body>
    <div class="header">
        @if ($logo)
            <img src="{{ $logo }}" alt="Logo UVCI" class="logo">
        @endif
        <div class="title-section">
            <h1>{!! $title !!}</h1>
            <p>Généré le {{ $date }}</p>
        </div>
        <div style="width: 100px;"></div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{!! $header !!}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{!! $cell !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Université Virtuelle de Côte d'Ivoire - Système de Gestion des Heures</p>
        <p>Document généré automatiquement - {{ $date }}</p>
    </div>
</body>

</html>
