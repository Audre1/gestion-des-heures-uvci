<x-app-page title="Affectations de cours" section="Gestion pédagogique" icon="fa-solid fa-link"
    subtitle="Attribution des cours aux enseignants par année académique.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addAffectationModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvelle affectation
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une affectation..." :count="$affectations->count()">
        <x-slot:head>
            <th>Enseignant</th>
            <th>Cours</th>
            <th>Année académique</th>
            <th>Date affectation</th>
            <th>Activités</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @forelse($affectations as $affectation)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="avatar-sm">{{ collect(explode(' ', $affectation->enseignant->utilisateur->nom ?? ''))->map(fn($p) => substr($p, 0, 1))->take(2)->implode('') }}</span>
                        <span class="fw-semibold">{{ $affectation->enseignant->utilisateur->prenom ?? 'N/A' }}
                            {{ $affectation->enseignant->utilisateur->nom ?? 'N/A' }}</span>
                    </div>
                </td>
                <td>
                    <div>
                        <span class="font-monospace fw-semibold">{{ $affectation->cours->code_cours }}</span>
                        <div class="text-muted small">{{ $affectation->cours->intitule }}</div>
                    </div>
                </td>
                <td><span class="badge badge-soft-purple">{{ $affectation->anneeAcademique->libelle }}</span></td>
                <td class="text-muted">
                    {{ $affectation->date_affectation ? $affectation->date_affectation->format('d/m/Y') : '-' }}</td>
                <td>{{ $affectation->activites_pedagogiques_count }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editAffectationModal{{ $affectation->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        @if ($affectation->activites_pedagogiques_count == 0)
                            <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                                data-bs-target="#deleteAffectationModal{{ $affectation->id }}">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-light border" disabled
                                title="Impossible de supprimer (activités associées)">
                                <i class="fa-solid fa-trash text-muted"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-link fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune affectation trouvée.</p>
                        <small>Commencez par créer une nouvelle affectation.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addAffectationModal" tabindex="-1" aria-labelledby="addAffectationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addAffectationModalLabel">
                            <i class="fa-solid fa-link me-2 text-primary"></i>
                            Nouvelle affectation
                        </h5>
                        <small class="text-muted">Attribuer un cours à un enseignant pour une année académique.</small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('affectations.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Enseignant <span
                                        class="text-danger">*</span></label>
                                <select name="id_enseignant"
                                    class="form-select @error('id_enseignant') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un enseignant --</option>
                                    @foreach (\App\Models\Enseignant::with('utilisateur')->get() as $enseignant)
                                        <option value="{{ $enseignant->id }}">
                                            {{ $enseignant->utilisateur->prenom }} {{ $enseignant->utilisateur->nom }}
                                            ({{ $enseignant->matricule }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_enseignant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cours <span class="text-danger">*</span></label>
                                <select name="id_cours" class="form-select @error('id_cours') is-invalid @enderror"
                                    required>
                                    <option value="">-- Sélectionner un cours --</option>
                                    @foreach (\App\Models\Cours::all() as $cours)
                                        <option value="{{ $cours->id }}">{{ $cours->code_cours }} -
                                            {{ $cours->intitule }}</option>
                                    @endforeach
                                </select>
                                @error('id_cours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Année académique <span
                                        class="text-danger">*</span></label>
                                <select name="id_annee" class="form-select @error('id_annee') is-invalid @enderror"
                                    required>
                                    <option value="">-- Sélectionner une année --</option>
                                    @foreach (\App\Models\AnneeAcademique::all() as $annee)
                                        <option value="{{ $annee->id }}"
                                            {{ $currentYear && $annee->id == $currentYear->id ? 'selected' : '' }}>
                                            {{ $annee->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_annee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date d'affectation <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="date_affectation"
                                    class="form-control @error('date_affectation') is-invalid @enderror"
                                    value="{{ old('date_affectation', now()->format('Y-m-d')) }}" required>
                                @error('date_affectation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Un enseignant ne peut avoir le même cours qu'une fois par année académique.
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer l'affectation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque affectation --}}
    @foreach ($affectations as $affectation)
        {{-- Modale : modification --}}
        <div class="modal fade" id="editAffectationModal{{ $affectation->id }}" tabindex="-1"
            aria-labelledby="editAffectationModalLabel{{ $affectation->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editAffectationModalLabel{{ $affectation->id }}">
                                <i class="fa-solid fa-link me-2 text-primary"></i>
                                Modifier l'affectation
                            </h5>
                            <small class="text-muted">
                                {{ $affectation->enseignant->utilisateur->prenom }}
                                {{ $affectation->enseignant->utilisateur->nom }}
                                — {{ $affectation->cours->code_cours }}
                            </small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('affectations.update', $affectation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Enseignant <span
                                            class="text-danger">*</span></label>
                                    <select name="id_enseignant"
                                        class="form-select @error('id_enseignant') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner un enseignant --</option>
                                        @foreach (\App\Models\Enseignant::with('utilisateur')->get() as $enseignant)
                                            <option value="{{ $enseignant->id }}"
                                                {{ $affectation->id_enseignant == $enseignant->id ? 'selected' : '' }}>
                                                {{ $enseignant->utilisateur->prenom }}
                                                {{ $enseignant->utilisateur->nom }}
                                                ({{ $enseignant->matricule }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_enseignant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Cours <span
                                            class="text-danger">*</span></label>
                                    <select name="id_cours"
                                        class="form-select @error('id_cours') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner un cours --</option>
                                        @foreach (\App\Models\Cours::all() as $cours)
                                            <option value="{{ $cours->id }}"
                                                {{ $affectation->id_cours == $cours->id ? 'selected' : '' }}>
                                                {{ $cours->code_cours }} — {{ $cours->intitule }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_cours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Année académique <span
                                            class="text-danger">*</span></label>
                                    <select name="id_annee"
                                        class="form-select @error('id_annee') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner une année --</option>
                                        @foreach (\App\Models\AnneeAcademique::all() as $annee)
                                            <option value="{{ $annee->id }}"
                                                {{ $affectation->id_annee == $annee->id ? 'selected' : ($currentYear && $annee->id == $currentYear->id && !$affectation->id_annee ? 'selected' : '') }}>
                                                {{ $annee->libelle }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_annee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Date d'affectation <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date_affectation"
                                        class="form-control @error('date_affectation') is-invalid @enderror"
                                        value="{{ old('date_affectation', $affectation->date_affectation ? $affectation->date_affectation->format('Y-m-d') : '') }}"
                                        required>
                                    @error('date_affectation')
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
        @if ($affectation->activites_pedagogiques_count == 0)
            <div class="modal fade" id="deleteAffectationModal{{ $affectation->id }}" tabindex="-1"
                aria-labelledby="deleteAffectationModalLabel{{ $affectation->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteAffectationModalLabel{{ $affectation->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment supprimer cette affectation ?</p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $affectation->enseignant->utilisateur->prenom }}
                                    {{ $affectation->enseignant->utilisateur->nom }}</strong><br>
                                <span class="small">{{ $affectation->cours->code_cours }} —
                                    {{ $affectation->cours->intitule }}</span><br>
                                <span class="small">{{ $affectation->anneeAcademique->libelle }}</span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>

                            <form action="{{ route('affectations.destroy', $affectation->id) }}" method="POST">
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
                var addAffectation = new bootstrap.Modal(document.getElementById('addAffectationModal'));
                addAffectation.show();
            });
        </script>
    @endif
</x-app-page>
