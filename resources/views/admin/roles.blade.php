<x-app-page title="Rôles" section="Administration" icon="fa-solid fa-user-shield"
    subtitle="Profils d'accès et permissions du système.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-plus me-1"></i> Nouveau rôle</button>
    </x-slot:actions>

    <div class="row g-3">
        @php
            $roles = [
                ['Administrateur', 'fa-solid fa-user-gear', 'purple', 3, 'Gestion technique, paramétrage, comptes et supervision.'],
                ['Secrétaire Principal', 'fa-solid fa-user-tie', 'green', 12, 'Gestion des enseignants, activités, états et paiements.'],
                ['Enseignant', 'fa-solid fa-chalkboard-user', 'blue', 233, 'Consultation de l\'espace personnel (lecture seule).'],
            ];
        @endphp
        @foreach($roles as [$name, $icon, $color, $nb, $desc])
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="stat-icon {{ $color }} mb-3"><i class="{{ $icon }}"></i></div>
                        <h5 class="fw-bold">{{ $name }}</h5>
                        <p class="text-muted small">{{ $desc }}</p>
                        <span class="badge badge-soft-gray">{{ $nb }} utilisateur(s)</span>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <button class="btn btn-sm btn-outline-uvci flex-fill"><i class="fa-solid fa-pen me-1"></i> Modifier</button>
                        <button class="btn btn-sm btn-light border"><i class="fa-solid fa-shield-halved text-uvci-purple"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-page>
