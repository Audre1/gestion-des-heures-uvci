<x-app-page title="Heures complémentaires" section="Volumes & Paiements" icon="fa-solid fa-clock"
    subtitle="Heures effectuées au-delà du service statutaire.">

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
            <div><div class="stat-value">1 240 h</div><div class="stat-label">Total heures complémentaires</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon purple"><i class="fa-solid fa-users"></i></div>
            <div><div class="stat-value">38</div><div class="stat-label">Enseignants concernés</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon green"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div><div class="stat-value">24,6 M</div><div class="stat-label">FCFA estimés</div></div>
        </div></div></div>
    </div>

    <x-data-table search-placeholder="Rechercher un enseignant..." :count="4">
        <x-slot:head>
            <th>Enseignant</th><th>Grade</th><th>Service</th><th>Réalisé</th><th>Heures compl.</th><th>Taux</th><th>Montant estimé</th>
        </x-slot:head>
        @php
            $hc = [
                ['Konan Kouassi', 'Professeur', '192h', '228h', '36h', '25 000', '900 000'],
                ['Sarah Koné', 'Assistant', '192h', '208h', '16h', '15 000', '240 000'],
                ['Fatou Ouattara', 'Maître-Assistant', '192h', '215h', '23h', '18 000', '414 000'],
                ['Charles N\'Guessan', 'Professeur', '192h', '208h', '16h', '25 000', '400 000'],
            ];
        @endphp
        @foreach($hc as [$ens, $grade, $serv, $real, $h, $taux, $mnt])
            <tr>
                <td class="fw-semibold">{{ $ens }}</td>
                <td>{{ $grade }}</td><td>{{ $serv }}</td><td>{{ $real }}</td>
                <td><span class="badge badge-soft-amber">{{ $h }}</span></td>
                <td>{{ $taux }} FCFA</td>
                <td class="fw-semibold text-uvci-green">{{ $mnt }} FCFA</td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
