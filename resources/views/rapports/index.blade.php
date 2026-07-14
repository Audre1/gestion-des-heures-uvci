<x-app-page title="Rapports & Statistiques" section="Volumes & Paiements" icon="fa-solid fa-chart-pie"
    subtitle="Génération d'états récapitulatifs et exports.">

    <div class="row g-3">
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="stat-icon green mb-3"><i class="fa-solid fa-id-card"></i></div>
                    <h6 class="fw-bold">Fiche individuelle enseignant</h6>
                    <p class="text-muted small">Bilan détaillé des activités et heures d'un enseignant.</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('rapports.fiche-individuelle') }}" class="btn btn-sm btn-uvci w-100">
                        <i class="fa-solid fa-gear me-1"></i> Générer
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="stat-icon purple mb-3"><i class="fa-solid fa-list-ol"></i></div>
                    <h6 class="fw-bold">État global des heures</h6>
                    <p class="text-muted small">Récapitulatif de tous les volumes horaires de l'université.</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('rapports.etat-global') }}" class="btn btn-sm btn-uvci w-100">
                        <i class="fa-solid fa-gear me-1"></i> Générer
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="stat-icon blue mb-3"><i class="fa-solid fa-chart-column"></i></div>
                    <h6 class="fw-bold">Statistiques pédagogiques</h6>
                    <p class="text-muted small">Répartition des activités par type, niveau et département.</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('rapports.statistiques') }}" class="btn btn-sm btn-uvci w-100">
                        <i class="fa-solid fa-gear me-1"></i> Générer
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="stat-icon amber mb-3"><i class="fa-solid fa-clock"></i></div>
                    <h6 class="fw-bold">État des heures complémentaires</h6>
                    <p class="text-muted small">Liste des heures effectuées au-delà du service statutaire.</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('rapports.heures-complementaires') }}" class="btn btn-sm btn-uvci w-100">
                        <i class="fa-solid fa-gear me-1"></i> Générer
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="stat-icon green mb-3"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <h6 class="fw-bold">État de paiement collectif</h6>
                    <p class="text-muted small">Synthèse des rémunérations dues par période.</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('rapports.paiement-collectif') }}" class="btn btn-sm btn-uvci w-100">
                        <i class="fa-solid fa-gear me-1"></i> Générer
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="stat-icon purple mb-3"><i class="fa-solid fa-building-columns"></i></div>
                    <h6 class="fw-bold">Charge par département</h6>
                    <p class="text-muted small">Volume horaire consolidé par département et filière.</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('rapports.charge-departement') }}" class="btn btn-sm btn-uvci w-100">
                        <i class="fa-solid fa-gear me-1"></i> Générer
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-page>
