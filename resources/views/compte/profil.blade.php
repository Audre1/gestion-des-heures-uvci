<x-app-page title="Mon profil" section="Compte" icon="fa-solid fa-user"
    subtitle="Consultez et mettez à jour vos informations personnelles.">

    <div class="row g-3">
        {{-- Carte profil --}}
        <div class="col-lg-4">
            <div class="card text-center">
                <div class="card-body">

                    <div class="avatar mx-auto mb-3" style="width:88px;height:88px;font-size:2rem">
                        {{ strtoupper(substr($utilisateur->prenom, 0, 1)) }}{{ strtoupper(substr($utilisateur->nom, 0, 1)) }}
                    </div>

                    <h5 class="fw-bold mb-0">
                        {{ $utilisateur->prenom }} {{ $utilisateur->nom }}
                    </h5>

                    <p class="text-muted mb-2">
                        {{ $utilisateur->email }}
                    </p>

                    @php
                        $badgeClass = match($role->code) {
                            'admin' => 'badge-soft-purple',
                            'secretaire' => 'badge-soft-blue',
                            'enseignant' => 'badge-soft-green',
                            default => 'badge-soft-gray',
                        };
                        $roleLabel = $role->libelle ?? ucfirst($role->code);
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        {{ $roleLabel }}
                    </span>

                    <hr>
                    
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Identifiant de connexion</span>
                        <span class="fw-semibold">{{ $utilisateur->login }}</span>
                    </div>
                    @if ($enseignant)
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Matricule</span>
                            <span class="fw-semibold">{{ $enseignant->matricule }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Département</span>
                            <span class="fw-semibold">{{ $enseignant->departement->nom_departement ?? 'Non défini' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Grade</span>
                            <span class="fw-semibold">{{ $enseignant->grade->libelle ?? 'Non défini' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Statut</span>
                            <span class="badge badge-soft-green">
                                {{ ucfirst($enseignant->statut) }}
                            </span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Rôle</span>
                            <span class="fw-semibold">{{ $roleLabel }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Téléphone</span>
                            <span class="fw-semibold">{{ $utilisateur->telephone ?? 'Non défini' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Statut</span>
                            <span class="badge badge-soft-green">{{ ucfirst($utilisateur->statut_compte) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Formulaires --}}
        <div class="col-lg-8">

            {{-- Informations personnelles --}}
            <div class="card mb-3">
                <div class="card-header"><i class="fa-solid fa-user-pen text-uvci-green me-2"></i>Informations
                    personnelles</div>
                <div class="card-body">
                    <form action="{{ route('profil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input class="form-control @error('nom') is-invalid @enderror"
                                       name="nom" value="{{ old('nom', $utilisateur->nom) }}">
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input class="form-control @error('prenom') is-invalid @enderror"
                                       name="prenom" value="{{ old('prenom', $utilisateur->prenom) }}">
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email', $utilisateur->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input class="form-control @error('telephone') is-invalid @enderror"
                                       name="telephone" value="{{ old('telephone', $utilisateur->telephone) }}">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-uvci">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Changer le mot de passe --}}
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-lock text-uvci-purple me-2"></i>Changer le mot de passe
                </div>
                <div class="card-body">
                    <form action="{{ route('profil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       name="current_password" placeholder="••••••••">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Confirmer</label>
                                <input type="password" class="form-control"
                                       name="password_confirmation" placeholder="••••••••">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-uvci-purple">
                                    <i class="fa-solid fa-key me-1"></i> Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-page>