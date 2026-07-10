<x-app-page title="Mes documents" section="Espace Enseignant" icon="fa-solid fa-download"
    subtitle="Téléchargez vos récapitulatifs et fiches individuelles.">

    <div class="row g-3">
        @php
            $docs = [
    [
        'Récapitulatif d\'activités',
        'Bilan complet de vos activités pédagogiques 2024-2025.',
        'fa-folder-tree',
        'green',
        'documents.recapitulatif'
    ],
    [
        'Fiche individuelle',
        'Vos informations et charge horaire consolidée.',
        'fa-id-card',
        'purple',
        'documents.fiche'
    ],
    [
        'État des heures',
        'Détail de vos volumes horaires et heures complémentaires.',
        'fa-clock',
        'amber',
        'documents.heures'
    ],
];
        @endphp
        @foreach($docs as [$title, $desc, $icon, $col, $route])
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon {{ $col }} mx-auto mb-3"><i class="fa-solid {{ $icon }}"></i></div>
                        <h6 class="fw-bold">{{ $title }}</h6>
                        <p class="text-muted small">{{ $desc }}</p>
                    </div>
                    <div class="card-footer bg-white">@if($route != '#')
    <a href="{{ route($route) }}" class="btn btn-uvci w-100">
@else
    <a href="#" class="btn btn-secondary w-100">
@endif

    <i class="fa-solid fa-file-pdf me-1"></i> Télécharger (PDF)

</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-page>
