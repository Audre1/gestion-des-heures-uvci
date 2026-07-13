<x-app-page title="Filières" section="Gestion pédagogique" icon="fa-solid fa-sitemap"
    subtitle="Filières rattachées aux départements.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addFiliereModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvelle filière
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une filière..." :count="$filieres->count()">
        <x-slot:filters>
            <label class="dt-filter-label">Département</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($filieres)->pluck('departement.nom_departement')->unique()->filter()->sort()->values() as $d)
                    <option value="{{ $d }}">{{ $d }}</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Code</th>
            <th>Filière</th>
            <th>Département</th>
            <th>Cours</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @forelse($filieres as $filiere)
            <tr>
                <td class="font-monospace fw-semibold text-uvci-purple">{{ $filiere->code_filiere }}</td>
                <td class="fw-semibold"><i
                        class="fa-solid fa-diagram-project text-uvci-purple me-2"></i>{{ $filiere->nom_filiere }}</td>
                <td><span class="badge badge-soft-gray">{{ $filiere->departement->nom_departement ?? '-' }}</span></td>
                <td>{{ $filiere->cours_count }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Gérer les cours"
                            data-bs-toggle="modal" data-bs-target="#coursFiliereModal{{ $filiere->id }}">
                            <i class="fa-solid fa-book text-uvci-purple"></i>
                        </button>
                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editFiliereModal{{ $filiere->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        @if ($filiere->cours_count == 0)
                            <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                                data-bs-target="#deleteFiliereModal{{ $filiere->id }}">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-light border" disabled
                                title="Impossible de supprimer (cours associés)">
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
                        <i class="fa-solid fa-sitemap fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune filière trouvée.</p>
                        <small>Commencez par ajouter une nouvelle filière.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addFiliereModal" tabindex="-1" aria-labelledby="addFiliereModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addFiliereModalLabel">
                            <i class="fa-solid fa-diagram-project me-2 text-primary"></i>
                            Nouvelle filière
                        </h5>
                        <small class="text-muted">Ajoutez une filière à un département.</small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('filieres.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code_filiere"
                                    class="form-control @error('code_filiere') is-invalid @enderror"
                                    value="{{ old('code_filiere') }}" placeholder="Ex : INFO-L1" required>
                                @error('code_filiere')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Nom de la filière <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nom_filiere"
                                    class="form-control @error('nom_filiere') is-invalid @enderror"
                                    value="{{ old('nom_filiere') }}" placeholder="Ex : Licence Informatique" required>
                                @error('nom_filiere')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Département <span
                                        class="text-danger">*</span></label>
                                <select name="id_departement"
                                    class="form-select @error('id_departement') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un département --</option>
                                    @foreach (\App\Models\Departement::all() as $departement)
                                        <option value="{{ $departement->id }}">{{ $departement->nom_departement }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_departement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer la filière
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque filière --}}
    @foreach ($filieres as $filiere)
        {{-- Modale : gestion des cours de la filière --}}
        <div class="modal fade" id="coursFiliereModal{{ $filiere->id }}" tabindex="-1"
            aria-labelledby="coursFiliereModalLabel{{ $filiere->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="coursFiliereModalLabel{{ $filiere->id }}">
                                <i class="fa-solid fa-book me-2 text-uvci-purple"></i>
                                Cours de la filière
                            </h5>
                            <small class="text-muted">
                                {{ $filiere->nom_filiere }}
                            </small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span
                                    class="badge badge-soft-gray">{{ $filiere->departement->nom_departement ?? '-' }}</span>
                            </div>

                            <button type="button" class="btn btn-sm btn-uvci" data-bs-toggle="modal"
                                data-bs-target="#addCoursFiliereModal{{ $filiere->id }}">
                                <i class="fa-solid fa-plus me-1"></i>
                                Associer un cours
                            </button>
                        </div>

                        <div class="table-responsive border rounded">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Intitulé</th>
                                        <th>Semestre</th>
                                        <th>Niveau</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($filiere->cours->count() > 0)
                                        @foreach ($filiere->cours as $cours)
                                            <tr>
                                                <td class="font-monospace fw-semibold">{{ $cours->code_cours }}
                                                </td>
                                                <td>{{ $cours->intitule }}</td>
                                                <td><span
                                                        class="badge badge-soft-purple">{{ $cours->pivot->semestre }}</span>
                                                </td>
                                                <td><span
                                                        class="badge badge-soft-gray">{{ $cours->pivot->niveau }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <form
                                                        action="{{ route('filieres.detach-cours', [$filiere->id, $cours->id, $cours->pivot->semestre, $cours->pivot->niveau]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light border"
                                                            title="Supprimer">
                                                            <i class="fa-solid fa-trash text-danger"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-muted">
                                                Aucun cours associé à cette filière.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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

        {{-- Modale : associer un cours à une filière --}}
        <div class="modal fade" id="addCoursFiliereModal{{ $filiere->id }}" tabindex="-1"
            aria-labelledby="addCoursFiliereModalLabel{{ $filiere->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="addCoursFiliereModalLabel{{ $filiere->id }}">
                                <i class="fa-solid fa-link me-2 text-primary"></i>
                                Associer un cours
                            </h5>
                            <small class="text-muted">{{ $filiere->nom_filiere }}</small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('filieres.attach-cours', $filiere->id) }}" method="POST">
                        @csrf

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Cours <span
                                            class="text-danger">*</span></label>
                                    <select name="id_cours" class="form-select" required>
                                        <option value="">-- Sélectionner un cours --</option>
                                        @foreach (\App\Models\Cours::all() as $cours)
                                            <option value="{{ $cours->id }}">{{ $cours->code_cours }} -
                                                {{ $cours->intitule }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Semestre <span
                                            class="text-danger">*</span></label>
                                    <select name="semestre" class="form-select" required>
                                        <option value="">--</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Niveau <span
                                            class="text-danger">*</span></label>
                                    <select name="niveau" class="form-select" required>
                                        <option value="">--</option>
                                        <option value="L1">L1</option>
                                        <option value="L2">L2</option>
                                        <option value="L3">L3</option>
                                        <option value="M1">M1</option>
                                        <option value="M2">M2</option>
                                    </select>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4 mb-0">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Un cours ne peut être associé qu'une seule fois à une filière (peu importe le semestre/niveau).
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>

                            <button type="submit" class="btn btn-uvci">
                                <i class="fa-solid fa-link me-1"></i>
                                Associer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modale : modification --}}
        <div class="modal fade" id="editFiliereModal{{ $filiere->id }}" tabindex="-1"
            aria-labelledby="editFiliereModalLabel{{ $filiere->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editFiliereModalLabel{{ $filiere->id }}">
                                <i class="fa-solid fa-diagram-project me-2 text-primary"></i>
                                Modifier la filière
                            </h5>
                            <small class="text-muted">{{ $filiere->code_filiere }} —
                                {{ $filiere->nom_filiere }}</small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('filieres.update', $filiere->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code_filiere"
                                        class="form-control @error('code_filiere') is-invalid @enderror"
                                        value="{{ old('code_filiere', $filiere->code_filiere) }}" required>
                                    @error('code_filiere')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Nom de la filière <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nom_filiere"
                                        class="form-control @error('nom_filiere') is-invalid @enderror"
                                        value="{{ old('nom_filiere', $filiere->nom_filiere) }}" required>
                                    @error('nom_filiere')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Département <span
                                            class="text-danger">*</span></label>
                                    <select name="id_departement"
                                        class="form-select @error('id_departement') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner un département --</option>
                                        @foreach (\App\Models\Departement::all() as $departement)
                                            <option value="{{ $departement->id }}"
                                                {{ $filiere->id_departement == $departement->id ? 'selected' : '' }}>
                                                {{ $departement->nom_departement }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_departement')
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
        @if ($filiere->cours_count == 0)
            <div class="modal fade" id="deleteFiliereModal{{ $filiere->id }}" tabindex="-1"
                aria-labelledby="deleteFiliereModalLabel{{ $filiere->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteFiliereModalLabel{{ $filiere->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment supprimer cette filière ?</p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $filiere->code_filiere }} — {{ $filiere->nom_filiere }}</strong><br>
                                <span class="small">{{ $filiere->departement->nom_departement ?? '-' }} —
                                    {{ $filiere->cours_count }} cours</span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>

                            <form action="{{ route('filieres.destroy', $filiere->id) }}" method="POST">
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
                var addFiliere = new bootstrap.Modal(document.getElementById('addFiliereModal'));
                addFiliere.show();
            });
        </script>
    @endif
</x-app-page>
