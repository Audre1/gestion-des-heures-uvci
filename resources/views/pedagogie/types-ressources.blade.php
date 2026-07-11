<x-app-page title="Types de ressources" section="Gestion pédagogique" icon="fa-solid fa-shapes"
    subtitle="Catégories de ressources pédagogiques numériques.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addTypeModal">
            <i class="fa-solid fa-plus me-1"></i> Nouveau type
        </button>
    </x-slot:actions>

    <div class="row g-3">
        @forelse($types as $type)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="mx-auto mb-2">
                            <i class="fa-solid fa-shapes fa-2x" style="color: #6f42c1;"></i>
                        </div>
                        <div class="fw-semibold">{{ $type->libelle }}</div>
                        <div class="text-muted small mt-1">{{ $type->ressources_pedagogiques_count }} ressource(s)</div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-light border" title="Modifier"
                            data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        @if ($type->ressources_pedagogiques_count == 0)
                            <button type="button" class="btn btn-sm btn-light border" title="Supprimer"
                                data-bs-toggle="modal" data-bs-target="#deleteTypeModal{{ $type->id }}">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-light border" disabled
                                title="Impossible de supprimer (ressources associées)">
                                <i class="fa-solid fa-trash text-muted"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-shapes fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun type de ressource trouvé.</p>
                        <small>Commencez par créer un nouveau type de ressource.</small>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addTypeModal" tabindex="-1" aria-labelledby="addTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addTypeModalLabel">
                            <i class="fa-solid fa-shapes me-2 text-primary"></i>
                            Nouveau type de ressource
                        </h5>
                        <small class="text-muted">Créer une nouvelle catégorie de ressource.</small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('types.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                            <input type="text" name="libelle"
                                class="form-control @error('libelle') is-invalid @enderror" value="{{ old('libelle') }}"
                                maxlength="100" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque type --}}
    @foreach ($types as $type)
        {{-- Modale : modification --}}
        <div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1"
            aria-labelledby="editTypeModalLabel{{ $type->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editTypeModalLabel{{ $type->id }}">
                                <i class="fa-solid fa-shapes me-2 text-primary"></i>
                                Modifier le type de ressource
                            </h5>
                            <small class="text-muted">
                                {{ $type->libelle }}
                            </small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('types.update', $type->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                                <input type="text" name="libelle"
                                    class="form-control @error('libelle') is-invalid @enderror"
                                    value="{{ old('libelle', $type->libelle) }}" maxlength="100" required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
        @if ($type->ressources_pedagogiques_count == 0)
            <div class="modal fade" id="deleteTypeModal{{ $type->id }}" tabindex="-1"
                aria-labelledby="deleteTypeModalLabel{{ $type->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteTypeModalLabel{{ $type->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment supprimer ce type de ressource ?</p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $type->libelle }}</strong>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <form action="{{ route('types.destroy', $type->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button type="button" class="btn btn-light border me-2"
                                    data-bs-dismiss="modal">Annuler</button>

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
                var addType = new bootstrap.Modal(document.getElementById('addTypeModal'));
                addType.show();
            });
        </script>
    @endif
</x-app-page>
