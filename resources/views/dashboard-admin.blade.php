<x-app-page title="Tableau de bord" section="Général"
    subtitle="Vue d'ensemble de l'activité pédagogique — {{ $currentYear?->libelle ?? 'Année en cours' }}.">

    @push('styles')
        <style>
            .chart-container {
                position: relative;
                height: 300px;
                width: 100%;
            }
        </style>
    @endpush

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
                        <div class="stat-label">Cours affectés</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon blue"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div>
                        <div class="stat-value">{{ number_format($stats['volume_horaire_total'], 0, ',', ' ') }} h</div>
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
                        <div class="stat-value">{{ number_format($stats['heures_complementaires'], 0, ',', ' ') }} h
                        </div>
                        <div class="stat-label">Heures complémentaires</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Graphique : Volume horaire par département --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-chart-column text-uvci-green me-2"></i>Volume horaire par
                        département</span>
                    <span class="badge badge-soft-purple">{{ $currentYear?->libelle ?? 'N/A' }}</span>
                </div>
                <div class="card-body">
                    @if ($volumesParDepartement->isNotEmpty())
                        <div class="chart-container">
                            <canvas id="volumeDepartementChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-chart-simple fa-3x mb-3"></i>
                            <p class="mb-0">Aucune donnée de volume horaire pour cette année.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Répartition des activités --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><i class="fa-solid fa-chart-pie text-uvci-purple me-2"></i>Répartition des
                    activités</div>
                <div class="card-body">
                    @if ($repartitionActivites->isNotEmpty())
                        @php
                            $colors = [
                                'var(--uvci-green)',
                                'var(--uvci-purple)',
                                '#2563eb',
                                '#d97706',
                                '#dc2626',
                                '#0891b2',
                            ];
                        @endphp
                        @foreach ($repartitionActivites as $i => $item)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>{{ $item['label'] }}</span>
                                    <span class="fw-semibold">{{ $item['pct'] }}%</span>
                                </div>
                                <div class="progress" style="height:8px">
                                    <div class="progress-bar"
                                        style="width:{{ $item['pct'] }}%;background:{{ $colors[$i % count($colors)] }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-chart-pie fa-3x mb-3"></i>
                            <p class="mb-0">Aucune activité enregistrée.</p>
                        </div>
                    @endif
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
                                <th>Cours</th>
                                <th>VHT</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activitesRecentes as $a)
                                <tr>
                                    <td>
                                        <span
                                            class="avatar-sm me-2">{{ strtoupper(substr($a['enseignant'], 0, 1)) }}</span>
                                        {{ $a['enseignant'] }}
                                    </td>
                                    <td>{{ $a['type'] }}</td>
                                    <td><span class="font-monospace">{{ $a['cours'] }}</span></td>
                                    <td class="fw-semibold">{{ $a['volume'] }}</td>
                                    <td>
                                        <span
                                            class="badge badge-soft-{{ $a['statut_badge'] }}">{{ $a['statut'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-inbox fa-2x mb-2"></i><br>
                                        Aucune activité récente
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Enseignants en dépassement
                        de charge</span>
                    <span class="small text-muted">Seuil : {{ $serviceStatutaire }}h</span>
                </div>
                @if ($enseignants->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach ($enseignants as $e)
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-sm">{{ strtoupper(substr($e['nom'], 0, 1)) }}</span>
                                    <div>
                                        <div class="fw-semibold" style="line-height:1.1">{{ $e['nom'] }}</div>
                                        <div class="text-muted small">{{ $e['vht'] }}h / {{ $e['service'] }}h
                                        </div>
                                    </div>
                                </div>
                                <span class="badge badge-soft-red">+{{ $e['complementaires'] }}h</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="card-body text-center py-4 text-muted">
                        <i class="fa-solid fa-check-circle fa-2x mb-2 text-success"></i><br>
                        Aucun enseignant en dépassement
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('volumeDepartementChart');
                if (ctx) {
                    const labels = {!! json_encode($chartLabels) !!};
                    const data = {!! json_encode($chartData) !!};

                    console.log('Chart labels:', labels);
                    console.log('Chart data:', data);

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Volume horaire (h)',
                                data: data,
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: 'rgb(34, 197, 94)',
                                borderWidth: 2,
                                borderRadius: 4,
                                hoverBackgroundColor: 'rgba(34, 197, 94, 0.9)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' h';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return value + ' h';
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                } else {
                    console.error('Canvas element not found');
                }
            });
        </script>
    @endpush
</x-app-page>
