<x-app-page title="Mon tableau de bord" section="Général"
    subtitle="Vue d'ensemble de vos activités — {{ $currentYear?->libelle ?? 'Année en cours' }}.">

    {{-- Cartes statistiques personnelles --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon green"><i class="fa-solid fa-book"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['cours_assignes'] }}</div>
                        <div class="stat-label">Cours assignés</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon purple"><i class="fa-solid fa-stopwatch"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['volume_realise'] }}h</div>
                        <div class="stat-label">Volume réalisé</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon blue"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['volume_total'] }}h</div>
                        <div class="stat-label">Volume total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['heures_complementaires'] }}h</div>
                        <div class="stat-label">Heures compl.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Barre de progression --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold"><i class="fa-solid fa-chart-line text-uvci-green me-2"></i>Progression
                            du service statutaire ({{ $serviceStatutaire }}h)</span>
                        <span class="badge badge-soft-green fs-6">{{ $stats['taux_realisation'] }}%</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ min($stats['taux_realisation'], 100) }}%;"
                            aria-valuenow="{{ $stats['taux_realisation'] }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>{{ $stats['volume_realise'] }}h réalisés</span>
                        <span>{{ $stats['volume_total'] }}h prévus</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Activités récentes --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-list-check text-uvci-green me-2"></i>Mes dernières activités</span>
                    <a href="{{ route('espace.activites') }}" class="small text-uvci-purple fw-semibold">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Cours</th>
                                <th>Type</th>
                                <th>Vol.</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activitesRecentes as $a)
                                <tr>
                                    <td>{{ $a['date'] }}</td>
                                    <td>{{ $a['cours'] }}</td>
                                    <td>{{ $a['type'] }}</td>
                                    <td class="fw-semibold">{{ $a['volume'] }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ $a['badge'] }}">{{ ucfirst(str_replace('_', ' ', $a['statut'])) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-inbox fa-2x mb-2"></i><br>
                                        Aucune activité enregistrée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Récapitulatif et actions rapides --}}
        <div class="col-lg-5">
            {{-- Résumé --}}
            <div class="card mb-3">
                <div class="card-header"><i class="fa-solid fa-chart-simple text-uvci-purple me-2"></i>Récapitulatif
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Activités validées</span>
                        <span class="fw-semibold badge-soft-green badge">{{ $stats['activites_validees'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Activités en cours</span>
                        <span class="fw-semibold badge-soft-amber badge">{{ $stats['activites_en_cours'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Activités rejetées</span>
                        <span class="fw-semibold badge-soft-red badge">{{ $stats['activites_rejetees'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Heures complémentaires</span>
                        <span class="fw-semibold badge-soft-red badge">{{ $stats['heures_complementaires'] }}h</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Service statutaire</span>
                        <span class="fw-semibold">{{ $serviceStatutaire }}h</span>
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-bolt text-uvci-amber me-2"></i>Actions rapides</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('espace.activites') }}" class="btn btn-uvci">
                            <i class="fa-solid fa-plus me-1"></i> Ajouter une activité
                        </a>
                        <a href="{{ route('espace.volume') }}" class="btn btn-light border">
                            <i class="fa-solid fa-stopwatch me-1"></i> Voir mon volume horaire
                        </a>
                        <a href="{{ route('espace.complementaires') }}" class="btn btn-light border">
                            <i class="fa-solid fa-clock me-1"></i> Heures complémentaires
                        </a>
                        <a href="{{ route('espace.ressources') }}" class="btn btn-light border">
                            <i class="fa-solid fa-book-open me-1"></i> Mes ressources
                        </a>
                        <a href="{{ route('espace.documents') }}" class="btn btn-light border">
                            <i class="fa-solid fa-download me-1"></i> Mes documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-page>
