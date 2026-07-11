<x-app-page title="Grades" section="Gestion pédagogique" icon="fa-solid fa-ranking-star"
    subtitle="Niveaux hiérarchiques académiques et taux horaires associés.">
    <x-slot:actions>
        <button class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addGrade">
            <i class="fa-solid fa-plus me-1"></i> Nouveau grade
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un grade..." :count="$grades->count()" :show-filters="false">
        <x-slot:head>
            <th>Libellé</th>
            <th>Enseignants</th>
            <th>Date création</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @forelse($grades as $grade)
            <tr>
                <td class="fw-semibold"><i class="fa-solid fa-medal text-uvci-purple me-2"></i>{{ $grade->libelle }}
                </td>
                <td><span
                        class="badge badge-soft-{{ $grade->enseignants_count > 0 ? 'green' : 'gray' }}">{{ $grade->enseignants_count }}</span>
                </td>
                <td>{{ $grade->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" data-bs-toggle="modal"
                            data-bs-target="#editGrade{{ $grade->id }}" title="Modifier">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>
                        @if ($grade->enseignants_count == 0)
                            <button class="btn btn-light border" data-bs-toggle="modal"
                                data-bs-target="#deleteGrade{{ $grade->id }}" title="Supprimer">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button class="btn btn-light border" disabled
                                title="Impossible de supprimer (enseignants associés)">
                                <i class="fa-solid fa-trash text-muted"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-ranking-star fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun grade trouvé.</p>
                        <small>Commencez par ajouter un nouveau grade.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <!-- Modal Ajout -->
    <div class="modal fade" id="addGrade" tabindex="-1" aria-labelledby="addGradeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addGradeLabel">
                            <i class="fa-solid fa-ranking-star me-2 text-primary"></i>
                            Nouveau grade
                        </h5>
                        <small class="text-muted">Définissez le libellé du nouveau grade.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('grades.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                            <input type="text" name="libelle"
                                class="form-control @error('libelle') is-invalid @enderror" value="{{ old('libelle') }}"
                                placeholder="Ex : Professeur" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-plus me-1"></i> Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modales Edition -->
    @foreach ($grades as $grade)
        <div class="modal fade" id="editGrade{{ $grade->id }}" tabindex="-1"
            aria-labelledby="editGradeLabel{{ $grade->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editGradeLabel{{ $grade->id }}">
                                <i class="fa-solid fa-pen me-2 text-primary"></i>
                                Modifier le grade
                            </h5>
                            <small class="text-muted">Modifiez le libellé du grade.</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <form action="{{ route('grades.update', $grade->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                                <input type="text" name="libelle"
                                    class="form-control @error('libelle') is-invalid @enderror"
                                    value="{{ old('libelle', $grade->libelle) }}" placeholder="Ex : Professeur"
                                    required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-uvci">
                                <i class="fa-solid fa-check me-1"></i> Modifier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modales Suppression -->
        @if ($grade->enseignants_count == 0)
            <div class="modal fade" id="deleteGrade{{ $grade->id }}" tabindex="-1"
                aria-labelledby="deleteGradeLabel{{ $grade->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteGradeLabel{{ $grade->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">
                                Voulez-vous vraiment supprimer le grade suivant ?
                            </p>

                            <div class="alert alert-warning mb-0">
                                <strong>
                                    <i class="fa-solid fa-medal me-2"></i>
                                    {{ $grade->libelle }}
                                </strong>
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

                            <form action="{{ route('grades.destroy', $grade->id) }}" method="POST">
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
        @endif
    @endforeach

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addGrade = new bootstrap.Modal(document.getElementById('addGrade'));
                addGrade.show();
            });
        </script>
    @endif
</x-app-page>
