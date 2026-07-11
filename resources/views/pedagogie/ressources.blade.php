<x-app-page title="Ressources pédagogiques" section="Gestion pédagogique" icon="fa-solid fa-photo-film"
    subtitle="Contenus numériques associés aux séquences.">
    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addRessourceModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvelle ressource
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une ressource..." :count="$ressources->count()">
        <x-slot:head>
            <th>Titre</th>
            <th>Type</th>
            <th>Séquence</th>
            <th>Créée le</th>
            <th>Modifiée le</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @forelse($ressources as $ressource)
            <tr>
                <td class="fw-semibold">{{ $ressource->titre }}</td>
                <td><span class="badge badge-soft-purple">{{ $ressource->typeRessource->libelle }}</span></td>
                <td>
                    <span class="badge badge-soft-blue">Séq. {{ $ressource->sequence->numero_ordre }}</span>
                    <div class="text-muted small">{{ $ressource->sequence->cours->code_cours }} — {{ $ressource->sequence->titre }}</div>
                </td>
                <td class="text-muted">{{ $ressource->date_creation ? $ressource->date_creation->format('d/m/Y') : '-' }}</td>
                <td class="text-muted">{{ $ressource->date_modification ? $ressource->date_modification->format('d/m/Y') : '-' }}</td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button type="button" class="btn btn-light border" title="Modifier"
                            data-bs-toggle="modal"
                            data-bs-target="#editRessourceModal{{ $ressource->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        @if ($ressource->activites_pedagogiques_count == 0)
                            <button type="button" class="btn btn-light border" title="Supprimer"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteRessourceModal{{ $ressource->id }}">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-light border" disabled
                                title="Impossible de supprimer (activités associées)">
                                <i class="fa-solid fa-trash text-muted"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-photo-film fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune ressource trouvée.</p>
                        <small>Commencez par créer une nouvelle ressource.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addRessourceModal" tabindex="-1" aria-labelledby="addRessourceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addRessourceModalLabel">
                            <i class="fa-solid fa-photo-film me-2 text-primary"></i>
                            Nouvelle ressource
                        </h5>
                        <small class="text-muted">Créer une nouvelle ressource pédagogique.</small>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('ressources.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Séquence <span
                                        class="text-danger">*</span></label>
                                <select name="id_sequence"
                                    class="form-select @error('id_sequence') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner une séquence --</option>
                                    @foreach (\App\Models\SequencePedagogique::with('cours')->get() as $sequence)
                                        <option value="{{ $sequence->id }}">
                                            Séq. {{ $sequence->numero_ordre }} — {{ $sequence->titre }}
                                            ({{ $sequence->cours->code_cours }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_sequence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Type de ressource <span
                                        class="text-danger">*</span></label>
                                <select name="id_type"
                                    class="form-select @error('id_type') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    @foreach (\App\Models\TypeRessource::all() as $type)
                                        <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                                    @endforeach
                                </select>
                                @error('id_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
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
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Enregistrer la ressource
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque ressource --}}
    @foreach ($ressources as $ressource)
        {{-- Modale : modification --}}
        <div class="modal fade" id="editRessourceModal{{ $ressource->id }}" tabindex="-1"
            aria-labelledby="editRessourceModalLabel{{ $ressource->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editRessourceModalLabel{{ $ressource->id }}">
                                <i class="fa-solid fa-photo-film me-2 text-primary"></i>
                                Modifier la ressource
                            </h5>
                            <small class="text-muted">
                                {{ $ressource->titre }}
                            </small>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('ressources.update', $ressource->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Séquence <span
                                            class="text-danger">*</span></label>
                                    <select name="id_sequence"
                                        class="form-select @error('id_sequence') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner une séquence --</option>
                                        @foreach (\App\Models\SequencePedagogique::with('cours')->get() as $sequence)
                                            <option value="{{ $sequence->id }}"
                                                {{ $ressource->id_sequence == $sequence->id ? 'selected' : '' }}>
                                                Séq. {{ $sequence->numero_ordre }} — {{ $sequence->titre }}
                                                ({{ $sequence->cours->code_cours }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_sequence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Type de ressource <span
                                            class="text-danger">*</span></label>
                                    <select name="id_type"
                                        class="form-select @error('id_type') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner un type --</option>
                                        @foreach (\App\Models\TypeRessource::all() as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $ressource->id_type == $type->id ? 'selected' : '' }}>
                                                {{ $type->libelle }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Titre <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="titre"
                                        class="form-control @error('titre') is-invalid @enderror"
                                        value="{{ old('titre', $ressource->titre) }}" maxlength="255" required>
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
        @if ($ressource->activites_pedagogiques_count == 0)
            <div class="modal fade" id="deleteRessourceModal{{ $ressource->id }}" tabindex="-1"
                aria-labelledby="deleteRessourceModalLabel{{ $ressource->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteRessourceModalLabel{{ $ressource->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment supprimer cette ressource ?</p>

                            <div class="alert alert-warning mb-0">
                                <strong>{{ $ressource->titre }}</strong><br>
                                <span class="small">{{ $ressource->typeRessource->libelle }}</span><br>
                                <span class="small">Séq. {{ $ressource->sequence->numero_ordre }} — {{ $ressource->sequence->titre }}
                                    ({{ $ressource->sequence->cours->code_cours }})</span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action est irréversible.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>

                            <form action="{{ route('ressources.destroy', $ressource->id) }}" method="POST">
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
                var addRessource = new bootstrap.Modal(document.getElementById('addRessourceModal'));
                addRessource.show();
            });
        </script>
    @endif
</x-app-page>
