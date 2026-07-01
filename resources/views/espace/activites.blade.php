<x-app-page title="Mes activités" section="Espace Enseignant" icon="fa-solid fa-folder-open"
    subtitle="Historique de vos activités pédagogiques (lecture seule).">

    <x-data-table search-placeholder="Rechercher une activité..." :count="5" :show-filters="true">
        <x-slot:head>
            <th>Cours</th><th>Type</th><th>Niveau</th><th>Séq.</th><th>VHT</th><th>Date</th><th>Statut</th><th class="text-end">Détail</th>
        </x-slot:head>
        @php
            $act = [
                ['INF-101 — Algorithmique', 'Création', 'Niv. 2', 40, '30h', '15/10/2024', 'Validée', 'green'],
                ['INF-205 — Bases de données', 'Création', 'Niv. 1', 40, '16h', '20/10/2024', 'Validée', 'green'],
                ['INF-310 — Développement Web', 'Mise à jour', 'Niv. 2', 40, '15h', '25/10/2024', 'En cours', 'amber'],
                ['INF-101 — Algorithmique', 'Mise à jour', 'Niv. 1', 40, '8h', '01/11/2024', 'Validée', 'green'],
                ['INF-205 — Bases de données', 'Création', 'Niv. 3', 80, '120h', '05/11/2024', 'En cours', 'amber'],
            ];
        @endphp
        @foreach($act as [$cours, $type, $niv, $seq, $vht, $date, $st, $c])
            <tr>
                <td class="fw-semibold">{{ $cours }}</td>
                <td><span class="badge badge-soft-{{ $type === 'Création' ? 'green' : 'purple' }}">{{ $type }}</span></td>
                <td>{{ $niv }}</td><td>{{ $seq }}</td>
                <td class="fw-semibold text-uvci-green">{{ $vht }}</td>
                <td class="text-muted">{{ $date }}</td>
                <td><span class="badge badge-soft-{{ $c }}">{{ $st }}</span></td>
                <td class="text-end"><button class="btn btn-sm btn-light border"><i class="fa-solid fa-eye text-muted"></i></button></td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
