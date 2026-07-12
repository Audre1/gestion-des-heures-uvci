<x-app-page title="Mon profil" section="Compte" icon="fa-solid fa-user"
    subtitle="Consultez et mettez à jour vos informations personnelles.">

    <div class="row g-3">
        {{-- Carte profil --}}
        <div class="col-lg-4">
            <div class="card text-center">
                <div class="card-body">

                    <div class="avatar mx-auto mb-3" style="width:88px;height:88px;font-size:2rem">
                        {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}{{ strtoupper(substr($enseignant->nom, 0, 1)) }}
                    </div>

                    <h5 class="fw-bold mb-0">
                        {{ $enseignant->prenom }} {{ $enseignant->nom }}
                    </h5>

                    <p class="text-muted mb-2">
                        {{ $enseignant->email }}
                    </p>

                    <span class="badge badge-soft-purple">
                        Enseignant
                    </span>

                    <hr>
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Matricule</span> <span
                            class="fw-semibold">{{ $enseignant->matricule }}</span> </div>
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Département</span> <span
                            class="fw-semibold"> {{ $enseignant->departement->nom ?? 'Non défini' }}</span> </div>
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Grade</span> <span
                            class="fw-semibold">
                            {{ $enseignant->grade->libelle ?? 'Non défini' }}
                        </span> </div>
                    <div class="d-flex justify-content-between py-1"><span class="text-muted">Statut</span> <span
                            class="badge badge-soft-green">
                            {{ ucfirst($enseignant->statut) }}
                        </span> </div>
                </div>
            </div>
        </div>

        {{-- Formulaires --}}
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><i class="fa-solid fa-user-pen text-uvci-green me-2"></i>Informations
                    personnelles</div>
                <div class="card-body">
                    <form action="{{ route('profil.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nom</label><input class="form-control"
                                    value="{{ $enseignant->nom }}"></div>
                            <div class="col-md-6"><label class="form-label">Prénom</label><input class="form-control"
                                    value="{{ $enseignant->prenom }}"></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email"
                                    class="form-control" value="{{ $enseignant->email }}"></div>
                            <div class="col-md-6"><label class="form-label">Téléphone</label><input class="form-control"
                                    value="{{ $enseignant->telephone }}"></div>
                            <div class="col-12 text-end"><button class="btn btn-uvci"><i
                                        class="fa-solid fa-floppy-disk me-1"></i> Enregistrer</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fa-solid fa-lock text-uvci-purple me-2"></i>Changer le mot de passe
                </div>
                <div class="card-body">
                    <form action="{{ route('profil.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">Mot de passe actuel</label><input
                                    type="password" class="form-control" placeholder="••••••••"></div>
                            <div class="col-md-4"><label class="form-label">Nouveau mot de passe</label><input
                                    type="password" class="form-control" placeholder="••••••••"></div>
                            <div class="col-md-4"><label class="form-label">Confirmer</label><input type="password"
                                    class="form-control" placeholder="••••••••"></div>
                            <div class="col-12 text-end"><button class="btn btn-uvci-purple"><i
                                        class="fa-solid fa-key me-1"></i> Mettre à jour</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-page>
