<x-app-page title="Types de ressources" section="Gestion pédagogique" icon="fa-solid fa-shapes"
    subtitle="Catégories de ressources pédagogiques numériques.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouveau type</button>
    </x-slot:actions>

    <div class="row g-3">
        @php
            $types = [
                ['Texte', 'fa-file-lines', 'blue'],
                ['Vidéo', 'fa-video', 'purple'],
                ['Document', 'fa-file-pdf', 'green'],
                ['Quiz', 'fa-circle-question', 'amber'],
                ['Activité Interactive', 'fa-laptop-code', 'purple'],
                ['Évaluation', 'fa-clipboard-check', 'green'],
            ];
        @endphp
        @foreach($types as [$name, $icon, $col])
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="stat-icon {{ $col }} mx-auto mb-2"><i class="fa-solid {{ $icon }}"></i></div>
                        <div class="fw-semibold">{{ $name }}</div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-light border"><i class="fa-solid fa-pen text-uvci-green"></i></button>
                        <button class="btn btn-sm btn-light border"><i class="fa-solid fa-trash text-danger"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-page>
