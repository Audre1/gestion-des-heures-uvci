<x-app-page title="Rapports & Statistiques" section="Volumes & Paiements" icon="fa-solid fa-chart-pie"
    subtitle="Génération d'états récapitulatifs et exports.">

    <div class="row g-3">
        @php
            $reports = [
                ['Fiche individuelle enseignant', 'Bilan détaillé des activités et heures d\'un enseignant.', 'fa-id-card', 'green'],
                ['État global des heures', 'Récapitulatif de tous les volumes horaires de l\'université.', 'fa-list-ol', 'purple'],
                ['Statistiques pédagogiques', 'Répartition des activités par type, niveau et département.', 'fa-chart-column', 'blue'],
                ['État des heures complémentaires', 'Liste des heures effectuées au-delà du service statutaire.', 'fa-clock', 'amber'],
                ['État de paiement collectif', 'Synthèse des rémunérations dues par période.', 'fa-file-invoice-dollar', 'green'],
                ['Charge par département', 'Volume horaire consolidé par département et filière.', 'fa-building-columns', 'purple'],
            ];
        @endphp
        @foreach($reports as [$title, $desc, $icon, $col])
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="stat-icon {{ $col }} mb-3"><i class="fa-solid {{ $icon }}"></i></div>
                        <h6 class="fw-bold">{{ $title }}</h6>
                        <p class="text-muted small">{{ $desc }}</p>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <button class="btn btn-sm btn-outline-uvci flex-fill"><i class="fa-solid fa-file-pdf me-1"></i> PDF</button>
                        <button class="btn btn-sm btn-light border flex-fill"><i class="fa-solid fa-file-excel text-uvci-green me-1"></i> Excel</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-page>
