<x-app-page title="Taux horaires" section="Administration" icon="fa-solid fa-money-bill-wave"
    subtitle="Barèmes de rémunération par grade et par année académique.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addTauxModal">
            <i class="fa-solid fa-plus me-1"></i>
            Nouveau taux
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un taux..." :count="$taux->count()" export-title="Liste taux horaires">
        <x-slot:filters>
            <label class="dt-filter-label">Grade</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(0)">
                <option value="">Tous</option>
                @foreach (collect($taux)->pluck('grade.libelle')->unique()->filter()->sort()->values() as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
            <label class="dt-filter-label">Statut</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(5)">
                <option value="">Tous</option>
                <option value="Actif">Actif</option>
                <option value="Expiré">Expiré</option>
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Grade</th>
            <th>Montant</th>
            <th>Devise</th>
            <th>Année</th>
            <th>Application</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot:head>

        @forelse ($taux as $t)
            @php
                $statut = is_null($t->date_fin_application) ? 'Actif' : 'Expiré';
                $color = is_null($t->date_fin_application) ? 'green' : 'gray';
                $montantFormate = number_format($t->montant, 0, ',', ' ');
            @endphp
            <tr>
                <td class="fw-semibold">{{ $t->grade?->libelle ?? 'N/A' }}</td>
                <td class="fw-semibold text-uvci-green">{{ $montantFormate }}</td>
                <td>{{ $t->devise }}</td>
                <td>{{ $t->anneeAcademique?->libelle ?? 'N/A' }}</td>
                <td>{{ $t->date_application->format('d/m/Y') }}</td>
                <td>
                    <span class="badge badge-soft-{{ $color }}">{{ $statut }}</span>
                </td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editTauxModal{{ $t->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                            data-bs-target="#deleteTauxModal{{ $t->id }}">
                            <i class="fa-solid fa-trash text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>

            {{-- Modale de modification --}}
            <div class="modal fade" id="editTauxModal{{ $t->id }}" tabindex="-1"
                aria-labelledby="editTauxModalLabel{{ $t->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg mt-4">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <div>
                                <h5 class="modal-title fw-bold" id="editTauxModalLabel{{ $t->id }}">
                                    <i class="fa-solid fa-money-bill-wave me-2 text-primary"></i>
                                    Modifier le taux horaire
                                </h5>
                                <small class="text-muted">
                                    Modification du taux de {{ $t->grade?->libelle ?? 'N/A' }} pour l'année
                                    {{ $t->anneeAcademique?->libelle ?? 'N/A' }}.
                                </small>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <form action="{{ route('taux.update', $t->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Grade <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_grade"
                                            class="form-select @error('id_grade') is-invalid @enderror" required>
                                            <option value="">Sélectionner un grade</option>
                                            @foreach ($grades as $grade)
                                                <option value="{{ $grade->id }}"
                                                    {{ old('id_grade', $t->id_grade) == $grade->id ? 'selected' : '' }}>
                                                    {{ $grade->libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_grade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Année académique <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_annee"
                                            class="form-select @error('id_annee') is-invalid @enderror" required>
                                            <option value="">Sélectionner une année</option>
                                            @foreach ($annees as $annee)
                                                <option value="{{ $annee->id }}"
                                                    {{ old('id_annee', $t->id_annee) == $annee->id ? 'selected' : '' }}>
                                                    {{ $annee->libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_annee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Montant horaire <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="montant"
                                                class="form-control @error('montant') is-invalid @enderror"
                                                value="{{ old('montant', $t->montant) }}" min="0" step="1"
                                                required>
                                            <span class="input-group-text">{{ old('devise', $t->devise) }}</span>
                                        </div>
                                        @error('montant')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Devise <span class="text-danger">*</span>
                                        </label>
                                        <select name="devise" class="form-select @error('devise') is-invalid @enderror"
                                            required>
                                            <option value="XOF"
                                                {{ old('devise', $t->devise) === 'XOF' ? 'selected' : '' }}>XOF
                                            </option>
                                            <option value="FCFA"
                                                {{ old('devise', $t->devise) === 'FCFA' ? 'selected' : '' }}>FCFA
                                            </option>
                                            <option value="EUR"
                                                {{ old('devise', $t->devise) === 'EUR' ? 'selected' : '' }}>EUR
                                            </option>
                                            <option value="USD"
                                                {{ old('devise', $t->devise) === 'USD' ? 'selected' : '' }}>USD
                                            </option>
                                        </select>
                                        @error('devise')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Date d'application <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="date_application"
                                            class="form-control @error('date_application') is-invalid @enderror"
                                            value="{{ old('date_application', $t->date_application->format('Y-m-d')) }}"
                                            required>
                                        @error('date_application')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Date de fin
                                        </label>
                                        <input type="date" name="date_fin_application"
                                            class="form-control @error('date_fin_application') is-invalid @enderror"
                                            value="{{ old('date_fin_application', $t->date_fin_application ? $t->date_fin_application->format('Y-m-d') : '') }}">
                                        @error('date_fin_application')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Laisser vide si le taux est toujours actif</small>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4 mb-0">
                                    <i class="fa-solid fa-circle-info me-2"></i>
                                    Le montant correspond au taux appliqué pour une heure complémentaire.
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                    Annuler
                                </button>

                                <button type="submit" class="btn btn-uvci">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modale de suppression --}}
            <div class="modal fade" id="deleteTauxModal{{ $t->id }}" tabindex="-1"
                aria-labelledby="deleteTauxModalLabel{{ $t->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteTauxModalLabel{{ $t->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">
                                Voulez-vous vraiment supprimer ce taux horaire ?
                            </p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $t->grade->libelle }}</strong><br>
                                <span class="small">
                                    {{ number_format($t->montant, 0, ',', ' ') }} {{ $t->devise }} / heure — Année
                                    {{ $t->anneeAcademique->libelle }}
                                </span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                Annuler
                            </button>

                            <form action="{{ route('taux.destroy', $t->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    <i class="fa-solid fa-trash me-1"></i>
                                    Oui, supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-money-bill-wave fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun taux horaire trouvé.</p>
                        <small>Commencez par ajouter un nouveau taux horaire.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale d'ajout --}}
    <div class="modal fade" id="addTauxModal" tabindex="-1" aria-labelledby="addTauxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addTauxModalLabel">
                            <i class="fa-solid fa-money-bill-wave me-2 text-primary"></i>
                            Nouveau taux horaire
                        </h5>
                        <small class="text-muted">
                            Définissez le barème de rémunération pour un grade et une année académique.
                        </small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('taux.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Grade <span class="text-danger">*</span>
                                </label>
                                <select name="id_grade" class="form-select @error('id_grade') is-invalid @enderror"
                                    required>
                                    <option value="">Sélectionner un grade</option>
                                    @foreach ($grades as $grade)
                                        <option value="{{ $grade->id }}"
                                            {{ old('id_grade') == $grade->id ? 'selected' : '' }}>
                                            {{ $grade->libelle }}</option>
                                    @endforeach
                                </select>
                                @error('id_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Année académique <span class="text-danger">*</span>
                                </label>
                                <select name="id_annee" class="form-select @error('id_annee') is-invalid @enderror"
                                    required>
                                    <option value="">Sélectionner une année</option>
                                    @foreach ($annees as $annee)
                                        <option value="{{ $annee->id }}"
                                            {{ old('id_annee') == $annee->id ? 'selected' : '' }}>
                                            {{ $annee->libelle }}</option>
                                    @endforeach
                                </select>
                                @error('id_annee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Montant horaire <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="montant"
                                        class="form-control @error('montant') is-invalid @enderror"
                                        placeholder="Ex : 25000" min="0" step="1"
                                        value="{{ old('montant') }}" required>
                                    <span class="input-group-text">XOF</span>
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Devise <span class="text-danger">*</span>
                                </label>
                                <select name="devise" class="form-select @error('devise') is-invalid @enderror"
                                    required>
                                    <option value="XOF" {{ old('devise') == 'XOF' ? 'selected' : '' }}>XOF</option>
                                    <option value="FCFA" {{ old('devise') == 'FCFA' ? 'selected' : '' }}>FCFA
                                    </option>
                                    <option value="EUR" {{ old('devise') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="USD" {{ old('devise') == 'USD' ? 'selected' : '' }}>USD</option>
                                </select>
                                @error('devise')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Date d'application <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="date_application"
                                    class="form-control @error('date_application') is-invalid @enderror"
                                    value="{{ old('date_application') }}" required>
                                @error('date_application')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Date de fin
                                </label>
                                <input type="date" name="date_fin_application"
                                    class="form-control @error('date_fin_application') is-invalid @enderror"
                                    value="{{ old('date_fin_application') }}">
                                @error('date_fin_application')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Laisser vide si le taux est toujours actif</small>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Un seul taux doit être défini pour un même grade et une même année académique.
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            Annuler
                        </button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer le taux
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addTauxModal = new bootstrap.Modal(document.getElementById('addTauxModal'));
                addTauxModal.show();
            });
        </script>
    @endif

</x-app-page>
