<x-app-page title="Cours" section="Gestion pédagogique" icon="fa-solid fa-book"
    subtitle="Catalogue des cours, crédits et volumes horaires.">

    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addCoursModal">
            <i class="fa-solid fa-plus me-1"></i>
            Nouveau cours
        </button>
    </x-slot:actions>

    @php
        // Extraire le paramètre highlight de l'URL
$highlightParam = request()->get('highlight');
$highlightId = null;
if ($highlightParam && str_contains($highlightParam, 'cours:')) {
    $highlightId = str_replace('cours:', '', $highlightParam);
        }
    @endphp

    <x-data-table search-placeholder="Rechercher un cours..." :count="$cours->count()" export-title="Liste des cours">
        <x-slot:filters>
            <label class="dt-filter-label">Crédits</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(3)">
                <option value="">Tous</option>
                @foreach (collect($cours)->pluck('nombre_credits')->unique()->filter()->sort()->values() as $c)
                    <option value="{{ $c }}">{{ $c }} crédits</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Code</th>
            <th>Intitulé</th>
            <th>Heures</th>
            <th>Crédits</th>
            <th class="text-end">Actions</th>
        </x-slot:head>


        @forelse($cours as $cour)
            <tr class="{{ $highlightId == $cour->id ? 'highlight-row' : '' }}">
                <td class="font-monospace fw-semibold text-uvci-purple">{{ $cour->code_cours }}</td>
                <td>{{ $cour->intitule }}</td>
                <td>{{ $cour->nombre_heures }}h</td>
                <td><span class="badge badge-soft-green">{{ $cour->nombre_credits }} cr.</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Gérer les séquences"
                            data-bs-toggle="modal" data-bs-target="#sequencesCoursModal{{ $cour->id }}">
                            <i class="fa-solid fa-layer-group text-uvci-purple"></i>
                        </button>

                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editCoursModal{{ $cour->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        @if ($cour->sequences_pedagogiques_count == 0 && $cour->affectations_cours_count == 0)
                            <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                                data-bs-target="#deleteCoursModal{{ $cour->id }}">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-light border" disabled
                                title="Impossible de supprimer (séquences ou affectations associées)">
                                <i class="fa-solid fa-trash text-muted"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-book fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun cours trouvé.</p>
                        <small>Commencez par ajouter un nouveau cours.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addCoursModal" tabindex="-1" aria-labelledby="addCoursModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addCoursModalLabel">
                            <i class="fa-solid fa-book-medical me-2 text-primary"></i>
                            Nouveau cours
                        </h5>
                        <small class="text-muted">Ajoutez un cours au catalogue pédagogique.</small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('cours.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code_cours"
                                    class="form-control @error('code_cours') is-invalid @enderror"
                                    value="{{ old('code_cours') }}" placeholder="Ex : INF-101" required>
                                @error('code_cours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Intitulé <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="intitule"
                                    class="form-control @error('intitule') is-invalid @enderror"
                                    value="{{ old('intitule') }}" placeholder="Ex : Algorithmique et programmation"
                                    required>
                                @error('intitule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Volume horaire <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="nombre_heures" id="nombre_heures"
                                        class="form-control @error('nombre_heures') is-invalid @enderror"
                                        placeholder="Ex : 20" min="1" step="1" required>
                                    <span class="input-group-text">heures</span>
                                </div>
                                @error('nombre_heures')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Crédits <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="nombre_credits" id="nombre_credits"
                                    class="form-control @error('nombre_credits') is-invalid @enderror"
                                    placeholder="Calculé automatiquement" min="0" step="1" readonly>
                                @error('nombre_credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Les séquences pédagogiques pourront être ajoutées après la création du cours.
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque cours --}}
    @foreach ($cours as $cour)
        {{-- Modale : gestion des séquences --}}
        <div class="modal fade" id="sequencesCoursModal{{ $cour->id }}" tabindex="-1"
            aria-labelledby="sequencesCoursModalLabel{{ $cour->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="sequencesCoursModalLabel{{ $cour->id }}">
                                <i class="fa-solid fa-layer-group me-2 text-uvci-purple"></i>
                                Séquences du cours
                            </h5>
                            <small class="text-muted">
                                {{ $cour->code_cours }} — {{ $cour->intitule }}
                            </small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="table-responsive border rounded">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>N°</th>
                                        <th>Titre</th>
                                        <th>Ressources</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($cour->sequencesPedagogiques->count() > 0)
                                        @foreach ($cour->sequencesPedagogiques as $sequence)
                                            <tr>
                                                <td><span
                                                        class="badge badge-soft-purple">{{ $sequence->numero_ordre }}</span>
                                                </td>
                                                <td class="fw-semibold">{{ $sequence->titre }}</td>
                                                <td>{{ $sequence->ressources_pedagogiques_count }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">
                                                Aucune séquence pédagogique pour ce cours.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Pour gérer les séquences pédagogiques, utilisez la section "Séquences pédagogiques" du menu.
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

        {{-- Modale : modification --}}
        <div class="modal fade" id="editCoursModal{{ $cour->id }}" tabindex="-1"
            aria-labelledby="editCoursModalLabel{{ $cour->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editCoursModalLabel{{ $cour->id }}">
                                <i class="fa-solid fa-book-open me-2 text-primary"></i>
                                Modifier le cours
                            </h5>
                            <small class="text-muted">{{ $cour->code_cours }} — {{ $cour->intitule }}</small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('cours.update', $cour->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code_cours"
                                        class="form-control @error('code_cours') is-invalid @enderror"
                                        value="{{ old('code_cours', $cour->code_cours) }}" required>
                                    @error('code_cours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Intitulé <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="intitule"
                                        class="form-control @error('intitule') is-invalid @enderror"
                                        value="{{ old('intitule', $cour->intitule) }}" required>
                                    @error('intitule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Volume horaire <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="nombre_heures"
                                            id="nombre_heures_edit_{{ $cour->id }}"
                                            class="form-control @error('nombre_heures') is-invalid @enderror"
                                            value="{{ old('nombre_heures', $cour->nombre_heures) }}" min="1"
                                            step="1" required>
                                        <span class="input-group-text">heures</span>
                                    </div>
                                    @error('nombre_heures')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Crédits <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="nombre_credits"
                                        id="nombre_credits_edit_{{ $cour->id }}"
                                        class="form-control @error('nombre_credits') is-invalid @enderror"
                                        value="{{ old('nombre_credits', $cour->nombre_credits) }}" min="0"
                                        step="1" readonly>
                                    @error('nombre_credits')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-uvci">
                                <i class="fa-solid fa-floppy-disk me-1"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modale : suppression --}}
        @if ($cour->sequences_pedagogiques_count == 0 && $cour->affectations_cours_count == 0)
            <div class="modal fade" id="deleteCoursModal{{ $cour->id }}" tabindex="-1"
                aria-labelledby="deleteCoursModalLabel{{ $cour->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteCoursModalLabel{{ $cour->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment supprimer ce cours ?</p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $cour->code_cours }} — {{ $cour->intitule }}</strong><br>
                                <span class="small">{{ $cour->nombre_heures }}h — {{ $cour->nombre_credits }}
                                    crédit(s)</span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>

                            <form action="{{ route('cours.destroy', $cour->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    <i class="fa-solid fa-trash me-1"></i>
                                    Oui, supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addCours = new bootstrap.Modal(document.getElementById('addCoursModal'));
                addCours.show();
            });
        </script>
    @endif

    <script>
        const heuresParCredit = 10;

        // Formulaire de création
        if (document.getElementById('nombre_heures')) {
            document.getElementById('nombre_heures').addEventListener('input', function() {
                const heures = parseInt(this.value) || 0;
                document.getElementById('nombre_credits').value = Math.round(heures / heuresParCredit);
            });
        }

        // Formulaires de modification (un pour chaque cours)
        @foreach ($cours as $cour)
            if (document.getElementById('nombre_heures_edit_{{ $cour->id }}')) {
                document.getElementById('nombre_heures_edit_{{ $cour->id }}').addEventListener('input', function() {
                    const heures = parseInt(this.value) || 0;
                    document.getElementById('nombre_credits_edit_{{ $cour->id }}').value = Math.round(heures /
                        heuresParCredit);
                });
            }
        @endforeach
    </script>
</x-app-page>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Si un élément est highlighté, faire défiler jusqu'à lui
            const highlightedRow = document.querySelector('.highlight-row');
            if (highlightedRow) {
                highlightedRow.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Nettoyer l'URL après 3 secondes
                setTimeout(() => {
                    const url = new URL(window.location);
                    url.searchParams.delete('highlight');
                    window.history.replaceState({}, '', url);
                }, 3000);
            }
        });
    </script>
@endpush
