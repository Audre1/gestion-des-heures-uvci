<x-app-page title="Mes ressources" section="Espace Enseignant" icon="fa-solid fa-book-open"
    subtitle="Ressources pédagogiques associées à vos cours.">

    <x-data-table search-placeholder="Rechercher une ressource..." :count="5">
        <x-slot:filters>
            <label class="dt-filter-label">Type</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(1)">
                <option value="">Tous</option>
                @foreach (collect($ressources)->pluck('typeRessource.libelle')->unique()->filter()->sort()->values() as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
            <label class="dt-filter-label">Cours</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($ressources)->pluck('sequence.cours.code_cours')->unique()->filter()->sort()->values() as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Titre</th>
            <th>Type</th>
            <th>Cours</th>
            <th>Séquence</th>
            <th>Modifiée le</th>
        </x-slot:head>


        @foreach ($ressources as $ressource)
            @php
                $couleur = match ($ressource->typeRessource->libelle) {
                    'Vidéo pédagogique' => 'purple',
                    'Document PDF' => 'blue',
                    default => 'green',
                };
            @endphp

            <tr>
                <td>
                    <i class="fa-solid fa-file-lines text-uvci-purple me-2"></i>
                    {{ $ressource->titre }}
                </td>

                <td>
                    <span class="badge badge-soft-{{ $couleur }}">
                        {{ $ressource->typeRessource->libelle }}
                    </span>
                </td>

                <td class="font-monospace">
                    {{ $ressource->sequence->cours->code_cours }}
                </td>

                <td>
                    {{ $ressource->sequence->titre }}
                </td>

                <td class="text-muted">
                    {{ $ressource->date_modification?->format('d/m/Y') ?? $ressource->date_creation->format('d/m/Y') }}
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
