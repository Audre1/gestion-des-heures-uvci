<x-app-page title="Mes ressources" section="Espace Enseignant" icon="fa-solid fa-book-open"
    subtitle="Ressources pédagogiques associées à vos cours.">

    <x-data-table search-placeholder="Rechercher une ressource..." :count="5">
        <x-slot:head>
            <th>Titre</th><th>Type</th><th>Cours</th><th>Séquence</th><th>Modifiée le</th>
        </x-slot:head>
        @php
            $res = [
                ['Support de cours — Introduction', 'Document', 'INF-101', 'Séq. 1', '12/10/2024', 'fa-file-lines', 'blue'],
                ['Vidéo — Les variables', 'Vidéo', 'INF-101', 'Séq. 2', '11/09/2024', 'fa-video', 'purple'],
                ['Quiz — Conditions', 'Quiz', 'INF-101', 'Séq. 3', '20/10/2024', 'fa-circle-question', 'green'],
                ['Cours SQL — PDF', 'Document', 'INF-205', 'Séq. 2', '18/10/2024', 'fa-file-pdf', 'blue'],
                ['Évaluation finale', 'Évaluation', 'INF-205', 'Séq. 2', '16/09/2024', 'fa-clipboard-check', 'green'],
            ];
        @endphp
        @foreach($res as [$titre, $type, $cours, $seq, $m, $icon, $col])
            <tr>
                <td><i class="fa-solid {{ $icon }} text-uvci-purple me-2"></i>{{ $titre }}</td>
                <td><span class="badge badge-soft-{{ $col }}">{{ $type }}</span></td>
                <td class="font-monospace">{{ $cours }}</td><td>{{ $seq }}</td>
                <td class="text-muted">{{ $m }}</td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
