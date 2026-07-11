<x-app-page title="Départements" section="Gestion pédagogique" icon="fa-solid fa-building-columns"
    subtitle="Structuration académique de l'université.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addDepartementModal">
            <i class="fa-solid fa-plus me-1"></i> Nouveau département
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un département..." :count="$departements->count()">
        <x-slot:head>
            <th>Code</th>
            <th>Département</th>
            <th>Filières</th>
            <th>Enseignants</th>
            <th class="text-end">Actions</th>
        </x-slot:head>

        @forelse($departements as $departement)
            <tr>
                <td class="font-monospace fw-semibold text-uvci-purple">{{ $departement->code_departement }}</td>
                <td class="fw-semibold"><i
                        class="fa-solid fa-building-columns text-uvci-green me-2"></i>{{ $departement->nom_departement }}
                </td>
                <td>{{ $departement->filieres_count }}</td>
                <td>{{ $departement->enseignants_count }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editDepartementModal{{ $departement->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        @if ($departement->enseignants_count == 0 && $departement->filieres_count == 0)
                            <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                                data-bs-target="#deleteDepartementModal{{ $departement->id }}">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-light border" disabled
                                title="Impossible de supprimer (enseignants ou filières associés)">
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
                        <i class="fa-solid fa-building-columns fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun département trouvé.</p>
                        <small>Commencez par ajouter un nouveau département.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addDepartementModal" tabindex="-1" aria-labelledby="addDepartementModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addDepartementModalLabel">
                            <i class="fa-solid fa-building-columns me-2 text-primary"></i>
                            Nouveau département
                        </h5>
                        <small class="text-muted">Ajoutez un département à l'université.</small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('departements.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code_departement"
                                    class="form-control @error('code_departement') is-invalid @enderror"
                                    value="{{ old('code_departement') }}" placeholder="Ex : INFO" required>
                                @error('code_departement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Nom du département <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nom_departement"
                                    class="form-control @error('nom_departement') is-invalid @enderror"
                                    value="{{ old('nom_departement') }}" placeholder="Ex : Informatique" required>
                                @error('nom_departement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer le département
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque département --}}
    @foreach ($departements as $departement)
        {{-- Modale : modification --}}
        <div class="modal fade" id="editDepartementModal{{ $departement->id }}" tabindex="-1"
            aria-labelledby="editDepartementModalLabel{{ $departement->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editDepartementModalLabel{{ $departement->id }}">
                                <i class="fa-solid fa-building-columns me-2 text-primary"></i>
                                Modifier le département
                            </h5>
                            <small class="text-muted">{{ $departement->code_departement }} —
                                {{ $departement->nom_departement }}</small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('departements.update', $departement->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code_departement"
                                        class="form-control @error('code_departement') is-invalid @enderror"
                                        value="{{ old('code_departement', $departement->code_departement) }}"
                                        required>
                                    @error('code_departement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Nom du département <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nom_departement"
                                        class="form-control @error('nom_departement') is-invalid @enderror"
                                        value="{{ old('nom_departement', $departement->nom_departement) }}" required>
                                    @error('nom_departement')
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
        @if ($departement->enseignants_count == 0 && $departement->filieres_count == 0)
            <div class="modal fade" id="deleteDepartementModal{{ $departement->id }}" tabindex="-1"
                aria-labelledby="deleteDepartementModalLabel{{ $departement->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteDepartementModalLabel{{ $departement->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment supprimer ce département ?</p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $departement->code_departement }} —
                                    {{ $departement->nom_departement }}</strong><br>
                                <span class="small">{{ $departement->filieres_count }} filière(s) —
                                    {{ $departement->enseignants_count }} enseignant(s)</span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>

                            <form action="{{ route('departements.destroy', $departement->id) }}" method="POST">
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
                var addDepartement = new bootstrap.Modal(document.getElementById('addDepartementModal'));
                addDepartement.show();
            });
        </script>
    @endif
</x-app-page>
