<x-app-page title="Mes heures complémentaires" section="Espace Enseignant" icon="fa-solid fa-hourglass-end"
    subtitle="Détail de vos heures effectuées au-delà du service statutaire.">

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>

            <div> <div class="stat-value">{{ $heuresComplementaires }} h</div>   <div class="stat-label">Total heures compl.</div></div>

        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>

            <div>  <div class="stat-value">{{ number_format($tauxHoraire->montant, 0, ',', ' ') }}</div><div class="stat-label">Taux horaire (FCFA)</div></div>

        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon purple"><i class="fa-solid fa-sack-dollar"></i></div>
            <div> <div class="stat-value">{{ number_format($montantEstime, 0, ',', ' ') }}</div> <div class="stat-label">Montant estimé (FCFA)</div></div>
        </div></div></div>
    </div>

    <x-data-table search-placeholder="Rechercher..." :count="3" :show-filters="false">
        <x-slot:head>
            <th>Période</th><th>Cours concerné</th><th>Heures</th><th>Taux</th><th>Montant</th>
        </x-slot:head>

       
        @foreach($activites as $activite)
 <tr>
    <td class="fw-semibold"> {{ $activite->date_activite->format('F Y') }}</td>
    <td>{{ $activite->affectationCours->cours->intitule }}</td>
    <td>
    <span class="badge badge-soft-amber">
        {{ $activite->heures_complementaires }} h
    </span>
     </td>
    <td> {{ number_format($tauxHoraire->montant, 0, ',', ' ') }} FCFA</td>
    <td class="fw-semibold text-uvci-green"> {{ number_format($activite->heures_complementaires * $tauxHoraire->montant, 0, ',', ' ') }} FCFA</td>
 </tr>
        @endforeach
    </x-data-table>
</x-app-page>
