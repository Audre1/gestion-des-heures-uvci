<x-app-page title="Volumes horaires" section="Volumes & Paiements" icon="fa-solid fa-hourglass-half"
    subtitle="Consultation et contrôle des charges horaires par enseignant.">

    <x-slot:actions>
        <form action="{{ route('volumes.index') }}" method="GET" class="d-flex gap-2">
            <select name="annee_id" class="form-select form-select-sm" style="width: 200px;">
                <option value="">Toutes les années</option>
                @foreach ($annees as $annee)
                    <option value="{{ $annee->id }}" {{ $anneeId == $annee->id ? 'selected' : '' }}>
                        {{ $annee->libelle }}
                        @if ($anneeActive && $annee->id == $anneeActive->id)
                            (Active)
                        @endif
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-uvci">
                <i class="fa-solid fa-filter me-1"></i> Filtrer
            </button>
        </form>
        <a href="{{ route('volumes.export', ['annee_id' => $anneeId]) }}" class="btn btn-sm btn-light border">
            <i class="fa-solid fa-download me-1"></i> Exporter
        </a>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un enseignant..." :count="$volumes->count()"
        export-title="Liste des volumes horaires">
        <x-slot:filters>
            <label class="dt-filter-label">Statut</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(1)">
                <option value="">Tous</option>
                <option value="Vacataire">Vacataire</option>
                <option value="Permanent">Permanent</option>
            </select>
            <label class="dt-filter-label">Grade</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($volumes)->pluck('grade')->unique()->filter()->sort()->values() as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Enseignant</th>
            <th>Statut</th>
            <th>Grade</th>
            <th>Service statutaire</th>
            <th>VHT réalisé</th>
            <th>Heures compl.</th>
            <th>Nb cours</th>
            <th>Charge</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @forelse($volumes as $volume)
            <tr>
                <td class="fw-semibold">
                    {{ $volume['enseignant']->utilisateur->nom }}
                    {{ $volume['enseignant']->utilisateur->prenom }}
                </td>
                <td>
                    @if ($volume['statut'] === 'Vacataire')
                        <span class="badge bg-info text-white">Vacataire</span>
                    @else
                        <span class="badge bg-success text-white">Permanent</span>
                    @endif
                </td>
                <td>{{ $volume['grade'] }}</td>
                <td>
                    @if ($volume['statut'] === 'Vacataire')
                        <span class="text-muted">—</span>
                    @else
                        {{ $volume['service_statutaire'] }}h
                    @endif
                </td>
                <td class="fw-semibold text-uvci-green">{{ $volume['vht_realise'] }}h</td>
                <td>
                    @if ($volume['statut'] === 'Vacataire')
                        <span class="text-muted">—</span>
                    @elseif($volume['heures_complementaires'] > 0)
                        <span class="badge badge-soft-amber">{{ $volume['heures_complementaires'] }}h</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $volume['nb_cours'] }}</td>
                <td style="min-width:140px">
                    @if ($volume['statut'] === 'Vacataire')
                        <span class="text-muted">N/A</span>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height:7px">
                                <div class="progress-bar"
                                    style="width:{{ min($volume['pourcentage'], 100) }}%;background:{{ $volume['pourcentage'] > 100 ? 'var(--uvci-purple)' : 'var(--uvci-green)' }}">
                                </div>
                            </div>
                            <small
                                class="fw-semibold {{ $volume['pourcentage'] > 100 ? 'text-uvci-purple' : 'text-muted' }}">{{ $volume['pourcentage'] }}%</small>
                        </div>
                    @endif
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-light border" title="Voir détails"
                        data-bs-toggle="modal" data-bs-target="#detailsModal{{ $volume['enseignant']->id }}">
                        <i class="fa-solid fa-eye text-uvci-green"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-hourglass-half fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune donnée de volume horaire trouvée.</p>
                        <small>Commencez par affecter des cours et valider des activités pédagogiques.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modales de détails pour chaque enseignant --}}
    @foreach ($volumes as $volume)
        <div class="modal fade" id="detailsModal{{ $volume['enseignant']->id }}" tabindex="-1"
            aria-labelledby="detailsModalLabel{{ $volume['enseignant']->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="detailsModalLabel{{ $volume['enseignant']->id }}">
                                <i class="fa-solid fa-user me-2 text-primary"></i>
                                {{ $volume['enseignant']->utilisateur->nom }}
                                {{ $volume['enseignant']->utilisateur->prenom }}
                            </h5>
                            <div class="d-flex gap-2 align-items-center">
                                <small class="text-muted">{{ $volume['grade'] }}</small>
                                @if ($volume['statut'] === 'Vacataire')
                                    <span class="badge bg-info text-white">Vacataire</span>
                                @else
                                    <span class="badge bg-success text-white">Permanent</span>
                                @endif
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            @if ($volume['statut'] !== 'Vacataire')
                                <div class="col-md-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body text-center">
                                            <h6 class="card-subtitle mb-2 text-muted">Service statutaire</h6>
                                            <h3 class="card-title fw-bold">{{ $volume['service_statutaire'] }}h</h3>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <div class="card bg-success bg-opacity-10 border-0">
                                    <div class="card-body text-center">
                                        <h6 class="card-subtitle mb-2 text-muted">VHT réalisé</h6>
                                        <h3 class="card-title fw-bold text-success">{{ $volume['vht_realise'] }}h</h3>
                                    </div>
                                </div>
                            </div>
                            @if ($volume['statut'] !== 'Vacataire')
                                <div class="col-md-3">
                                    <div class="card bg-warning bg-opacity-10 border-0">
                                        <div class="card-body text-center">
                                            <h6 class="card-subtitle mb-2 text-muted">Heures compl.</h6>
                                            <h3 class="card-title fw-bold text-warning">
                                                {{ $volume['heures_complementaires'] }}h</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info bg-opacity-10 border-0">
                                        <div class="card-body text-center">
                                            <h6 class="card-subtitle mb-2 text-muted">Charge</h6>
                                            <h3 class="card-title fw-bold text-info">{{ $volume['pourcentage'] }}%
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">Détail des cours et activités</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Cours</th>
                                        <th>Type</th>
                                        <th>Niveau</th>
                                        <th>VHT</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($volume['enseignant']->affectationsCours as $affectation)
                                        @foreach ($affectation->activitesPedagogiques as $activite)
                                            <tr>
                                                <td>{{ $affectation->cours->code_cours }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-soft-{{ $activite->type_activite === 'creation' ? 'green' : 'purple' }}">
                                                        {{ $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour' }}
                                                    </span>
                                                </td>
                                                <td>{{ $activite->niveauComplexite->libelle ?? 'N/A' }}</td>
                                                <td class="fw-semibold">{{ $activite->volume_horaire }}h</td>
                                                <td>{{ $activite->date_activite ? $activite->date_activite->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-soft-{{ $activite->statut === 'validee' ? 'green' : 'amber' }}">
                                                        {{ $activite->statut === 'validee' ? 'Validée' : 'En cours' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Aucune activité
                                                pédagogique</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-app-page>
