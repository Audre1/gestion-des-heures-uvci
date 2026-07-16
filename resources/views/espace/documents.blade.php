<x-app-page title="Mes documents" section="Espace Enseignant" icon="fa-solid fa-download"
    subtitle="Téléchargez vos récapitulatifs et fiches individuelles.">

    {{-- Sélecteur d'année académique --}}
    <div class="row g-3 mb-4">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <label class="form-label fw-semibold"><i
                            class="fa-solid fa-calendar-days text-uvci-purple me-2"></i>Année académique</label>
                    <select id="selectAnnee" class="form-select" onchange="updateDocumentLinks()">
                        @foreach ($annees as $annee)
                            <option value="{{ $annee->id }}" {{ $annee->statut === 'en_cours' ? 'selected' : '' }}>
                                {{ $annee->libelle }} {{ $annee->statut === 'en_cours' ? '(En cours)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @php
            $docs = [
                [
                    'Récapitulatif d\'activités',
                    'Bilan complet de vos activités pédagogiques.',
                    'fa-folder-tree',
                    'green',
                    'documents.recapitulatif',
                ],
                [
                    'Fiche individuelle',
                    'Vos informations et charge horaire consolidée.',
                    'fa-id-card',
                    'purple',
                    'documents.fiche',
                ],
                [
                    'État des heures',
                    'Détail de vos volumes horaires et heures complémentaires.',
                    'fa-clock',
                    'amber',
                    'documents.heures',
                ],
            ];
        @endphp
        @foreach ($docs as [$title, $desc, $icon, $col, $route])
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon {{ $col }} mx-auto mb-3"><i
                                class="fa-solid {{ $icon }}"></i></div>
                        <h6 class="fw-bold">{{ $title }}</h6>
                        <p class="text-muted small">{{ $desc }}</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route($route) }}" class="btn btn-uvci w-100 document-link"
                            data-route="{{ $route }}">
                            <i class="fa-solid fa-file-pdf me-1"></i> Télécharger (PDF)
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script>
            const routes = {
                'documents.recapitulatif': "{{ route('documents.recapitulatif') }}",
                'documents.fiche': "{{ route('documents.fiche') }}",
                'documents.heures': "{{ route('documents.heures') }}",
            };

            function updateDocumentLinks() {
                const anneeId = document.getElementById('selectAnnee').value;
                document.querySelectorAll('.document-link').forEach(link => {
                    const route = link.dataset.route;
                    link.href = routes[route] + '?annee=' + anneeId;
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateDocumentLinks();
            });
        </script>
    @endpush
</x-app-page>
