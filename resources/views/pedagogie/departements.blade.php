<x-app-page title="Départements" section="Gestion pédagogique" icon="fa-solid fa-building-columns"
    subtitle="Structuration académique de l'université.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouveau département</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un département..." :count="6" :show-filters="false">
        <x-slot:head>
            <th>Département</th><th>Filières</th><th>Cours</th><th>Enseignants</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $deps = [
                ['Informatique', 4, 128, 56],
                ['Gestion', 3, 96, 42],
                ['Droit', 2, 74, 31],
                ['Lettres', 3, 68, 28],
                ['Sciences', 5, 102, 47],
                ['Économie', 2, 54, 24],
            ];
        @endphp
        @foreach($deps as [$name, $f, $c, $e])
            <tr>
                <td class="fw-semibold"><i class="fa-solid fa-building-columns text-uvci-green me-2"></i>{{ $name }}</td>
                <td>{{ $f }}</td><td>{{ $c }}</td><td>{{ $e }}</td>
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
