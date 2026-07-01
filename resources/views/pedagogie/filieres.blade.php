<x-app-page title="Filières" section="Gestion pédagogique" icon="fa-solid fa-sitemap"
    subtitle="Filières rattachées aux départements.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouvelle filière</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une filière..." :count="6">
        <x-slot:head>
            <th>Filière</th><th>Département</th><th>Cours</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $fil = [
                ['Développement Web & Mobile', 'Informatique', 32],
                ['Réseaux & Sécurité', 'Informatique', 28],
                ['Comptabilité-Finance', 'Gestion', 24],
                ['Marketing Digital', 'Gestion', 21],
                ['Droit des Affaires', 'Droit', 18],
                ['Sciences de Gestion', 'Économie', 15],
            ];
        @endphp
        @foreach($fil as [$name, $dep, $c])
            <tr>
                <td class="fw-semibold"><i class="fa-solid fa-diagram-project text-uvci-purple me-2"></i>{{ $name }}</td>
                <td><span class="badge badge-soft-gray">{{ $dep }}</span></td>
                <td>{{ $c }}</td>
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
