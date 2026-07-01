<x-app-page title="Mon volume horaire" section="Espace Enseignant" icon="fa-solid fa-stopwatch"
    subtitle="Total de vos heures créditées pour l'année 2024-2025.">

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon green"><i class="fa-solid fa-hourglass-half"></i></div>
            <div><div class="stat-value">228 h</div><div class="stat-label">VHT réalisé</div></div>
        </div></div></div>
        <div class="col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon blue"><i class="fa-solid fa-briefcase"></i></div>
            <div><div class="stat-value">192 h</div><div class="stat-label">Service statutaire</div></div>
        </div></div></div>
        <div class="col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
            <div><div class="stat-value">36 h</div><div class="stat-label">Heures complémentaires</div></div>
        </div></div></div>
        <div class="col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon purple"><i class="fa-solid fa-list-check"></i></div>
            <div><div class="stat-value">12</div><div class="stat-label">Activités validées</div></div>
        </div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-chart-line text-uvci-green me-2"></i>Évolution mensuelle du volume horaire</div>
                <div class="card-body">
                    @php $months = [['Sep',20],['Oct',46],['Nov',60],['Déc',38],['Jan',32],['Fév',32]]; @endphp
                    <div class="d-flex align-items-end justify-content-around gap-3" style="height:210px">
                        @foreach($months as $i => [$m, $h])
                            <div class="text-center flex-fill">
                                <div class="mx-auto rounded-top" style="width:55%;height:{{ $h*3 }}px;background:{{ $i % 2 ? 'var(--uvci-purple)' : 'var(--uvci-green)' }}"></div>
                                <div class="small text-muted mt-2">{{ $m }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><i class="fa-solid fa-circle-info text-uvci-purple me-2"></i>Synthèse</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Grade</span><span class="fw-semibold">Professeur</span></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Statut</span><span class="badge badge-soft-green">Permanent</span></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Taux horaire</span><span class="fw-semibold">25 000 FCFA</span></div>
                    <div class="d-flex justify-content-between py-2"><span class="text-muted">Charge globale</span><span class="fw-semibold text-uvci-purple">118 %</span></div>
                    <a href="{{ route('espace.documents') }}" class="btn btn-uvci w-100 mt-3"><i class="fa-solid fa-download me-1"></i> Télécharger mon récapitulatif</a>
                </div>
            </div>
        </div>
    </div>
</x-app-page>
