<x-app-page title="Niveaux de complexité" section="Gestion pédagogique" icon="fa-solid fa-signal"
    subtitle="Classification déterminant le coefficient de calcul des heures.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouveau niveau</button>
    </x-slot:actions>

    <div class="row g-3">
        @php
            $niv = [
                ['Niveau 1', 'Simple', 'Textes + quiz', '0,40', 'green'],
                ['Niveau 2', 'Interactif', 'Textes + activités interactives', '0,75', 'purple'],
                ['Niveau 3', 'Serious games', 'Simulations, serious games', '1,50', 'blue'],
            ];
        @endphp
        @foreach($niv as [$lib, $tag, $desc, $coeff, $col])
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="stat-icon {{ $col }}"><i class="fa-solid fa-signal"></i></div>
                            <span class="badge badge-soft-{{ $col }}">Coeff. {{ $coeff }}</span>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $lib }} <small class="text-muted fw-normal">— {{ $tag }}</small></h5>
                        <p class="text-muted small mb-0">{{ $desc }}</p>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <button class="btn btn-sm btn-outline-uvci flex-fill"><i class="fa-solid fa-pen me-1"></i> Modifier</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-page>
