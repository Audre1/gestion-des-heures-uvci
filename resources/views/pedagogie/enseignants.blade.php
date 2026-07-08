<x-app-page title="Enseignants" section="Gestion pédagogique" icon="fa-solid fa-chalkboard-user"
    subtitle="Informations personnelles et professionnelles des enseignants.">
    <x-slot:actions>
        <button class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addEns">
            <i class="fa-solid fa-plus me-1"></i> Ajouter un enseignant
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher par nom, matricule..." :count="$enseignants->count()">
        <x-slot:head>
            <th>Enseignant</th>
            <th>Matricule</th>
            <th>Département</th>
            <th>Grade</th>
            <th>Statut</th>
            <th>Téléphone</th>
            <th>Créé par</th>
            <th class="text-end">Actions</th>
        </x-slot:head>

        @forelse($enseignants as $enseignant)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-sm">{{ collect(explode(' ', $enseignant->utilisateur->nom ?? ''))->map(fn($p) => substr($p, 0, 1))->take(2)->implode('') }}</span>
                        <span class="fw-semibold">{{ $enseignant->utilisateur->prenom ?? 'N/A' }} {{ $enseignant->utilisateur->nom ?? 'N/A' }}</span>
                    </div>
                </td>
                <td class="font-monospace">{{ $enseignant->matricule }}</td>
                <td>{{ $enseignant->departement->nom_departement ?? 'N/A' }}</td>
                <td>{{ $enseignant->grade->libelle ?? 'N/A' }}</td>
                <td><span class="badge badge-soft-{{ $enseignant->statut == 'actif' ? 'green' : 'purple' }}">{{ ucfirst($enseignant->statut) }}</span></td>
                <td class="text-muted">{{ $enseignant->utilisateur->telephone ?? 'N/A' }}</td>
                <td>{{ $enseignant->utilisateur->createdBy ? ($enseignant->utilisateur->createdBy->prenom . ' ' . $enseignant->utilisateur->createdBy->nom) : 'Système' }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Voir"><i class="fa-solid fa-eye text-muted"></i></button>
                        <button class="btn btn-light border" title="Modifier"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Supprimer"><i class="fa-solid fa-trash text-danger"></i></button>
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

    <div class="modal fade" id="addEns" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un enseignant</h5><button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('enseignants.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <h6 class="text-muted mb-3">Informations utilisateur</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" required>
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="mot_de_passe" class="form-control @error('mot_de_passe') is-invalid @enderror" required>
                                @error('mot_de_passe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmer le mot de passe</label>
                                <input type="password" name="mot_de_passe_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="alert alert-info mb-4">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Le login sera généré automatiquement sous la forme : prenom.nom
                        </div>

                        <h6 class="text-muted mb-3">Informations professionnelles</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Matricule</label>
                                <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror" value="{{ old('matricule') }}" required>
                                @error('matricule') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Département</label>
                                <select name="id_departement" class="form-select @error('id_departement') is-invalid @enderror" required>
                                    <option value="">Sélectionner un département</option>
                                    @foreach(\App\Models\Departement::all() as $departement)
                                        <option value="{{ $departement->id }}" {{ old('id_departement') == $departement->id ? 'selected' : '' }}>{{ $departement->nom_departement }}</option>
                                    @endforeach
                                </select>
                                @error('id_departement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Grade</label>
                                <select name="id_grade" class="form-select @error('id_grade') is-invalid @enderror" required>
                                    <option value="">Sélectionner un grade</option>
                                    @foreach(\App\Models\Grade::all() as $grade)
                                        <option value="{{ $grade->id }}" {{ old('id_grade') == $grade->id ? 'selected' : '' }}>{{ $grade->libelle }}</option>
                                    @endforeach
                                </select>
                                @error('id_grade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut</label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                    <option value="actif" {{ old('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                    <option value="retraite" {{ old('statut') == 'retraite' ? 'selected' : '' }}>Retraite</option>
                                </select>
                                @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date de recrutement</label>
                                <input type="date" name="date_recrutement" class="form-control @error('date_recrutement') is-invalid @enderror" value="{{ old('date_recrutement') }}" required>
                                @error('date_recrutement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-uvci">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addEns = new bootstrap.Modal(document.getElementById('addEns'));
                addEns.show();
            });
        </script>
    @endif
</x-app-page>
