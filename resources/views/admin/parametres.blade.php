<x-app-page title="Paramètres de calcul" section="Administration" icon="fa-solid fa-sliders"
    subtitle="Coefficients et règles de calcul des volumes horaires (VHT).">

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-table-cells text-uvci-green me-2"></i>Grille des coefficients par séquence</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Type / Niveau</th><th>10h (40 séq.)</th><th>20h (80 séq.)</th><th>30h (120 séq.)</th><th>Coeff./séq.</th></tr>
                        </thead>
                        <tbody>
                            @php
                                $grille = [
                                    ['Création — Niv. 1 (simple)', '16h', '32h', '48h', '0,40'],
                                    ['Création — Niv. 2 (interactif)', '30h', '60h', '90h', '0,75'],
                                    ['Création — Niv. 3 (serious games)', '60h', '120h', '180h', '1,50'],
                                    ['Mise à jour — Niv. 1', '8h', '16h', '24h', '0,20'],
                                    ['Mise à jour — Niv. 2', '15h', '30h', '45h', '0,375'],
                                    ['Mise à jour — Niv. 3', '30h', '60h', '90h', '0,75'],
                                ];
                            @endphp
                            @foreach($grille as [$t, $a, $b, $c, $co])
                                <tr>
                                    <td class="fw-semibold">{{ $t }}</td><td>{{ $a }}</td><td>{{ $b }}</td><td>{{ $c }}</td>
                                    <td><span class="badge badge-soft-purple">{{ $co }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-gear text-uvci-purple me-2"></i>Règles générales</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">1 crédit correspond à</label>
                        <div class="input-group"><input class="form-control" value="10"><span class="input-group-text">heures</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">1 crédit correspond à</label>
                        <div class="input-group"><input class="form-control" value="40"><span class="input-group-text">séquences</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service statutaire (permanent)</label>
                        <div class="input-group"><input class="form-control" value="192"><span class="input-group-text">heures / an</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Réduction mise à jour</label>
                        <div class="input-group"><input class="form-control" value="50"><span class="input-group-text">%</span></div>
                    </div>
                    <button class="btn btn-uvci w-100"><i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer les paramètres</button>
                </div>
            </div>
        </div>
    </div>
</x-app-page>
