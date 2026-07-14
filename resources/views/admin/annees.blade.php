<x-app-page title="Années académiques" section="Administration" icon="fa-solid fa-calendar-days"
    subtitle="Définissez les périodes et calendriers académiques.">

    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addAnneeModal">
            <i class="fa-solid fa-plus me-1"></i>
            Nouvelle année
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une année..." :count="$annees->count()" :show-filters="false"
        export-title="Années académiques">
        <x-slot:head>
            <th>Libellé</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot:head>

        @forelse($annees as $annee)
            <tr {{ $annee->statut === 'en_cours' ? 'class="table-active border-start border-4 border-success"' : '' }}>
                <td class="fw-semibold">
                    @if ($annee->statut === 'en_cours')
                        <i class="fa-solid fa-star text-warning me-2"></i>
                    @else
                        <i class="fa-solid fa-calendar text-uvci-purple me-2"></i>
                    @endif
                    {{ $annee->libelle }}
                    @if ($annee->statut === 'en_cours')
                        <span class="badge badge-soft-success ms-2">Année en cours</span>
                    @endif
                </td>
                <td>{{ $annee->date_debut ? $annee->date_debut->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ $annee->date_fin ? $annee->date_fin->format('d/m/Y') : 'N/A' }}</td>
                <td>
                    @if ($annee->statut === 'en_cours')
                        <span class="badge badge-soft-green">En cours</span>
                    @elseif($annee->statut === 'cloturee')
                        <span class="badge badge-soft-gray">Clôturée</span>
                    @else
                        <span class="badge badge-soft-amber">À venir</span>
                    @endif
                </td>
                <td>
                    <div class="action-btns justify-content-end">
                        @if ($annee->statut !== 'en_cours')
                            <button type="button" class="btn btn-light border" title="Activer" data-bs-toggle="modal"
                                data-bs-target="#activateAnneeModal{{ $annee->id }}">
                                <i class="fa-solid fa-power-off text-uvci-green"></i>
                            </button>
                        @endif

                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editAnneeModal{{ $annee->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                            data-bs-target="#deleteAnneeModal{{ $annee->id }}">
                            <i class="fa-solid fa-trash text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>

            {{-- Modale de modification --}}
            <div class="modal fade" id="editAnneeModal{{ $annee->id }}" tabindex="-1"
                aria-labelledby="editAnneeModalLabel{{ $annee->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg mt-4">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <div>
                                <h5 class="modal-title fw-bold" id="editAnneeModalLabel{{ $annee->id }}">
                                    <i class="fa-solid fa-calendar-pen me-2 text-primary"></i>
                                    Modifier l'année académique
                                </h5>
                                <small class="text-muted">Modification de l'année {{ $annee->libelle }}.</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <form action="{{ route('annees.update', $annee->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Libellé <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="libelle" class="form-control"
                                            value="{{ old('libelle', $annee->libelle) }}" placeholder="Ex : 2026-2027"
                                            required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Date de début <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="date_debut" class="form-control"
                                            value="{{ old('date_debut', $annee->date_debut ? $annee->date_debut->format('Y-m-d') : '') }}"
                                            required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Date de fin <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="date_fin" class="form-control"
                                            value="{{ old('date_fin', $annee->date_fin ? $annee->date_fin->format('Y-m-d') : '') }}"
                                            required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Statut <span
                                                class="text-danger">*</span></label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input type="radio" name="statut"
                                                    id="statutAvenir{{ $annee->id }}" value="a_venir"
                                                    class="form-check-input"
                                                    {{ old('statut', $annee->statut) === 'a_venir' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="statutAvenir{{ $annee->id }}">
                                                    À venir
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" name="statut"
                                                    id="statutEnCours{{ $annee->id }}" value="en_cours"
                                                    class="form-check-input"
                                                    {{ old('statut', $annee->statut) === 'en_cours' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="statutEnCours{{ $annee->id }}">
                                                    En cours
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" name="statut"
                                                    id="statutCloturee{{ $annee->id }}" value="cloturee"
                                                    class="form-check-input"
                                                    {{ old('statut', $annee->statut) === 'cloturee' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="statutCloturee{{ $annee->id }}">
                                                    Clôturée
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4 mb-0">
                                    <i class="fa-solid fa-circle-info me-2"></i>
                                    Vérifiez que la date de fin est postérieure à la date de début.
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

            {{-- Modale d'activation --}}
            @if ($annee->statut !== 'actif')
                <div class="modal fade" id="activateAnneeModal{{ $annee->id }}" tabindex="-1"
                    aria-labelledby="activateAnneeModalLabel{{ $annee->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title fw-bold" id="activateAnneeModalLabel{{ $annee->id }}">
                                    <i class="fa-solid fa-power-off me-2"></i>
                                    Activer l'année académique
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Fermer"></button>
                            </div>

                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Voulez-vous définir l'année <strong>{{ $annee->libelle }}</strong> comme année
                                    académique active ?
                                </p>

                                <div class="alert alert-warning mb-0">
                                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                    L'année actuellement active sera automatiquement désactivée.
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                    Annuler
                                </button>

                                <form action="{{ route('annees.activate', $annee->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-check me-1"></i>
                                        Oui, activer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modale de suppression --}}
            <div class="modal fade" id="deleteAnneeModal{{ $annee->id }}" tabindex="-1"
                aria-labelledby="deleteAnneeModalLabel{{ $annee->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteAnneeModalLabel{{ $annee->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">
                                Voulez-vous vraiment supprimer l'année académique suivante ?
                            </p>

                            <div class="alert alert-warning mb-0">
                                <strong>
                                    <i class="fa-solid fa-calendar me-2"></i>
                                    {{ $annee->libelle }}
                                </strong><br>
                                <span class="small">Du
                                    {{ $annee->date_debut ? $annee->date_debut->format('d/m/Y') : 'N/A' }} au
                                    {{ $annee->date_fin ? $annee->date_fin->format('d/m/Y') : 'N/A' }}</span>
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

                            <form action="{{ route('annees.destroy', $annee->id) }}" method="POST">
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
                <td colspan="5" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune année académique trouvée.</p>
                        <small>Commencez par ajouter une nouvelle année académique.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale d'ajout --}}
    <div class="modal fade" id="addAnneeModal" tabindex="-1" aria-labelledby="addAnneeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addAnneeModalLabel">
                            <i class="fa-solid fa-calendar-plus me-2 text-primary"></i>
                            Nouvelle année académique
                        </h5>
                        <small class="text-muted">Définissez la période de la nouvelle année académique.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('annees.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Libellé <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="libelle"
                                    class="form-control @error('libelle') is-invalid @enderror"
                                    value="{{ old('libelle') }}" placeholder="Ex : 2026-2027" required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date de début <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="date_debut"
                                    class="form-control @error('date_debut') is-invalid @enderror"
                                    value="{{ old('date_debut') }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date de fin <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="date_fin"
                                    class="form-control @error('date_fin') is-invalid @enderror"
                                    value="{{ old('date_fin') }}" required>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Une seule année académique peut être active à la fois.
                            Vous pourrez l'activer après sa création.
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer l'année
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addAnneeModal = new bootstrap.Modal(document.getElementById('addAnneeModal'));
                addAnneeModal.show();
            });
        </script>
    @endif

</x-app-page>
