<x-app-page title="Affectations de cours" section="Gestion pédagogique" icon="fa-solid fa-link"
    subtitle="Attribution des cours aux enseignants par année académique.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouvelle affectation</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une affectation..." :count="6">
        <x-slot:head>
            <th>Enseignant</th><th>Cours</th><th>Année</th><th>Date affectation</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $aff = [
                ['Konan Kouassi', 'INF-101 — Algorithmique', '2024-2025', '10/09/2024'],
                ['Awa Traoré', 'GES-120 — Comptabilité', '2024-2025', '11/09/2024'],
                ['Moussa Diabaté', 'DRT-210 — Droit des contrats', '2024-2025', '12/09/2024'],
                ['Sarah Koné', 'INF-310 — Développement Web', '2024-2025', '13/09/2024'],
                ['Blaise Yao', 'MTH-101 — Mathématiques', '2024-2025', '14/09/2024'],
                ['Konan Kouassi', 'INF-205 — Bases de données', '2024-2025', '15/09/2024'],
            ];
        @endphp
        @foreach($aff as [$ens, $cours, $an, $date])
            <tr>
                <td class="fw-semibold">{{ $ens }}</td>
                <td>{{ $cours }}</td>
                <td><span class="badge badge-soft-purple">{{ $an }}</span></td>
                <td class="text-muted">{{ $date }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
