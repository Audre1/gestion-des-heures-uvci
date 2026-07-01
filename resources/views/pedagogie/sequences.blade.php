<x-app-page title="Séquences pédagogiques" section="Gestion pédagogique" icon="fa-solid fa-layer-group"
    subtitle="Unités structurelles composant les cours.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouvelle séquence</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une séquence..." :count="6">
        <x-slot:head>
            <th>N°</th><th>Titre</th><th>Cours</th><th>Ressources</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $seq = [
                [1, 'Introduction à l\'algorithmique', 'INF-101', 3],
                [2, 'Variables et types de données', 'INF-101', 4],
                [3, 'Structures conditionnelles', 'INF-101', 5],
                [4, 'Boucles et itérations', 'INF-101', 4],
                [1, 'Modèle relationnel', 'INF-205', 3],
                [2, 'Langage SQL', 'INF-205', 6],
            ];
        @endphp
        @foreach($seq as [$n, $titre, $cours, $r])
            <tr>
                <td><span class="badge badge-soft-purple">{{ $n }}</span></td>
                <td class="fw-semibold">{{ $titre }}</td>
                <td class="font-monospace">{{ $cours }}</td>
                <td>{{ $r }} ressource(s)</td>
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
