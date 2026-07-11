<x-app-page title="Activités pédagogiques" section="Gestion pédagogique" icon="fa-solid fa-list-check"
    subtitle="Enregistrement et validation des activités des enseignants.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouvelle activité</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une activité..." :count="6">
        <x-slot:head>
            <th>Enseignant</th>
            <th>Type</th>
            <th>Niveau</th>
            <th>Séq.</th>
            <th>Coeff.</th>
            <th>VHT</th>
            <th>Date</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $act = [
                ['K. Kouassi', 'Création', 'Niv. 2', 40, '0,75', '30h', '15/10/2024', 'Validée', 'green'],
                ['A. Traoré', 'Mise à jour', 'Niv. 1', 40, '0,20', '8h', '18/10/2024', 'En cours', 'amber'],
                ['M. Diabaté', 'Création', 'Niv. 3', 80, '1,50', '120h', '20/10/2024', 'Validée', 'green'],
                ['S. Koné', 'Création', 'Niv. 1', 40, '0,40', '16h', '22/10/2024', 'En cours', 'amber'],
                ['B. Yao', 'Mise à jour', 'Niv. 3', 40, '0,75', '30h', '25/10/2024', 'Validée', 'green'],
                ['F. Ouattara', 'Création', 'Niv. 2', 80, '0,75', '60h', '28/10/2024', 'En cours', 'amber'],
            ];
        @endphp
        @foreach ($act as [$ens, $type, $niv, $seq, $co, $vht, $date, $st, $c])
            <tr>
                <td class="fw-semibold">{{ $ens }}</td>
                <td><span
                        class="badge badge-soft-{{ $type === 'Création' ? 'green' : 'purple' }}">{{ $type }}</span>
                </td>
                <td>{{ $niv }}</td>
                <td>{{ $seq }}</td>
                <td>{{ $co }}</td>
                <td class="fw-semibold text-uvci-green">{{ $vht }}</td>
                <td class="text-muted">{{ $date }}</td>
                <td><span class="badge badge-soft-{{ $c }}">{{ $st }}</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        @if ($st === 'En cours')
                            <button class="btn btn-light border" title="Valider"><i
                                    class="fa-solid fa-check text-uvci-green"></i></button>
                        @endif
                        <button class="btn btn-light border" title="Modifier"><i
                                class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-light border" title="Supprimer"><i
                                class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
