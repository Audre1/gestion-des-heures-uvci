<x-app-page title="Tableau de bord — Secrétariat" section="Général"
    subtitle="Gestion des activités pédagogiques — {{ $currentYear?->libelle ?? 'Année en cours' }}.">

    {{-- Cartes statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon green"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['enseignants'] }}</div>
                        <div class="stat-label">Enseignants</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon purple"><i class="fa-solid fa-book"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['cours_actifs'] }}</div>
                        <div class="stat-label">Cours actifs</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon blue"><i class="fa-solid fa-link"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['affectations'] }}</div>
                        <div class="stat-label">Affectations</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['activites_en_attente'] }}</div>
                        <div class="stat-label">En attente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Activités en attente de validation --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-list-check text-uvci-amber me-2"></i>Activités en attente de validation</span>
                    <a href="{{ route('activites.index') }}" class="small text-uvci-purple fw-semibold">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Enseignant</th>
                                <th>Cours</th>
                                <th>Type</th>
                                <th>Vol.</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activitesEnAttente as $a)
                                <tr>
                                    <td>{{ $a['date'] }}</td>
                                    <td>{{ $a['enseignant'] }}</td>
                                    <td>{{ $a['cours'] }}</td>
                                    <td>{{ $a['type'] }}</td>
                                    <td class="fw-semibold">{{ $a['volume'] }}</td>
                                    <td>
                                        <a href="{{ route('activites.index') }}" class="btn btn-sm btn-uvci">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-check-circle fa-2x mb-2 text-success"></i><br>
                                        Aucune activité en attente
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Dernières affectations --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-link text-uvci-green me-2"></i>Dernières affectations</span>
                    <a href="{{ route('affectations.index') }}" class="small text-uvci-purple fw-semibold">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Enseignant</th>
                                <th>Cours</th>
                                <th>Niv.</th>
                                <th>Sem.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dernieresAffectations as $a)
                                <tr>
                                    <td>{{ $a['enseignant'] }}</td>
                                    <td>{{ $a['cours'] }}</td>
                                    <td>{{ $a['niveau'] }}</td>
                                    <td>{{ $a['semestre'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-inbox fa-2x mb-2"></i><br>
                                        Aucune affectation récente
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Liens rapides --}}
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-bolt text-uvci-purple me-2"></i>Actions rapides</div>
                <div class="card-body text-center">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ route('enseignants.index') }}" class="btn btn-uvci-outline" style="background:#e6f7ee;border-color:#00a54e;color:#008a41;">
                            <i class="fa-solid fa-chalkboard-user me-1"></i> Gérer les enseignants
                        </a>
                        <a href="{{ route('affectations.index') }}" class="btn btn-uvci-outline" style="background:#f7e9f6;border-color:#91268f;color:#741d72;">
                            <i class="fa-solid fa-link me-1"></i> Gérer les affectations
                        </a>
                        <a href="{{ route('cours.index') }}" class="btn btn-uvci-outline" style="background:#dbeafe;border-color:#2563eb;color:#1d4ed8;">
                            <i class="fa-solid fa-book me-1"></i> Gérer les cours
                        </a>
                        <a href="{{ route('activites.index') }}" class="btn btn-uvci-outline" style="background:#fef3c7;border-color:#d97706;color:#92400e;">
                            <i class="fa-solid fa-list-check me-1"></i> Valider des activités
                        </a>
                        <a href="{{ route('volumes.index') }}" class="btn btn-uvci-outline" style="background:#cffafe;border-color:#0891b2;color:#155e75;">
                            <i class="fa-solid fa-hourglass-half me-1"></i> Volumes horaires
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-page>