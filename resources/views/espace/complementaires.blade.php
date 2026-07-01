<x-app-page title="Mes heures complémentaires" section="Espace Enseignant" icon="fa-solid fa-hourglass-end"
    subtitle="Détail de vos heures effectuées au-delà du service statutaire.">

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
            <div><div class="stat-value">36 h</div><div class="stat-label">Total heures compl.</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div><div class="stat-value">25 000</div><div class="stat-label">Taux horaire (FCFA)</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon purple"><i class="fa-solid fa-sack-dollar"></i></div>
            <div><div class="stat-value">900 000</div><div class="stat-label">Montant estimé (FCFA)</div></div>
        </div></div></div>
    </div>

    <x-data-table search-placeholder="Rechercher..." :count="3" :show-filters="false">
        <x-slot:head>
            <th>Période</th><th>Cours concerné</th><th>Heures</th><th>Taux</th><th>Montant</th>
        </x-slot:head>
        @php
            $hc = [
                ['Octobre 2024', 'INF-101 — Algorithmique', '14h', '25 000 FCFA', '350 000 FCFA'],
                ['Novembre 2024', 'INF-205 — Bases de données', '12h', '25 000 FCFA', '300 000 FCFA'],
                ['Décembre 2024', 'INF-310 — Développement Web', '10h', '25 000 FCFA', '250 000 FCFA'],
            ];
        @endphp
        @foreach($hc as [$per, $cours, $h, $taux, $mnt])
            <tr>
                <td class="fw-semibold">{{ $per }}</td>
                <td>{{ $cours }}</td>
                <td><span class="badge badge-soft-amber">{{ $h }}</span></td>
                <td>{{ $taux }}</td>
                <td class="fw-semibold text-uvci-green">{{ $mnt }}</td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
