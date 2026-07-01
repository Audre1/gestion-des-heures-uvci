<x-app-page title="Cours" section="Gestion pédagogique" icon="fa-solid fa-book"
    subtitle="Catalogue des cours, crédits et volumes horaires.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouveau cours</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un cours..." :count="6">
        <x-slot:head>
            <th>Code</th><th>Intitulé</th><th>Heures</th><th>Crédits</th><th>Semestre</th><th>Niveau</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $cours = [
                ['INF-101', 'Algorithmique & Programmation', '20h', 2, 'S1', 'L1'],
                ['INF-205', 'Bases de données', '10h', 1, 'S3', 'L2'],
                ['INF-310', 'Développement Web', '30h', 3, 'S5', 'L3'],
                ['GES-120', 'Comptabilité générale', '20h', 2, 'S2', 'L1'],
                ['DRT-210', 'Droit des contrats', '10h', 1, 'S3', 'L2'],
                ['MTH-101', 'Mathématiques appliquées', '20h', 2, 'S1', 'L1'],
            ];
        @endphp
        @foreach($cours as [$code, $int, $h, $cr, $sem, $niv])
            <tr>
                <td class="font-monospace fw-semibold text-uvci-purple">{{ $code }}</td>
                <td>{{ $int }}</td><td>{{ $h }}</td>
                <td><span class="badge badge-soft-green">{{ $cr }} cr.</span></td>
                <td>{{ $sem }}</td>
                <td><span class="badge badge-soft-gray">{{ $niv }}</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Séquences"><i class="fa-solid fa-layer-group text-uvci-purple"></i></button>
                        <button class="btn btn-light border"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
