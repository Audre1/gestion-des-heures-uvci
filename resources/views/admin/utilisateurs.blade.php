<x-app-page title="Utilisateurs" section="Administration" icon="fa-solid fa-users-gear"
    subtitle="Gérez les comptes, rôles et accès à la plateforme.">
    <x-slot:actions>
        <button class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvel utilisateur
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un utilisateur..." :count="$utilisateurs->count()">
        <x-slot:filters>
            <label class="dt-filter-label">Rôle</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($utilisateurs)->pluck('role.libelle')->unique()->filter()->sort()->values() as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>
            <label class="dt-filter-label">Statut</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(5)">
                <option value="">Tous</option>
                @foreach (collect($utilisateurs)->pluck('statut_compte')->unique()->filter()->sort()->values() as $s)
                    <option value="{{ ucfirst($s) }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Utilisateur</th>
            <th>Login</th>
            <th>Rôle</th>
            <th>Date création</th>
            <th>Créé par</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot:head>

        @forelse ($utilisateurs as $user)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="avatar-sm">{{ collect(explode(' ', $user->nom))->map(fn($p) => substr($p, 0, 1))->take(2)->implode('') }}</span>
                        <div>
                            <div class="fw-semibold" style="line-height:1.1">{{ $user->prenom }} {{ $user->nom }}
                            </div>
                            <div class="text-muted small">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $user->login }}</td>
                <td><span class="badge badge-soft-purple">{{ $user->role->libelle ?? 'N/A' }}</span></td>
                <td>{{ $user->date_creation ? $user->date_creation->format('d/m/Y') : 'N/A' }}</td>
                <td>
                    {{ $user->createdBy ? explode(' ', trim($user->createdBy->prenom))[0] . ' ' . $user->createdBy->nom : 'Système' }}
                </td>
                <td><span
                        class="badge badge-soft-{{ $user->statut_compte == 'actif' ? 'green' : 'red' }}">{{ ucfirst($user->statut_compte) }}</span>
                </td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $user->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $user->id }}">
                            <i class="fa-solid fa-trash text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>

            {{-- Modale de modification --}}
            <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1"
                aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg mt-4">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <div>
                                <h5 class="modal-title fw-bold" id="editModalLabel{{ $user->id }}">
                                    <i class="fa-solid fa-user-pen me-2 text-primary"></i>
                                    Modifier l'utilisateur
                                </h5>
                                <small class="text-muted">Modifiez les informations de {{ $user->prenom }}
                                    {{ $user->nom }}.</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form action="{{ route('utilisateurs.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nom <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="nom" class="form-control"
                                            value="{{ old('nom', $user->nom) }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Prénom <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="prenom" class="form-control"
                                            value="{{ old('prenom', $user->prenom) }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $user->email) }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Téléphone</label>
                                        <input type="tel" name="telephone" class="form-control"
                                            value="{{ old('telephone', $user->telephone) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Rôle <span
                                                class="text-danger">*</span></label>
                                        <select name="id_role" class="form-select" required>
                                            <option value="">Sélectionner un rôle</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ old('id_role', $user->id_role) == $role->id ? 'selected' : '' }}>
                                                    {{ $role->libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Statut <span
                                                class="text-danger">*</span></label>
                                        <select name="statut_compte" class="form-select" required>
                                            <option value="actif"
                                                {{ old('statut_compte', $user->statut_compte) === 'actif' ? 'selected' : '' }}>
                                                Actif
                                            </option>
                                            <option value="inactif"
                                                {{ old('statut_compte', $user->statut_compte) === 'inactif' ? 'selected' : '' }}>
                                                Inactif
                                            </option>
                                            <option value="suspendu"
                                                {{ old('statut_compte', $user->statut_compte) === 'suspendu' ? 'selected' : '' }}>
                                                Suspendu
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <p class="text-muted small mb-2">
                                            Laissez les champs suivants vides si vous ne souhaitez pas modifier le mot
                                            de passe.
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nouveau mot de passe</label>
                                        <input type="password" name="mot_de_passe" class="form-control"
                                            placeholder="Laisser vide pour ne pas modifier">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Confirmer le nouveau mot de passe</label>
                                        <input type="password" name="mot_de_passe_confirmation" class="form-control"
                                            placeholder="Confirmer le nouveau mot de passe">
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4 mb-0">
                                    <i class="fa-solid fa-circle-info me-2"></i>
                                    Login actuel : <strong>{{ $user->login }}</strong>. Il ne sera pas modifié
                                    automatiquement.
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

            {{-- Modale de suppression --}}
            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1"
                aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $user->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>
                            <button type="button" class="btn-close btn-close-white"
                                data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-2">Voulez-vous vraiment supprimer cet utilisateur ?</p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $user->prenom }} {{ $user->nom }}</strong><br>
                                <span class="small">Login : {{ $user->login }} — Email : {{ $user->email }}</span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                Annuler
                            </button>

                            <form action="{{ route('utilisateurs.destroy', $user->id) }}" method="POST">
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
        @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-users-slash fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun utilisateur trouvé.</p>
                        <small>Commencez par ajouter un nouvel utilisateur.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale d'ajout --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addModalLabel">
                            <i class="fa-solid fa-user-plus me-2 text-primary"></i>
                            Nouvel utilisateur
                        </h5>
                        <small class="text-muted">Renseignez les informations du nouvel utilisateur.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('utilisateurs.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="nom"
                                    class="form-control @error('nom') is-invalid @enderror"
                                    value="{{ old('nom') }}" placeholder="Ex : Yao" required>

                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="prenom"
                                    class="form-control @error('prenom') is-invalid @enderror"
                                    value="{{ old('prenom') }}" placeholder="Ex : Loïc Emmanuel" required>

                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="Ex : utilisateu@uvci.ci" required>

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" name="telephone"
                                    class="form-control @error('telephone') is-invalid @enderror"
                                    value="{{ old('telephone') }}" placeholder="Ex : 0700000000">

                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mot de passe <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="mot_de_passe"
                                    class="form-control @error('mot_de_passe') is-invalid @enderror"
                                    placeholder="Saisir le mot de passe" required>

                                @error('mot_de_passe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmer le mot de passe <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="mot_de_passe_confirmation" class="form-control"
                                    placeholder="Confirmer le mot de passe" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                                <select name="id_role" class="form-select @error('id_role') is-invalid @enderror"
                                    required>
                                    <option value="">Sélectionner un rôle</option>

                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('id_role') == $role->id ? 'selected' : '' }}>
                                            {{ $role->libelle }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('id_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Statut <span
                                        class="text-danger">*</span></label>
                                <select name="statut_compte"
                                    class="form-select @error('statut_compte') is-invalid @enderror" required>
                                    <option value="actif"
                                        {{ old('statut_compte', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ old('statut_compte') == 'inactif' ? 'selected' : '' }}>
                                        Inactif</option>
                                    <option value="suspendu"
                                        {{ old('statut_compte') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                                </select>

                                @error('statut_compte')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center mt-4 mb-0" role="alert">
                            <i class="fa-solid fa-circle-info fs-5 me-3"></i>
                            <div>
                                <strong>Login automatique :</strong>
                                il sera généré sous le format <code>prenom.nom</code>.
                                En cas de doublon, un numéro sera ajouté automatiquement.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i>
                            Annuler
                        </button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addModal = new bootstrap.Modal(document.getElementById('addModal'));
                addModal.show();
            });
        </script>
    @endif
</x-app-page>
