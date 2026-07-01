<x-app-page title="Taux horaires" section="Administration" icon="fa-solid fa-money-bill-wave"
    subtitle="Barèmes de rémunération par grade et par année académique.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouveau taux</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un taux..." :count="5">
        <x-slot:head>
            <th>Grade</th><th>Montant</th><th>Devise</th><th>Année</th><th>Application</th><th>Statut</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $taux = [
                ['Professeur', '25 000', 'FCFA', '2024-2025', '01/09/2024', 'Actif', 'green'],
                ['Maître-Assistant', '18 000', 'FCFA', '2024-2025', '01/09/2024', 'Actif', 'green'],
                ['Assistant', '15 000', 'FCFA', '2024-2025', '01/09/2024', 'Actif', 'green'],
                ['Professeur', '23 000', 'FCFA', '2023-2024', '01/09/2023', 'Expiré', 'gray'],
                ['Assistant', '13 000', 'FCFA', '2023-2024', '01/09/2023', 'Expiré', 'gray'],
            ];
        @endphp
        @foreach($taux as [$g, $m, $d, $a, $ap, $st, $c])
            <tr>
                <td class="fw-semibold">{{ $g }}</td>
                <td class="fw-semibold text-uvci-green">{{ $m }}</td>
                <td>{{ $d }}</td><td>{{ $a }}</td><td>{{ $ap }}</td>
                <td><span class="badge badge-soft-{{ $c }}">{{ $st }}</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Modifier"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Supprimer"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
