<x-app-page title="Enseignants" section="Gestion pédagogique" icon="fa-solid fa-chalkboard-user"
    subtitle="Informations personnelles et professionnelles des enseignants.">
    <x-slot:actions>
        <button class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addEns">
            <i class="fa-solid fa-plus me-1"></i> Ajouter un enseignant
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher par nom, matricule..." :count="$enseignants->count()" export-title="Liste des enseignants">
        <x-slot:filters>
            <label class="dt-filter-label">Département</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($enseignants)->pluck('departement.nom_departement')->unique()->filter()->sort()->values() as $d)
                    <option value="{{ $d }}">{{ $d }}</option>
                @endforeach
            </select>
            <label class="dt-filter-label">Grade</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(3)">
                <option value="">Tous</option>
                @foreach (collect($enseignants)->pluck('grade.libelle')->unique()->filter()->sort()->values() as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Enseignant</th>
            <th>Matricule</th>
            <th>Département</th>
            <th>Grade</th>
            <th>Statut</th>
            <th>Taux horaire perso</th>
            <th>Téléphone</th>
            <th>Créé par</th>
            <th class="text-end">Actions</th>
        </x-slot:head>

        @forelse($enseignants as $enseignant)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="avatar-sm">{{ collect(explode(' ', $enseignant->utilisateur->nom ?? ''))->map(fn($p) => substr($p, 0, 1))->take(2)->implode('') }}</span>
                        <span class="fw-semibold">{{ $enseignant->utilisateur->prenom ?? 'N/A' }}
                            {{ $enseignant->utilisateur->nom ?? 'N/A' }}</span>
                    </div>
                </td>
                <td class="font-monospace">{{ $enseignant->matricule }}</td>
                <td>{{ $enseignant->departement->nom_departement ?? 'N/A' }}</td>
                <td>{{ $enseignant->grade->libelle ?? 'N/A' }}</td>
                <td><span
                        class="badge badge-soft-{{ $enseignant->statut == 'Permanent' ? 'green' : 'purple' }}">{{ $enseignant->statut }}</span>
                </td>
                <td>{{ $enseignant->taux_horaire_perso ? number_format($enseignant->taux_horaire_perso, 2) . ' FCFA' : '-' }}
                </td>
                <td class="text-muted">{{ $enseignant->utilisateur->telephone ?? 'N/A' }}</td>
                <td>{{ $enseignant->utilisateur->createdBy ? $enseignant->utilisateur->createdBy->prenom . ' ' . $enseignant->utilisateur->createdBy->nom : 'Système' }}
                </td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editEnseignantModal{{ $enseignant->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        @if ($enseignant->affectations_cours_count == 0 && $enseignant->etats_paiement_count == 0)
                            <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                                data-bs-target="#deleteEnseignantModal{{ $enseignant->id }}">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-light border" disabled
                                title="Impossible de supprimer (affectations ou paiements associés)">
                                <i class="fa-solid fa-trash text-muted"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-chalkboard-user fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun enseignant trouvé.</p>
                        <small>Commencez par ajouter un nouvel enseignant.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <div class="modal fade" id="addEns" tabindex="-1" aria-labelledby="addEnsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addEnsModalLabel">
                            <i class="fa-solid fa-chalkboard-user me-2 text-primary"></i>
                            Nouvel enseignant
                        </h5>
                        <small class="text-muted">Ajoutez un enseignant à l'établissement.</small>
                    </div>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('enseignants.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <h6 class="text-muted mb-3 fw-semibold">Informations utilisateur</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="nom"
                                    class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}"
                                    required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="prenom"
                                    class="form-control @error('prenom') is-invalid @enderror"
                                    value="{{ old('prenom') }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="text" name="telephone"
                                    class="form-control @error('telephone') is-invalid @enderror"
                                    value="{{ old('telephone') }}">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mot de passe <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="mot_de_passe"
                                    class="form-control @error('mot_de_passe') is-invalid @enderror" required>
                                @error('mot_de_passe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmer le mot de passe <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="mot_de_passe_confirmation" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="alert alert-info mb-4">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Login automatique : il sera généré sous le format <code>prenom.nom</code>. En cas de
                            doublon, un numéro sera ajouté automatiquement.
                        </div>

                        <h6 class="text-muted mb-3 fw-semibold">Informations professionnelles</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Matricule <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="matricule"
                                    class="form-control @error('matricule') is-invalid @enderror"
                                    value="{{ old('matricule') }}" required>
                                @error('matricule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Département <span
                                        class="text-danger">*</span></label>
                                <select name="id_departement"
                                    class="form-select @error('id_departement') is-invalid @enderror" required>
                                    <option value="">Sélectionner un département</option>
                                    @foreach (\App\Models\Departement::all() as $departement)
                                        <option value="{{ $departement->id }}"
                                            {{ old('id_departement') == $departement->id ? 'selected' : '' }}>
                                            {{ $departement->nom_departement }}</option>
                                    @endforeach
                                </select>
                                @error('id_departement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Grade <span class="text-danger">*</span></label>
                                <select name="id_grade" class="form-select @error('id_grade') is-invalid @enderror"
                                    required>
                                    <option value="">Sélectionner un grade</option>
                                    @foreach (\App\Models\Grade::all() as $grade)
                                        <option value="{{ $grade->id }}"
                                            {{ old('id_grade') == $grade->id ? 'selected' : '' }}>
                                            {{ $grade->libelle }}</option>
                                    @endforeach
                                </select>
                                @error('id_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Statut <span
                                        class="text-danger">*</span></label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror"
                                    required>
                                    <option value="Permanent" {{ old('statut') == 'Permanent' ? 'selected' : '' }}>
                                        Permanent
                                    </option>
                                    <option value="Vacataire" {{ old('statut') == 'Vacataire' ? 'selected' : '' }}>
                                        Vacataire
                                    </option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Taux horaire perso</label>
                                <div class="input-group">
                                    <input type="number" name="taux_horaire_perso"
                                        class="form-control @error('taux_horaire_perso') is-invalid @enderror"
                                        placeholder="Optionnel" min="0" step="0.01"
                                        value="{{ old('taux_horaire_perso') }}">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('taux_horaire_perso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date de recrutement <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="date_recrutement"
                                    class="form-control @error('date_recrutement') is-invalid @enderror"
                                    value="{{ old('date_recrutement') }}" required>
                                @error('date_recrutement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer l'enseignant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addEns = new bootstrap.Modal(document.getElementById('addEns'));
                addEns.show();
            });
        </script>
    @endif

    {{-- Modales individuelles pour chaque enseignant --}}
    @foreach ($enseignants as $enseignant)
        {{-- Modale : modification --}}
        <div class="modal fade" id="editEnseignantModal{{ $enseignant->id }}" tabindex="-1"
            aria-labelledby="editEnseignantModalLabel{{ $enseignant->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editEnseignantModalLabel{{ $enseignant->id }}">
                                <i class="fa-solid fa-chalkboard-user me-2 text-primary"></i>
                                Modifier l'enseignant
                            </h5>
                            <small class="text-muted">{{ $enseignant->utilisateur->prenom }}
                                {{ $enseignant->utilisateur->nom }}</small>
                        </div>
                        <button class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <form action="{{ route('enseignants.update', $enseignant->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <h6 class="text-muted mb-3 fw-semibold">Informations utilisateur</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nom <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nom"
                                        class="form-control @error('nom') is-invalid @enderror"
                                        value="{{ old('nom', $enseignant->utilisateur->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Prénom <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="prenom"
                                        class="form-control @error('prenom') is-invalid @enderror"
                                        value="{{ old('prenom', $enseignant->utilisateur->prenom) }}" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $enseignant->utilisateur->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Téléphone</label>
                                    <input type="text" name="telephone"
                                        class="form-control @error('telephone') is-invalid @enderror"
                                        value="{{ old('telephone', $enseignant->utilisateur->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mot de passe (laisser vide pour ne pas
                                        changer)</label>
                                    <input type="password" name="mot_de_passe"
                                        class="form-control @error('mot_de_passe') is-invalid @enderror">
                                    @error('mot_de_passe')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                                    <input type="password" name="mot_de_passe_confirmation" class="form-control">
                                </div>
                            </div>

                            <h6 class="text-muted mb-3 fw-semibold">Informations professionnelles</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Matricule <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="matricule"
                                        class="form-control @error('matricule') is-invalid @enderror"
                                        value="{{ old('matricule', $enseignant->matricule) }}" required>
                                    @error('matricule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Département <span
                                            class="text-danger">*</span></label>
                                    <select name="id_departement"
                                        class="form-select @error('id_departement') is-invalid @enderror" required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach (\App\Models\Departement::all() as $departement)
                                            <option value="{{ $departement->id }}"
                                                {{ $enseignant->id_departement == $departement->id ? 'selected' : '' }}>
                                                {{ $departement->nom_departement }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_departement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Grade <span
                                            class="text-danger">*</span></label>
                                    <select name="id_grade"
                                        class="form-select @error('id_grade') is-invalid @enderror" required>
                                        <option value="">Sélectionner un grade</option>
                                        @foreach (\App\Models\Grade::all() as $grade)
                                            <option value="{{ $grade->id }}"
                                                {{ $enseignant->id_grade == $grade->id ? 'selected' : '' }}>
                                                {{ $grade->libelle }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Statut <span
                                            class="text-danger">*</span></label>
                                    <select name="statut" class="form-select @error('statut') is-invalid @enderror"
                                        required>
                                        <option value="Permanent"
                                            {{ $enseignant->statut == 'Permanent' ? 'selected' : '' }}>
                                            Permanent
                                        </option>
                                        <option value="Vacataire"
                                            {{ $enseignant->statut == 'Vacataire' ? 'selected' : '' }}>Vacataire
                                        </option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Taux horaire perso</label>
                                    <div class="input-group">
                                        <input type="number" name="taux_horaire_perso"
                                            class="form-control @error('taux_horaire_perso') is-invalid @enderror"
                                            placeholder="Optionnel" min="0" step="0.01"
                                            value="{{ old('taux_horaire_perso', $enseignant->taux_horaire_perso) }}">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('taux_horaire_perso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Date de recrutement <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date_recrutement"
                                        class="form-control @error('date_recrutement') is-invalid @enderror"
                                        value="{{ old('date_recrutement', $enseignant->date_recrutement ? $enseignant->date_recrutement->format('Y-m-d') : '') }}"
                                        required>
                                    @error('date_recrutement')
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
        @if ($enseignant->affectations_cours_count == 0 && $enseignant->etats_paiement_count == 0)
            <div class="modal fade" id="deleteEnseignantModal{{ $enseignant->id }}" tabindex="-1"
                aria-labelledby="deleteEnseignantModalLabel{{ $enseignant->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>
                            <button class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment supprimer cet enseignant ?</p>
                            <div class="alert alert-warning mb-0">
                                <strong>{{ $enseignant->utilisateur->prenom }}
                                    {{ $enseignant->utilisateur->nom }}</strong><br>
                                <span class="small">{{ $enseignant->matricule }} —
                                    {{ $enseignant->departement->nom_departement ?? '-' }}</span>
                            </div>
                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>
                            <form action="{{ route('enseignants.destroy', $enseignant->id) }}" method="POST">
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
</x-app-page>
