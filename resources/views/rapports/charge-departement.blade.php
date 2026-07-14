<x-app-page title="Charge par département" section="Rapports & Statistiques" icon="fa-solid fa-building-columns"
    subtitle="Volume horaire consolidé par département et filière.">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('rapports.charge-departement.generate') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Année académique</label>
                            <select name="annee_id" class="form-select">
                                <option value="">Année active (par défaut)</option>
                                @foreach($annees as $annee)
                                    <option value="{{ $annee->id }}" {{ $annee->statut === 'en_cours' ? 'selected' : '' }}>
                                        {{ $annee->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Format d'export <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" value="pdf" id="formatPdf" checked>
                                    <label class="form-check-label" for="formatPdf">
                                        <i class="fa-solid fa-file-pdf text-danger me-1"></i> PDF
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" value="excel" id="formatExcel">
                                    <label class="form-check-label" for="formatExcel">
                                        <i class="fa-solid fa-file-excel text-uvci-green me-1"></i> Excel
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-uvci flex-fill">
                                <i class="fa-solid fa-download me-1"></i> Générer le rapport
                            </button>
                            <a href="{{ route('rapports.index') }}" class="btn btn-light border">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-page>
