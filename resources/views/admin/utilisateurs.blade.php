<x-app-page title="Utilisateurs" section="Administration" icon="fa-solid fa-users-gear"
    subtitle="Gérez les comptes, rôles et accès à la plateforme.">
    <x-slot:actions>
        <button class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvel utilisateur
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un utilisateur..." :count="6">
        <x-slot:head>
            <th>Utilisateur</th><th>Login</th><th>Rôle</th><th>Date création</th><th>Statut</th><th class="text-end">Actions</th>
        </x-slot:head>

        @php
            $users = [
                ['Konan Kouassi', 'k.kouassi', 'k.kouassi@uvci.edu.ci', 'Administrateur', '12/09/2024', 'Actif', 'purple', 'green'],
                ['Awa Traoré', 'a.traore', 'a.traore@uvci.edu.ci', 'Secrétaire', '15/09/2024', 'Actif', 'green', 'green'],
                ['Moussa Diabaté', 'm.diabate', 'm.diabate@uvci.edu.ci', 'Enseignant', '20/09/2024', 'Actif', 'gray', 'green'],
                ['Sarah Koné', 's.kone', 's.kone@uvci.edu.ci', 'Enseignant', '22/09/2024', 'Inactif', 'gray', 'red'],
                ['Blaise Yao', 'b.yao', 'b.yao@uvci.edu.ci', 'Enseignant', '28/09/2024', 'Actif', 'gray', 'green'],
                ['Fatou Ouattara', 'f.ouattara', 'f.ouattara@uvci.edu.ci', 'Secrétaire', '02/10/2024', 'Actif', 'green', 'green'],
            ];
        @endphp
        @foreach($users as [$name, $login, $email, $role, $date, $statut, $rc, $sc])
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-sm">{{ collect(explode(' ', $name))->map(fn($p)=>substr($p,0,1))->take(2)->implode('') }}</span>
                        <div>
                            <div class="fw-semibold" style="line-height:1.1">{{ $name }}</div>
                            <div class="text-muted small">{{ $email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $login }}</td>
                <td><span class="badge badge-soft-{{ $rc }}">{{ $role }}</span></td>
                <td>{{ $date }}</td>
                <td><span class="badge badge-soft-{{ $sc }}">{{ $statut }}</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Voir"><i class="fa-solid fa-eye text-muted"></i></button>
                        <button class="btn btn-light border" title="Réinitialiser le mot de passe"><i class="fa-solid fa-key text-uvci-purple"></i></button>
                        <button class="btn btn-light border" title="Modifier"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Supprimer"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>

    {{-- Modale d'ajout (démo) --}}
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvel utilisateur</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('utilisateurs.index') }}" method="GET">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nom</label><input class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Prénom</label><input class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Login</label><input class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Rôle</label>
                                <select class="form-select"><option>Administrateur</option><option>Secrétaire</option><option>Enseignant</option></select>
                            </div>
                            <div class="col-md-6"><label class="form-label">Statut</label>
                                <select class="form-select"><option>Actif</option><option>Inactif</option></select>
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
</x-app-page>
