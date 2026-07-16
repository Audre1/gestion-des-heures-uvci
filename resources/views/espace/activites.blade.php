<x-app-page title="Mes activités" section="Espace Enseignant" icon="fa-solid fa-folder-open"
    subtitle="Historique de vos activités pédagogiques (lecture seule).">





    <x-data-table search-placeholder="Rechercher une activité..." :count="$activites->count()" :show-filters="true"
        export-title="Mes activités pédagogiques">
        <x-slot:filters>
            <label class="dt-filter-label">Type</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(1)">
                <option value="">Tous</option>
                <option value="creation">Création</option>
                <option value="maj">Mise à jour</option>
            </select>
            <label class="dt-filter-label">Niveau</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($activites)->pluck('niveauComplexite.libelle')->unique()->filter()->sort()->values() as $n)
                    <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Cours</th>
            <th>Type</th>
            <th>Niveau (affectation)</th>
            <th>Semestre</th>
            <th>Niveau (complexité)</th>
            <th>Séq.</th>
            <th>VHT</th>
            <th>Année</th>
            <th>Date</th>
            <th>Statut</th>
            <th class="text-end">Détail</th>
        </x-slot:head>


        @if ($activites->isEmpty())
            <tr>
                <td colspan="11" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune activité pédagogique n'est disponible pour le moment.</p>
                    </div>
                </td>
            </tr>
        @endif



        @foreach ($activites as $activite)
            <tr>
                <td class="fw-semibold">
                    {{ $activite->affectationCours->cours->code_cours }} —
                    {{ $activite->affectationCours->cours->intitule }}
                </td>

                <td>
                    <span class="badge badge-soft-green">
                        {{ $activite->type_activite }}
                    </span>
                </td>

                <td>
                    <span class="badge badge-soft-gray">
                        {{ $activite->affectationCours->niveau ?? '-' }}
                    </span>
                </td>

                <td>
                    <span class="badge badge-soft-purple">
                        {{ $activite->affectationCours->semestre ?? '-' }}
                    </span>
                </td>

                <td>
                    {{ $activite->niveauComplexite->libelle ?? '-' }}
                </td>

                <td>{{ $activite->nb_sequences }}</td>

                <td class="fw-semibold text-uvci-green">
                    {{ $activite->volume_horaire }}h
                </td>

                <td>
                    <span class="badge badge-soft-blue">
                        {{ $activite->affectationCours->anneeAcademique->libelle ?? '-' }}
                    </span>
                </td>

                <td class="text-muted">
                    {{ $activite->date_activite->format('d/m/Y') }}
                </td>

                <td>
                    <span class="badge badge-soft-green">
                        {{ ucfirst(str_replace('_', ' ', $activite->statut)) }}
                    </span>
                </td>

                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal"
                        data-bs-target="#viewActiviteModal{{ $activite->id }}">
                        <i class="fa-solid fa-eye text-muted"></i>
                    </button>
                </td>
            </tr>
        @endforeach

    </x-data-table>

    {{-- Modales individuelles pour chaque activité --}}
    @foreach ($activites as $activite)
        {{-- Modale : détails --}}
        <div class="modal fade" id="viewActiviteModal{{ $activite->id }}" tabindex="-1"
            aria-labelledby="viewActiviteModalLabel{{ $activite->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="viewActiviteModalLabel{{ $activite->id }}">
                                <i class="fa-solid fa-eye me-2 text-primary"></i>
                                Détails de l'activité
                            </h5>
                            <small class="text-muted">
                                {{ $activite->affectationCours->cours->code_cours }} —
                                {{ $activite->affectationCours->cours->intitule }}
                            </small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Type d'activité</label>
                                <div>
                                    <span class="badge badge-soft-green">
                                        {{ $activite->type_activite }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Statut</label>
                                <div>
                                    <span class="badge badge-soft-green">
                                        {{ ucfirst(str_replace('_', ' ', $activite->statut)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Niveau (affectation)</label>
                                <div>
                                    <span class="badge badge-soft-gray">
                                        {{ $activite->affectationCours->niveau ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Semestre</label>
                                <div>
                                    <span class="badge badge-soft-purple">
                                        {{ $activite->affectationCours->semestre ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Niveau de complexité</label>
                                <div>{{ $activite->niveauComplexite->libelle ?? '-' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Nombre de séquences</label>
                                <div>{{ $activite->nb_sequences }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Volume horaire</label>
                                <div class="fw-semibold text-uvci-green">{{ $activite->volume_horaire }}h</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Coefficient</label>
                                <div>{{ $activite->coefficient }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Année académique</label>
                                <div>
                                    <span class="badge badge-soft-blue">
                                        {{ $activite->affectationCours->anneeAcademique->libelle ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Date de l'activité</label>
                                <div>{{ $activite->date_activite->format('d/m/Y') }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted small mb-1">Cours</label>
                                <div class="fw-semibold">
                                    {{ $activite->affectationCours->cours->code_cours }} —
                                    {{ $activite->affectationCours->cours->intitule }}
                                </div>
                            </div>

                            @if ($activite->ressourcePedagogique)
                                <div class="col-12">
                                    <label class="form-label text-muted small mb-1">Ressource pédagogique</label>
                                    <div>{{ $activite->ressourcePedagogique->titre }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-app-page>
