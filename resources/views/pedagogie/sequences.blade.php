<x-app-page title="Séquences pédagogiques" section="Gestion pédagogique" icon="fa-solid fa-layer-group"
    subtitle="Unités structurelles composant les cours.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addSequenceModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvelle séquence
        </button>
    </x-slot:actions>

    @forelse($sequences as $coursId => $sequencesList)
        <div class="card mb-4">
            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold">
                        <i class="fa-solid fa-book me-2 text-primary"></i>
                        {{ $cours[$coursId]->code_cours }} — {{ $cours[$coursId]->intitule }}
                    </h6>
                    <small class="text-muted">{{ $sequencesList->count() }} séquence(s)</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 sequences-table" data-cours-id="{{ $coursId }}">
                        <thead>
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>N°</th>
                                <th>Titre</th>
                                <th>Ressources</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="sortable-list">
                            @foreach ($sequencesList as $sequence)
                                <tr class="sortable-item" data-sequence-id="{{ $sequence->id }}">
                                    <td class="text-center">
                                        <i class="fa-solid fa-grip-vertical text-muted cursor-move"></i>
                                    </td>
                                    <td><span class="badge badge-soft-purple">{{ $sequence->numero_ordre }}</span></td>
                                    <td class="fw-semibold">{{ $sequence->titre }}</td>
                                    <td>{{ $sequence->ressources_pedagogiques_count }}</td>
                                    <td>
                                        <div class="action-btns justify-content-end">
                                            <button type="button" class="btn btn-light border" title="Modifier"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSequenceModal{{ $sequence->id }}">
                                                <i class="fa-solid fa-pen text-uvci-green"></i>
                                            </button>

                                            @if ($sequence->ressources_pedagogiques_count == 0)
                                                <button type="button" class="btn btn-light border" title="Supprimer"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteSequenceModal{{ $sequence->id }}">
                                                    <i class="fa-solid fa-trash text-danger"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-light border" disabled
                                                    title="Impossible de supprimer (ressources associées)">
                                                    <i class="fa-solid fa-trash text-muted"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <div class="text-muted">
                <i class="fa-solid fa-layer-group fa-3x mb-3 text-muted"></i>
                <p class="mb-0">Aucune séquence trouvée.</p>
                <small>Commencez par créer une nouvelle séquence.</small>
            </div>
        </div>
    @endforelse

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addSequenceModal" tabindex="-1" aria-labelledby="addSequenceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addSequenceModalLabel">
                            <i class="fa-solid fa-layer-group me-2 text-primary"></i>
                            Nouvelle séquence
                        </h5>
                        <small class="text-muted">Créer une nouvelle séquence pédagogique.</small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('sequences.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cours <span class="text-danger">*</span></label>
                                <select name="id_cours" class="form-select @error('id_cours') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un cours --</option>
                                    @foreach ($cours as $c)
                                        <option value="{{ $c->id }}">{{ $c->code_cours }} -
                                            {{ $c->intitule }}</option>
                                    @endforeach
                                </select>
                                @error('id_cours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-semibold">Titre <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="titre"
                                    class="form-control @error('titre') is-invalid @enderror"
                                    value="{{ old('titre') }}" maxlength="255" required>
                                @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Le numéro d'ordre sera calculé automatiquement.
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer la séquence
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque séquence --}}
    @foreach ($sequences as $sequencesList)
        @foreach ($sequencesList as $sequence)
            {{-- Modale : modification --}}
            <div class="modal fade" id="editSequenceModal{{ $sequence->id }}" tabindex="-1"
                aria-labelledby="editSequenceModalLabel{{ $sequence->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg mt-4">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <div>
                                <h5 class="modal-title fw-bold" id="editSequenceModalLabel{{ $sequence->id }}">
                                    <i class="fa-solid fa-layer-group me-2 text-primary"></i>
                                    Modifier la séquence
                                </h5>
                                <small class="text-muted">
                                    Séquence n°{{ $sequence->numero_ordre }} — {{ $sequence->titre }}
                                </small>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <form action="{{ route('sequences.update', $sequence->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Cours <span
                                                class="text-danger">*</span></label>
                                        <select name="id_cours"
                                            class="form-select @error('id_cours') is-invalid @enderror" required>
                                            <option value="">-- Sélectionner un cours --</option>
                                            @foreach ($cours as $c)
                                                <option value="{{ $c->id }}"
                                                    {{ $sequence->id_cours == $c->id ? 'selected' : '' }}>
                                                    {{ $c->code_cours }} — {{ $c->intitule }}</option>
                                            @endforeach
                                        </select>
                                        @error('id_cours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Titre <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="titre"
                                            class="form-control @error('titre') is-invalid @enderror"
                                            value="{{ old('titre', $sequence->titre) }}" maxlength="255" required>
                                        @error('titre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border"
                                    data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-uvci">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modale : suppression --}}
            @if ($sequence->ressources_pedagogiques_count == 0)
                <div class="modal fade" id="deleteSequenceModal{{ $sequence->id }}" tabindex="-1"
                    aria-labelledby="deleteSequenceModalLabel{{ $sequence->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title fw-bold" id="deleteSequenceModalLabel{{ $sequence->id }}">
                                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                    Confirmer la suppression
                                </h5>

                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Fermer"></button>
                            </div>

                            <div class="modal-body p-4">
                                <p class="mb-3">Voulez-vous vraiment supprimer cette séquence ?</p>

                                <div class="alert alert-warning mb-0">
                                    <strong>Séquence n°{{ $sequence->numero_ordre }}</strong><br>
                                    <span class="small">{{ $sequence->titre }}</span><br>
                                    <span class="small">{{ $sequence->cours->code_cours }} — {{ $sequence->cours->intitule }}</span>
                                </div>

                                <p class="text-danger small mt-3 mb-0">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    Cette action est irréversible.
                                </p>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border"
                                    data-bs-dismiss="modal">Annuler</button>

                                <form action="{{ route('sequences.destroy', $sequence->id) }}" method="POST">
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
    @endforeach

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addSequence = new bootstrap.Modal(document.getElementById('addSequenceModal'));
                addSequence.show();
            });
        </script>
    @endif

    {{-- SortableJS pour glisser-déposer --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser SortableJS pour chaque tableau de séquences
            document.querySelectorAll('.sortable-list').forEach(function(list) {
                var table = list.closest('.sequences-table');
                var coursId = table.getAttribute('data-cours-id');

                new Sortable(list, {
                    animation: 150,
                    handle: '.cursor-move',
                    ghostClass: 'sortable-ghost',
                    group: 'course-' + coursId, // Groupe unique par cours
                    onEnd: function(evt) {
                        var sequenceIds = [];
                        list.querySelectorAll('.sortable-item').forEach(function(item) {
                            sequenceIds.push(item.getAttribute('data-sequence-id'));
                        });

                        // Envoyer l'ordre au serveur
                        fetch('{{ route("sequences.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                sequence_ids: sequenceIds
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mettre à jour les numéros d'ordre affichés
                                list.querySelectorAll('.sortable-item').forEach(function(item, index) {
                                    item.querySelector('.badge').textContent = index + 1;
                                });
                            } else {
                                alert('Erreur lors du réordonnancement: ' + data.message);
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            location.reload();
                        });
                    }
                });
            });
        });
    </script>

    <style>
        .cursor-move {
            cursor: move;
        }

        .sortable-ghost {
            opacity: 0.4;
            background-color: #f0f0f0;
        }

        .sortable-item {
            background-color: white;
        }
    </style>
</x-app-page>
