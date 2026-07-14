<x-app-page title="Mes activités" section="Espace Enseignant" icon="fa-solid fa-folder-open"
    subtitle="Historique de vos activités pédagogiques (lecture seule).">





    <x-data-table search-placeholder="Rechercher une activité..." :count="5" :show-filters="true"
        export-title="Mes activités pédagogiques">
        <x-slot:filters>
            <label class="dt-filter-label">Type</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(1)">
                <option value="">Tous</option>
                <option value="creation">Création</option>
                <option value="maj">Mise à jour</option>
            </select>
            <label class="dt-filter-label">Niveau</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($activites)->pluck('niveauComplexite.libelle')->unique()->filter()->sort()->values() as $n)
                    <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Cours</th>
            <th>Type</th>
            <th>Niveau</th>
            <th>Séq.</th>
            <th>VHT</th>
            <th>Date</th>
            <th>Statut</th>
            <th class="text-end">Détail</th>
        </x-slot:head>


        @if ($activites->isEmpty())
            <div class="alert alert-info">
                Aucune activité pédagogique n'est disponible pour le moment.
            </div>
        @endif



        @foreach ($activites as $activite)
            <tr>
                <td class="fw-semibold">
                    {{ $activite->affectationCours->cours->code_cours }} —
                    {{ $activite->affectationCours->cours->intitule }}
                </td>

                <td>
                    <span class="badge badge-soft-green">
                        {{ $activite->type_activite }}
                    </span>
                </td>

                <td>
                    {{ $activite->niveauComplexite->libelle ?? '-' }}
                </td>

                <td>{{ $activite->nb_sequences }}</td>

                <td class="fw-semibold text-uvci-green">
                    {{ $activite->volume_horaire }}h
                </td>

                <td class="text-muted">
                    {{ $activite->date_activite->format('d/m/Y') }}
                </td>

                <td>
                    <span class="badge badge-soft-green">
                        {{ $activite->statut }}
                    </span>
                </td>

                <td class="text-end">
                    <button class="btn btn-sm btn-light border">
                        <i class="fa-solid fa-eye text-muted"></i>
                    </button>
                </td>
            </tr>
        @endforeach



    </x-data-table>
</x-app-page>
