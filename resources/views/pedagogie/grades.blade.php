<x-app-page title="Grades" section="Gestion pédagogique" icon="fa-solid fa-ranking-star"
    subtitle="Niveaux hiérarchiques académiques et taux horaires associés.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouveau grade</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un grade..." :count="3" :show-filters="false">
        <x-slot:head>
            <th>Libellé</th><th>Taux horaire actuel</th><th>Enseignants</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $grades = [
                ['Professeur', '25 000 FCFA', 42],
                ['Maître-Assistant', '18 000 FCFA', 86],
                ['Assistant', '15 000 FCFA', 120],
            ];
        @endphp
        @foreach($grades as [$lib, $taux, $nb])
            <tr>
                <td class="fw-semibold"><i class="fa-solid fa-medal text-uvci-purple me-2"></i>{{ $lib }}</td>
                <td class="text-uvci-green fw-semibold">{{ $taux }}</td>
                <td><span class="badge badge-soft-gray">{{ $nb }}</span></td>
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
