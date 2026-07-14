<x-app-page title="Mon volume horaire" section="Espace Enseignant" icon="fa-solid fa-stopwatch"
    subtitle="Total de vos heures créditées pour l'année {{ $anneeActive->libelle ?? 'en cours' }}.">

    @push('styles')
        <style>
            .chart-container {
                position: relative;
                height: 300px;
                width: 100%;
            }
        </style>
    @endpush

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon green"><i class="fa-solid fa-hourglass-half"></i></div>

                    <div class="stat-value">{{ $volumeRealise }} h</div>
                    <div class="stat-label">VHT réalisé</div>

                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon blue"><i class="fa-solid fa-briefcase"></i></div>

                    <div>
                        <div class="stat-value">{{ $serviceStatutaire }} h</div>
                        <div class="stat-label">Service statutaire</div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>

                    <div>
                        <div class="stat-value">{{ $heuresComplementaires }} h</div>
                        <div class="stat-label">Heures complémentaires</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon purple"><i class="fa-solid fa-list-check"></i></div>

                    <div>
                        <div class="stat-value">{{ $nombreActivites }}</div>
                        <div class="stat-label">Activités validées</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-chart-line text-uvci-green me-2"></i>Évolution mensuelle
                    du volume horaire</div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="volumeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><i class="fa-solid fa-circle-info text-uvci-purple me-2"></i>Synthèse</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Grade</span>
                        <span class="fw-semibold">
                            {{ $enseignant->grade->libelle ?? 'Non défini' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span
                            class="text-muted">Statut</span> <span class="badge badge-soft-green">
                            {{ $enseignant->statut ?? 'Non défini' }}
                        </span> </div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Taux
                            horaire</span> <span class="fw-semibold">
                            {{ number_format($tauxHoraireMontant ?? 0, 0, ',', ' ') }} FCFA
                        </span> </div>
                    <div class="d-flex justify-content-between py-2"><span class="text-muted">Charge globale</span>
                        <span class="fw-semibold text-uvci-purple">
                            {{ $chargeGlobale }} %
                        </span>
                    </div>
                    <a href="{{ route('espace.documents') }}" class="btn btn-uvci w-100 mt-3"><i
                            class="fa-solid fa-download me-1"></i> Télécharger mon récapitulatif</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('volumeChart');
                if (ctx) {
                    const labels = {!! json_encode($chartLabels) !!};
                    const data = {!! json_encode($chartData) !!};

                    console.log('Chart labels:', labels);
                    console.log('Chart data:', data);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Volume horaire (h)',
                                data: data,
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgb(34, 197, 94)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
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
