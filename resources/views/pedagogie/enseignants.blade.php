<x-app-page title="Enseignants" section="Gestion pédagogique" icon="fa-solid fa-chalkboard-user"
    subtitle="Informations personnelles et professionnelles des enseignants.">
    <x-slot:actions>
        <button class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addEns">
            <i class="fa-solid fa-plus me-1"></i> Ajouter un enseignant
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher par nom, matricule..." :count="6">
        <x-slot:head>
            <th>Enseignant</th><th>Matricule</th><th>Département</th><th>Grade</th><th>Statut</th><th>Téléphone</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $ens = [
                ['Konan Kouassi', 'ENS-0012', 'Informatique', 'Professeur', 'Permanent', '+225 07 00 00 12', 'green'],
                ['Awa Traoré', 'ENS-0024', 'Gestion', 'Maître-Assistant', 'Permanent', '+225 07 00 00 24', 'green'],
                ['Moussa Diabaté', 'ENS-0031', 'Droit', 'Assistant', 'Vacataire', '+225 07 00 00 31', 'purple'],
                ['Sarah Koné', 'ENS-0045', 'Lettres', 'Assistant', 'Permanent', '+225 07 00 00 45', 'green'],
                ['Blaise Yao', 'ENS-0058', 'Sciences', 'Professeur', 'Permanent', '+225 07 00 00 58', 'green'],
                ['Fatou Ouattara', 'ENS-0067', 'Économie', 'Maître-Assistant', 'Vacataire', '+225 07 00 00 67', 'purple'],
            ];
        @endphp
        @foreach($ens as [$name, $mat, $dep, $grade, $statut, $tel, $c])
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-sm">{{ collect(explode(' ', $name))->map(fn($p)=>substr($p,0,1))->take(2)->implode('') }}</span>
                        <span class="fw-semibold">{{ $name }}</span>
                    </div>
                </td>
                <td class="font-monospace">{{ $mat }}</td>
                <td>{{ $dep }}</td><td>{{ $grade }}</td>
                <td><span class="badge badge-soft-{{ $c }}">{{ $statut }}</span></td>
                <td class="text-muted">{{ $tel }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Voir"><i class="fa-solid fa-eye text-muted"></i></button>
                        <button class="btn btn-light border" title="Modifier"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Supprimer"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>

    <div class="modal fade" id="addEns" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Ajouter un enseignant</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="{{ route('enseignants.index') }}" method="GET">
                    <div class="modal-body"><div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Matricule</label><input class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">Nom</label><input class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">Prénom</label><input class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Téléphone</label><input class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Département</label>
                            <select class="form-select"><option>Informatique</option><option>Gestion</option><option>Droit</option><option>Lettres</option></select></div>
                        <div class="col-md-4"><label class="form-label">Grade</label>
                            <select class="form-select"><option>Assistant</option><option>Maître-Assistant</option><option>Professeur</option></select></div>
                        <div class="col-md-4"><label class="form-label">Statut</label>
                            <select class="form-select"><option>Permanent</option><option>Vacataire</option></select></div>
                    </div></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-uvci">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-page>
