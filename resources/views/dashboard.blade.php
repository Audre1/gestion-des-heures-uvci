<x-app-page title="Tableau de bord" section="Général"
    subtitle="Vue d'ensemble de l'activité pédagogique — Année académique 2024-2025.">

    {{-- Cartes statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon green"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div>
                        <div class="stat-value">248</div>
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
                        <div class="stat-value">512</div>
                        <div class="stat-label">Cours actifs</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon blue"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div>
                        <div class="stat-value">18 420 h</div>
                        <div class="stat-label">Volume horaire total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <div class="stat-value">1 240 h</div>
                        <div class="stat-label">Heures complémentaires</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Graphique (placeholder) --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-chart-column text-uvci-green me-2"></i>Volume horaire par
                        département</span>
                    <button class="btn btn-sm btn-light border">Ce semestre</button>
                </div>
                <div class="card-body">
                    {{-- Fausses barres pour illustrer le design --}}
                    @php
                        $bars = [
                            ['Informatique', 82, 'green'],
                            ['Gestion', 64, 'purple'],
                            ['Droit', 55, 'green'],
                            ['Lettres', 47, 'purple'],
                            ['Sciences', 71, 'green'],
                            ['Économie', 38, 'purple'],
                        ];
                    @endphp
                    <div class="d-flex align-items-end justify-content-around gap-3" style="height:230px">
                        @foreach ($bars as [$label, $h, $color])
                            <div class="text-center flex-fill">
                                <div class="mx-auto rounded-top"
                                    style="width:60%;height:{{ $h * 2 }}px;background:{{ $color === 'green' ? 'var(--uvci-green)' : 'var(--uvci-purple)' }};transition:height .3s">
                                </div>
                                <div class="small text-muted mt-2">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Répartition des activités --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><i class="fa-solid fa-chart-pie text-uvci-purple me-2"></i>Répartition des
                    activités</div>
                <div class="card-body">
                    @php
                        $activs = [
                            ['Création — Niv. 1', 40, 'var(--uvci-green)'],
                            ['Création — Niv. 2', 28, 'var(--uvci-purple)'],
                            ['Création — Niv. 3', 12, '#2563eb'],
                            ['Mise à jour', 20, '#d97706'],
                        ];
                    @endphp
                    @foreach ($activs as [$label, $pct, $c])
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $label }}</span><span class="fw-semibold">{{ $pct }}%</span>
                            </div>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar"
                                    style="width:{{ $pct }}%;background:{{ $c }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Dernières activités & enseignants en dépassement --}}
    <div class="row g-3 mt-1">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-list-check text-uvci-green me-2"></i>Activités récentes</span>
                    <a href="{{ route('activites.index') }}" class="small text-uvci-purple fw-semibold">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Enseignant</th>
                                <th>Type</th>
                                <th>Séq.</th>
                                <th>VHT</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $rows = [
                                    ['K. Kouassi', 'Création — Niv. 2', 40, '30h', 'Validée', 'green'],
                                    ['A. Traoré', 'Mise à jour — Niv. 1', 40, '8h', 'En cours', 'amber'],
                                    ['M. Diabaté', 'Création — Niv. 3', 80, '120h', 'Validée', 'green'],
                                    ['S. Koné', 'Création — Niv. 1', 40, '16h', 'En cours', 'amber'],
                                ];
                            @endphp
                            @foreach ($rows as [$n, $t, $s, $v, $st, $c])
                                <tr>
                                    <td><span
                                            class="avatar-sm me-2">{{ strtoupper(substr($n, 0, 1)) }}</span>{{ $n }}
                                    </td>
                                    <td>{{ $t }}</td>
                                    <td>{{ $s }}</td>
                                    <td class="fw-semibold">{{ $v }}</td>
                                    <td><span class="badge badge-soft-{{ $c }}">{{ $st }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Enseignants
                    en dépassement de charge</div>
                <ul class="list-group list-group-flush">
                    @php
                        $over = [
                            ['Prof. B. Yao', '228h / 192h', '+36h'],
                            ['Dr. F. Ouattara', '215h / 192h', '+23h'],
                            ['Dr. C. N\'Guessan', '208h / 192h', '+16h'],
                        ];
                    @endphp
                    @foreach ($over as [$n, $r, $d])
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar-sm">{{ strtoupper(substr($n, 0, 1)) }}</span>
                                <div>
                                    <div class="fw-semibold" style="line-height:1.1">{{ $n }}</div>
                                    <div class="text-muted small">{{ $r }}</div>
                                </div>
                            </div>
                            <span class="badge badge-soft-red">{{ $d }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

</x-app-page>
