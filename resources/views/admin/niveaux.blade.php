<x-app-page title="Niveaux de complexité" section="Administration" icon="fa-solid fa-signal"
    subtitle="Classification déterminant le coefficient de calcul des heures.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fa-solid fa-plus me-1"></i> Nouveau niveau
        </button>
    </x-slot:actions>

    <div class="row g-3">
        @forelse($niveaux as $niveau)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="stat-icon purple"><i class="fa-solid fa-signal"></i></div>
                            <span class="badge badge-soft-purple">Coeff.
                                {{ number_format($niveau->coefficient, 2) }}</span>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $niveau->libelle }}</h5>
                        <p class="text-muted small mb-0">{{ $niveau->description ?? 'Aucune description' }}</p>
                        <div class="text-muted small mt-2">{{ $niveau->activites_pedagogiques_count }} activité(s)
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-uvci flex-fill" data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $niveau->id }}">
                            <i class="fa-solid fa-pen me-1"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger flex-fill" data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $niveau->id }}"
                            {{ $niveau->activites_pedagogiques_count > 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-trash me-1"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal Edit --}}
            <div class="modal fade" id="editModal{{ $niveau->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <div>
                                <h5 class="modal-title fw-bold" id="editNiveauModalLabel{{ $niveau->id }}">
                                    <i class="fa-solid fa-signal me-2 text-primary"></i>
                                    Modifier le niveau de complexité
                                </h5>
                                <small class="text-muted">Modification du niveau {{ $niveau->libelle }}.</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <form action="{{ route('niveaux.update', $niveau->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Libellé <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="libelle" class="form-control"
                                            value="{{ old('libelle', $niveau->libelle) }}" placeholder="Ex : Niveau 1"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Coefficient <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="coefficient" class="form-control"
                                            value="{{ old('coefficient', $niveau->coefficient) }}"
                                            placeholder="Ex : 0,40" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Description</label>
                                        <textarea name="description" class="form-control" rows="3" placeholder="Description du niveau de complexité...">{{ old('description', $niveau->description) }}</textarea>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4 mb-0">
                                    <i class="fa-solid fa-circle-info me-2"></i>
                                    Le coefficient est utilisé pour le calcul des volumes horaires (VHT).
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                    Annuler
                                </button>
                                <button type="submit" class="btn btn-uvci">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Delete --}}
            <div class="modal fade" id="deleteModal{{ $niveau->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Supprimer le niveau de complexité</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer le niveau <strong>{{ $niveau->libelle }}</strong>
                                ?
                            </p>
                            @if ($niveau->activites_pedagogiques_count > 0)
                                <p class="text-danger">Impossible de supprimer ce niveau car il a des activités
                                    pédagogiques associées.</p>
                            @endif
                        </div>
                        <div class="modal-footer bg-light">
                            <form action="{{ route('niveaux.destroy', $niveau->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button type="button" class="btn btn-light border me-2"
                                    data-bs-dismiss="modal">Annuler</button>

                                <button type="submit" class="btn btn-danger"
                                    {{ $niveau->activites_pedagogiques_count > 0 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-trash me-1"></i> Oui, supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-info-circle me-2 fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun niveau de complexité trouvé.</p>
                        <small>Cliquez sur "Nouveau niveau" pour en créer un.</small>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Modal Add --}}
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addNiveauModalLabel">
                            <i class="fa-solid fa-signal me-2 text-primary"></i>
                            Nouveau niveau de complexité
                        </h5>
                        <small class="text-muted">Création d'un nouveau niveau de complexité.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('niveaux.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Libellé <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="libelle" class="form-control"
                                    value="{{ old('libelle') }}" placeholder="Ex : Niveau 1" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Coefficient <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="coefficient" class="form-control"
                                    value="{{ old('coefficient') }}" placeholder="Ex : 0,40" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="Description du niveau de complexité...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Le coefficient est utilisé pour le calcul des volumes horaires (VHT).
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-page>
