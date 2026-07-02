<x-app-page title="Années académiques" section="Administration" icon="fa-solid fa-calendar-days"
    subtitle="Définissez les périodes et calendriers académiques.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouvelle année</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une année..." :count="4" :show-filters="false">
        <x-slot:head>
            <th>Libellé</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $annees = [
                ['2024-2025', '01/09/2024', '31/08/2025', 'Active', 'green'],
                ['2023-2024', '01/09/2023', '31/08/2024', 'Clôturée', 'gray'],
                ['2022-2023', '01/09/2022', '31/08/2023', 'Clôturée', 'gray'],
                ['2025-2026', '01/09/2025', '31/08/2026', 'À venir', 'amber'],
            ];
        @endphp
        @foreach($annees as [$lib, $d1, $d2, $st, $c])
            <tr>
                <td class="fw-semibold"><i class="fa-solid fa-calendar text-uvci-purple me-2"></i>{{ $lib }}</td>
                <td>{{ $d1 }}</td>
                <td>{{ $d2 }}</td>
                <td>
                    <span class="badge badge-soft-{{ $c }}">{{ $st }}</span>
                </td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Activer"><i class="fa-solid fa-power-off text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Modifier"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Supprimer"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
