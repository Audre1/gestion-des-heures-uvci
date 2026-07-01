<x-app-page title="Journaux d'activités" section="Administration" icon="fa-solid fa-clipboard-list"
    subtitle="Traçabilité des actions réalisées dans le système.">

    <x-data-table search-placeholder="Rechercher dans les journaux..." :count="7">
        <x-slot:head>
            <th>Date / Heure</th><th>Utilisateur</th><th>Action</th><th>Cible</th><th>Adresse IP</th>
        </x-slot:head>
        @php
            $logs = [
                ['01/07/2025 14:32', 'k.kouassi', 'Connexion', 'Session', '196.201.x.x'],
                ['01/07/2025 14:10', 'a.traore', 'Validation activité', 'Activité #1042', '196.201.x.x'],
                ['01/07/2025 13:55', 'a.traore', 'Création enseignant', 'Enseignant #248', '196.201.x.x'],
                ['01/07/2025 11:20', 'k.kouassi', 'Modification taux', 'Taux #12', '196.201.x.x'],
                ['30/06/2025 17:02', 'f.ouattara', 'Génération état', 'Paiement #88', '196.201.x.x'],
                ['30/06/2025 16:44', 'k.kouassi', 'Sauvegarde', 'Base de données', '196.201.x.x'],
                ['30/06/2025 09:15', 'm.diabate', 'Consultation', 'Volume horaire', '154.72.x.x'],
            ];
        @endphp
        @foreach($logs as [$dt, $u, $act, $cible, $ip])
            <tr>
                <td class="text-muted">{{ $dt }}</td>
                <td><span class="badge badge-soft-purple">{{ $u }}</span></td>
                <td>{{ $act }}</td><td>{{ $cible }}</td>
                <td class="text-muted font-monospace small">{{ $ip }}</td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
