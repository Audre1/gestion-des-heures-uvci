<x-app-page title="Ressources pédagogiques" section="Gestion pédagogique" icon="fa-solid fa-photo-film"
    subtitle="Contenus numériques associés aux séquences.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouvelle ressource</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une ressource..." :count="6">
        <x-slot:head>
            <th>Titre</th><th>Type</th><th>Séquence</th><th>Créée le</th><th>Modifiée le</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $res = [
                ['Support de cours — Introduction', 'Document', 'Séq. 1 (INF-101)', '10/09/2024', '12/10/2024', 'fa-file-lines', 'blue'],
                ['Vidéo — Les variables', 'Vidéo', 'Séq. 2 (INF-101)', '11/09/2024', '11/09/2024', 'fa-video', 'purple'],
                ['Quiz — Conditions', 'Quiz', 'Séq. 3 (INF-101)', '13/09/2024', '20/10/2024', 'fa-circle-question', 'green'],
                ['TP interactif — Boucles', 'Activité Interactive', 'Séq. 4 (INF-101)', '14/09/2024', '14/09/2024', 'fa-laptop-code', 'amber'],
                ['Cours SQL — PDF', 'Document', 'Séq. 2 (INF-205)', '15/09/2024', '18/10/2024', 'fa-file-pdf', 'blue'],
                ['Évaluation finale', 'Évaluation', 'Séq. 2 (INF-205)', '16/09/2024', '16/09/2024', 'fa-clipboard-check', 'green'],
            ];
        @endphp
        @foreach($res as [$titre, $type, $seq, $c, $m, $icon, $col])
            <tr>
                <td><i class="fa-solid {{ $icon }} text-uvci-purple me-2"></i>{{ $titre }}</td>
                <td><span class="badge badge-soft-{{ $col }}">{{ $type }}</span></td>
                <td>{{ $seq }}</td>
                <td class="text-muted">{{ $c }}</td><td class="text-muted">{{ $m }}</td>
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
