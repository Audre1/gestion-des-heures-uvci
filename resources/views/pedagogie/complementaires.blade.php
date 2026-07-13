<x-app-page title="Heures complémentaires" section="Volumes & Paiements" icon="fa-solid fa-clock"
    subtitle="Heures effectuées au-delà du service statutaire pour les enseignants permanents.">

    <x-slot:actions>
        <form action="{{ route('complementaires.index') }}" method="GET" class="d-flex gap-2">
            <select name="annee_id" class="form-select form-select-sm" style="width: 200px;">
                <option value="">Toutes les années</option>
                @foreach ($annees as $annee)
                    <option value="{{ $annee->id }}" {{ $anneeId == $annee->id ? 'selected' : '' }}>
                        {{ $annee->libelle }}
                        @if ($anneeActive && $annee->id == $anneeActive->id) (Active) @endif
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-uvci">
                <i class="fa-solid fa-filter me-1"></i> Filtrer
            </button>
        </form>
    </x-slot:actions>

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
            <div><div class="stat-value">{{ number_format($totalHeures, 0, ',', ' ') }} h</div><div class="stat-label">Total heures complémentaires</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon purple"><i class="fa-solid fa-users"></i></div>
            <div><div class="stat-value">{{ $complementaires->count() }}</div><div class="stat-label">Enseignants concernés</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon green"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div><div class="stat-value">{{ number_format($totalMontant, 0, ',', ' ') }} FCFA</div><div class="stat-label">Montant estimé</div></div>
        </div></div></div>
    </div>

    <x-data-table search-placeholder="Rechercher un enseignant..." :count="$complementaires->count()">
        <x-slot:head>
            <th>Enseignant</th>
            <th>Grade</th>
            <th>Service statutaire</th>
            <th>VHT réalisé</th>
            <th>Heures compl.</th>
            <th>Taux horaire</th>
            <th>Source taux</th>
            <th>Montant estimé</th>
        </x-slot:head>
        @forelse($complementaires as $comp)
            <tr>
                <td class="fw-semibold">
                    {{ $comp['enseignant']->utilisateur->nom }}
                    {{ $comp['enseignant']->utilisateur->prenom }}
                </td>
                <td>{{ $comp['grade'] }}</td>
                <td>{{ $comp['service_statutaire'] }}h</td>
                <td>{{ $comp['vht_realise'] }}h</td>
                <td>
                    <span class="badge badge-soft-amber">{{ $comp['heures_complementaires'] }}h</span>
                </td>
                <td>{{ number_format($comp['taux_horaire'], 0, ',', ' ') }} FCFA</td>
                <td>
                    @if($comp['taux_source'] === 'Personnel')
                        <span class="badge bg-info text-white">Personnel</span>
                    @else
                        <span class="badge bg-secondary text-white">Grade</span>
                    @endif
                </td>
                <td class="fw-semibold text-uvci-green">{{ number_format($comp['montant'], 0, ',', ' ') }} FCFA</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-clock fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune heure complémentaire enregistrée.</p>
                        <small>Les enseignants permanents n'ont pas dépassé leur service statutaire.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>
</x-app-page>
