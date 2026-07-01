<x-app-page title="Sauvegardes" section="Administration" icon="fa-solid fa-database"
    subtitle="Sauvegarde et restauration des données du système.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-cloud-arrow-up me-1"></i> Lancer une sauvegarde</button>
    </x-slot:actions>

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon green"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div><div class="stat-value" style="font-size:1.1rem">30/06/2025 16:44</div><div class="stat-label">Dernière sauvegarde</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon purple"><i class="fa-solid fa-hard-drive"></i></div>
            <div><div class="stat-value">248 Mo</div><div class="stat-label">Taille des données</div></div>
        </div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon blue"><i class="fa-solid fa-shield-halved"></i></div>
            <div><div class="stat-value">Quotidienne</div><div class="stat-label">Fréquence automatique</div></div>
        </div></div></div>
    </div>

    <x-data-table search-placeholder="Rechercher une sauvegarde..." :count="4" :show-filters="false">
        <x-slot:head>
            <th>Fichier</th><th>Date</th><th>Taille</th><th>Type</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $bk = [
                ['backup_2025-06-30.sql', '30/06/2025 16:44', '248 Mo', 'Manuelle'],
                ['backup_2025-06-29.sql', '29/06/2025 02:00', '246 Mo', 'Automatique'],
                ['backup_2025-06-28.sql', '28/06/2025 02:00', '245 Mo', 'Automatique'],
                ['backup_2025-06-27.sql', '27/06/2025 02:00', '244 Mo', 'Automatique'],
            ];
        @endphp
        @foreach($bk as [$f, $d, $s, $t])
            <tr>
                <td><i class="fa-solid fa-file-zipper text-uvci-purple me-2"></i>{{ $f }}</td>
                <td>{{ $d }}</td><td>{{ $s }}</td>
                <td><span class="badge badge-soft-gray">{{ $t }}</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Télécharger"><i class="fa-solid fa-download text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Restaurer"><i class="fa-solid fa-rotate-left text-uvci-purple"></i></button>
                        <button class="btn btn-light border" title="Supprimer"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
