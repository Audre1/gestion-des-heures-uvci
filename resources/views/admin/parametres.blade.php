<x-app-page title="Paramètres de calcul" section="Administration" icon="fa-solid fa-sliders"
    subtitle="Coefficients et règles de calcul des volumes horaires (VHT).">

    <form method="POST" action="{{ route('parametres.update') }}">
        @method('PUT')
        @csrf

        <div class="row g-3">

            {{-- Grille des coefficients --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-table-cells text-uvci-green me-2"></i>
                        Grille des coefficients par séquence
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Type / Niveau</th>
                                    @foreach ([10, 20, 30] as $heures)
                                        <th>
                                            {{ $heures }}h
                                            ({{ $parametres->calculerSequencesDepuisHeures($heures) }} séq.)
                                        </th>
                                    @endforeach
                                    <th>Coeff./séq.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parametres->getGrille() as $ligne)
                                    <tr class="{{ $ligne['type'] === 'maj' ? 'table-light' : '' }}">

                                        {{-- Libellé --}}
                                        <td class="fw-semibold">
                                            {{ $ligne['type'] === 'creation' ? 'Création' : 'Mise à jour' }}
                                            — Niv. {{ $ligne['niveau'] }}
                                            @if ($ligne['niveau'] === 1)
                                                (simple)
                                            @elseif($ligne['niveau'] === 2)
                                                (interactif)
                                            @else
                                                (serious games)
                                            @endif
                                        </td>

                                        {{-- Valeurs VHT calculées (lecture seule) --}}
                                        @foreach ($ligne['valeurs'] as $valeur)
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $valeur['vht'] }}h
                                                </span>
                                            </td>
                                        @endforeach

                                        {{-- Coefficient --}}
                                        <td>
                                            @if ($ligne['type'] === 'creation')
                                                {{-- Création : input modifiable --}}
                                                @php
                                                    $champCoeff = 'coeff_creation_niv' . $ligne['niveau'];
                                                @endphp
                                                <input type="number" step="0.001" name="{{ $champCoeff }}"
                                                    class="form-control form-control-sm @error($champCoeff) is-invalid @enderror"
                                                    value="{{ $parametres->$champCoeff }}" required>
                                                @error($champCoeff)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @else
                                                {{-- Mise à jour : calculé dynamiquement, lecture seule --}}
                                                <span class="badge bg-light text-uvci-purple border">
                                                    {{ $ligne['coeff'] }}
                                                </span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Note explicative --}}
                    <div class="card-footer text-muted small">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Les coefficients de mise à jour sont calculés automatiquement :
                        <strong>coeff_création × (1 - réduction / 100)</strong>
                    </div>

                </div>
            </div>

            {{-- Règles générales --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-gear text-uvci-purple me-2"></i>
                        Règles générales
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">1 crédit correspond à</label>
                            <div class="input-group">
                                <input type="number" name="heures_par_credit"
                                    class="form-control @error('heures_par_credit') is-invalid @enderror"
                                    value="{{ $parametres->heures_par_credit }}" required>
                                <span class="input-group-text">heures</span>
                                @error('heures_par_credit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">1 crédit correspond à</label>
                            <div class="input-group">
                                <input type="number" name="sequences_par_credit"
                                    class="form-control @error('sequences_par_credit') is-invalid @enderror"
                                    value="{{ $parametres->sequences_par_credit }}" required>
                                <span class="input-group-text">séquences</span>
                                @error('sequences_par_credit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service statutaire (permanent)</label>
                            <div class="input-group">
                                <input type="number" name="service_statutaire"
                                    class="form-control @error('service_statutaire') is-invalid @enderror"
                                    value="{{ $parametres->service_statutaire }}" required>
                                <span class="input-group-text">heures / an</span>
                                @error('service_statutaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Réduction mise à jour</label>
                            <div class="input-group">
                                <input type="number" name="reduction_mise_a_jour"
                                    class="form-control @error('reduction_mise_a_jour') is-invalid @enderror"
                                    value="{{ $parametres->reduction_mise_a_jour }}" required>
                                <span class="input-group-text">%</span>
                                @error('reduction_mise_a_jour')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-uvci w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer les paramètres
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </form>
</x-app-page>
