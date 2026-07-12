<x-app-page title="Activités pédagogiques" section="Gestion pédagogique" icon="fa-solid fa-list-check"
    subtitle="Enregistrement et validation des activités des enseignants.">

    <x-slot:actions>
        <button type="button" class="btn btn-uvci" data-bs-toggle="modal" data-bs-target="#addActiviteModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvelle activité
        </button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher une activité..." :count="$activites->count()">
        <x-slot:head>
            <th>Enseignant</th>
            <th>Cours</th>
            <th>Type</th>
            <th>Niveau</th>
            <th>Séq.</th>
            <th>Coeff.</th>
            <th>VHT</th>
            <th>Date</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot:head>

        @forelse($activites as $activite)
            <tr>
                <td class="fw-semibold">
                    {{ $activite->affectationCours->enseignant->utilisateur->nom }}
                    {{ $activite->affectationCours->enseignant->utilisateur->prenom }}
                </td>
                <td>{{ $activite->affectationCours->cours->code_cours }}</td>
                <td>
                    <span class="badge badge-soft-{{ $activite->type_activite === 'creation' ? 'green' : 'purple' }}">
                        {{ $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour' }}
                    </span>
                </td>
                <td>{{ $activite->niveauComplexite->libelle }}</td>
                <td>{{ $activite->nb_sequences }}</td>
                <td>{{ number_format($activite->coefficient, 2) }}</td>
                <td class="fw-semibold text-uvci-green">{{ number_format($activite->volume_horaire, 0) }}H</td>
                <td class="text-muted">
                    {{ $activite->date_activite ? $activite->date_activite->format('d/m/Y') : 'N/A' }}</td>
                <td>
                    <span
                        class="badge badge-soft-{{ $activite->statut === 'validee' ? 'green' : ($activite->statut === 'rejetee' ? 'red' : 'amber') }}">
                        {{ $activite->statut === 'validee' ? 'Validée' : ($activite->statut === 'rejetee' ? 'Rejetée' : 'En cours') }}
                    </span>
                </td>
                <td>
                    <div class="action-btns justify-content-end">
                        @if ($activite->statut === 'en_cours')
                            <button type="button" class="btn btn-light border" title="Valider" data-bs-toggle="modal"
                                data-bs-target="#validerActiviteModal{{ $activite->id }}">
                                <i class="fa-solid fa-check text-uvci-green"></i>
                            </button>
                        @endif

                        <button type="button" class="btn btn-light border" title="Modifier" data-bs-toggle="modal"
                            data-bs-target="#editActiviteModal{{ $activite->id }}">
                            <i class="fa-solid fa-pen text-uvci-green"></i>
                        </button>

                        <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                            data-bs-target="#deleteActiviteModal{{ $activite->id }}">
                            <i class="fa-solid fa-trash text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-list-check fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune activité pédagogique trouvée.</p>
                        <small>Commencez par ajouter une nouvelle activité.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modale : ajout --}}
    <div class="modal fade" id="addActiviteModal" tabindex="-1" aria-labelledby="addActiviteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg mt-4">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="addActiviteModalLabel">
                            <i class="fa-solid fa-list-check me-2 text-primary"></i>
                            Nouvelle activité pédagogique
                        </h5>
                        <small class="text-muted">Enregistrez une activité d'enseignement.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('activites.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Affectation <span
                                        class="text-danger">*</span></label>
                                <select name="id_affectation" id="id_affectation"
                                    class="form-select @error('id_affectation') is-invalid @enderror" required>
                                    <option value="">Sélectionner une affectation</option>
                                    @foreach ($affectations as $affectation)
                                        <option value="{{ $affectation->id }}"
                                            data-heures="{{ $affectation->cours->nombre_heures }}"
                                            {{ old('id_affectation') == $affectation->id ? 'selected' : '' }}>
                                            {{ $affectation->enseignant->utilisateur->nom }}
                                            {{ $affectation->enseignant->utilisateur->prenom }} —
                                            {{ $affectation->cours->code_cours }}
                                            ({{ $affectation->anneeAcademique->libelle }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_affectation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Type d'activité <span
                                        class="text-danger">*</span></label>
                                <select name="type_activite" id="type_activite"
                                    class="form-select @error('type_activite') is-invalid @enderror" required>
                                    <option value="">Sélectionner le type</option>
                                    <option value="creation"
                                        {{ old('type_activite') === 'creation' ? 'selected' : '' }}>Création</option>
                                    <option value="maj"
                                        {{ old('type_activite') === 'maj' ? 'selected' : '' }}>Mise à jour
                                    </option>
                                </select>
                                @error('type_activite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Niveau de complexité <span
                                        class="text-danger">*</span></label>
                                <select name="id_niveau" id="id_niveau"
                                    class="form-select @error('id_niveau') is-invalid @enderror" required>
                                    <option value="">Sélectionner le niveau</option>
                                    @if ($parametres)
                                        @foreach ($niveaux as $index => $niveau)
                                            @php
                                                $niveauNum = $index + 1;
                                                $coeffCreation = (float) $parametres->getCoefficient('creation', $niveauNum);
                                                $coeffMaj = (float) $parametres->getCoefficient('maj', $niveauNum);
                                            @endphp
                                            <option value="{{ $niveau->id }}" 
                                                data-coeff-creation="{{ $coeffCreation }}"
                                                data-coeff-maj="{{ $coeffMaj }}"
                                                {{ old('id_niveau') == $niveau->id ? 'selected' : '' }}>
                                                {{ $niveau->libelle }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Aucun paramètre actif configuré</option>
                                    @endif
                                </select>
                                @error('id_niveau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ressource pédagogique</label>
                                <select name="id_ressource"
                                    class="form-select @error('id_ressource') is-invalid @enderror">
                                    <option value="">Aucune ressource</option>
                                    @foreach ($ressources as $ressource)
                                        <option value="{{ $ressource->id }}"
                                            {{ old('id_ressource') == $ressource->id ? 'selected' : '' }}>
                                            {{ $ressource->titre }}
                                            ({{ $ressource->typeRessource->libelle ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_ressource')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date de l'activité <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="date_activite"
                                    class="form-control @error('date_activite') is-invalid @enderror"
                                    value="{{ old('date_activite') }}" required>
                                @error('date_activite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Statut</label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                                    <option value="">Par défaut : En cours</option>
                                    <option value="en_cours" {{ old('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="validee" {{ old('statut') === 'validee' ? 'selected' : '' }}>Validée</option>
                                    <option value="rejetee" {{ old('statut') === 'rejetee' ? 'selected' : '' }}>Rejetée</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Aperçu calculé automatiquement --}}
                            <div class="col-12">
                                <div class="alert alert-info mb-0" id="apercu-calcul" style="display:none">
                                    <i class="fa-solid fa-calculator me-2"></i>
                                    <strong>Aperçu du calcul :</strong>
                                    <span id="apercu-sequences"></span> séquences ×
                                    <span id="apercu-coeff"></span> =
                                    <strong><span id="apercu-vht"></span>h</strong>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer l'activité
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales individuelles pour chaque activité --}}
    @foreach ($activites as $activite)
        {{-- Modale : modification --}}
        <div class="modal fade" id="editActiviteModal{{ $activite->id }}" tabindex="-1"
            aria-labelledby="editActiviteModalLabel{{ $activite->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-4">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="editActiviteModalLabel{{ $activite->id }}">
                                <i class="fa-solid fa-pen me-2 text-primary"></i> Modifier l'activité
                            </h5>
                            <small class="text-muted">
                                {{ $activite->affectationCours->enseignant->utilisateur->nom }}
                                {{ $activite->affectationCours->enseignant->utilisateur->prenom }} -
                                {{ $activite->affectationCours->cours->code_cours }}
                            </small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <form action="{{ route('activites.update', $activite->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Affectation <span
                                            class="text-danger">*</span></label>
                                    <select name="id_affectation"
                                        class="form-select @error('id_affectation') is-invalid @enderror" required>
                                        <option value="">Sélectionner une affectation</option>
                                        @foreach ($affectations as $affectation)
                                            <option value="{{ $affectation->id }}"
                                                {{ old('id_affectation', $activite->id_affectation) == $affectation->id ? 'selected' : '' }}>
                                                {{ $affectation->enseignant->utilisateur->nom }}
                                                {{ $affectation->enseignant->utilisateur->prenom }} -
                                                {{ $affectation->cours->code_cours }}
                                                ({{ $affectation->anneeAcademique->libelle }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_affectation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Type d'activité <span
                                            class="text-danger">*</span></label>
                                    <select name="type_activite"
                                        class="form-select @error('type_activite') is-invalid @enderror" required>
                                        <option value="creation"
                                            {{ old('type_activite', $activite->type_activite) === 'creation' ? 'selected' : '' }}>
                                            Création</option>
                                        <option value="maj"
                                            {{ old('type_activite', $activite->type_activite) === 'maj' ? 'selected' : '' }}>
                                            Mise à jour</option>
                                    </select>
                                    @error('type_activite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Niveau de complexité <span
                                            class="text-danger">*</span></label>
                                    <select name="id_niveau"
                                        class="form-select @error('id_niveau') is-invalid @enderror" required>
                                        @foreach ($niveaux as $niveau)
                                            <option value="{{ $niveau->id }}"
                                                {{ old('id_niveau', $activite->id_niveau) == $niveau->id ? 'selected' : '' }}>
                                                {{ $niveau->libelle }} (coeff:
                                                {{ number_format($niveau->coefficient, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_niveau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ressource pédagogique</label>
                                    <select name="id_ressource"
                                        class="form-select @error('id_ressource') is-invalid @enderror">
                                        <option value="">Aucune ressource</option>
                                        @foreach ($ressources as $ressource)
                                            <option value="{{ $ressource->id }}"
                                                {{ old('id_ressource', $activite->id_ressource) == $ressource->id ? 'selected' : '' }}>
                                                {{ $ressource->titre }}
                                                ({{ $ressource->typeRessource->libelle ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_ressource')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Date de l'activité <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date_activite"
                                        class="form-control @error('date_activite') is-invalid @enderror"
                                        value="{{ old('date_activite', $activite->date_activite ? $activite->date_activite->format('Y-m-d') : '') }}"
                                        required>
                                    @error('date_activite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Statut <span
                                            class="text-danger">*</span></label>
                                    <select name="statut" class="form-select @error('statut') is-invalid @enderror"
                                        required>
                                        <option value="en_cours"
                                            {{ old('statut', $activite->statut) === 'en_cours' ? 'selected' : '' }}>En
                                            cours</option>
                                        <option value="validee"
                                            {{ old('statut', $activite->statut) === 'validee' ? 'selected' : '' }}>
                                            Validée</option>
                                        <option value="rejetee"
                                            {{ old('statut', $activite->statut) === 'rejetee' ? 'selected' : '' }}>
                                            Rejetée</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nombre de séquences <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="nb_sequences"
                                        class="form-control @error('nb_sequences') is-invalid @enderror"
                                        value="{{ old('nb_sequences', $activite->nb_sequences) }}" min="1"
                                        step="1" readonly>
                                    @error('nb_sequences')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Coefficient <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="coefficient"
                                        class="form-control @error('coefficient') is-invalid @enderror"
                                        value="{{ old('coefficient', $activite->coefficient) }}" min="0"
                                        step="0.01" readonly>
                                    @error('coefficient')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Volume horaire <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="volume_horaire"
                                        class="form-control @error('volume_horaire') is-invalid @enderror"
                                        value="{{ old('volume_horaire', $activite->volume_horaire) }}" min="0"
                                        step="1" readonly>
                                    @error('volume_horaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-uvci">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modale : validation --}}
        @if ($activite->statut === 'en_cours')
            <div class="modal fade" id="validerActiviteModal{{ $activite->id }}" tabindex="-1"
                aria-labelledby="validerActiviteModalLabel{{ $activite->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title fw-bold" id="validerActiviteModalLabel{{ $activite->id }}">
                                <i class="fa-solid fa-check-circle me-2"></i> Confirmer la validation
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">Voulez-vous vraiment valider cette activité pédagogique ?</p>

                            <div class="alert alert-info mb-0">
                                <strong>
                                    <i class="fa-solid fa-user me-2"></i>
                                    {{ $activite->affectationCours->enseignant->utilisateur->nom }}
                                    {{ $activite->affectationCours->enseignant->utilisateur->prenom }}
                                </strong><br>
                                <span class="small">
                                    {{ $activite->affectationCours->cours->code_cours }} —
                                    {{ $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour' }} —
                                    VHT: {{ number_format($activite->volume_horaire, 0) }}H
                                </span>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                            <form action="{{ route('activites.valider', $activite->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check me-1"></i> Oui, valider
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modale : suppression --}}
        <div class="modal fade" id="deleteActiviteModal{{ $activite->id }}" tabindex="-1"
            aria-labelledby="deleteActiviteModalLabel{{ $activite->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="deleteActiviteModalLabel{{ $activite->id }}">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body p-4">
                        <p class="mb-3">Voulez-vous vraiment supprimer cette activité pédagogique ?</p>

                        <div class="alert alert-warning mb-0">
                            <strong>
                                <i class="fa-solid fa-user me-2"></i>
                                {{ $activite->affectationCours->enseignant->utilisateur->nom }}
                                {{ $activite->affectationCours->enseignant->utilisateur->prenom }}
                            </strong><br>
                            <span class="small">
                                {{ $activite->affectationCours->cours->code_cours }} —
                                {{ $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour' }} —
                                VHT: {{ number_format($activite->volume_horaire, 0) }}H
                            </span>
                        </div>

                        <p class="text-danger small mt-3 mb-0">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Cette action est irréversible.
                        </p>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>

                        <form action="{{ route('activites.destroy', $activite->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-trash me-1"></i> Oui, supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if ($parametres)
        <script>
            const ratio = {{ $parametres->sequences_par_credit / $parametres->heures_par_credit }};

            function recalculer() {
                const affectationSelect = document.getElementById('id_affectation');
                const typeSelect = document.getElementById('type_activite');
                const niveauSelect = document.getElementById('id_niveau');
                const apercu = document.getElementById('apercu-calcul');

                const heures = parseFloat(affectationSelect.selectedOptions[0]?.dataset.heures) || 0;
                const type = typeSelect.value;
                const option = niveauSelect.selectedOptions[0];

                if (!heures || !type || !option?.value) {
                    apercu.style.display = 'none';
                    return;
                }

                const nbSequences = Math.round(heures * ratio);
                const coeff = type === 'creation' ?
                    parseFloat(option.dataset.coeffCreation) :
                    parseFloat(option.dataset.coeffMaj);
                const vht = (nbSequences * coeff).toFixed(2);

                document.getElementById('apercu-sequences').textContent = nbSequences;
                document.getElementById('apercu-coeff').textContent = coeff.toFixed(3);
                document.getElementById('apercu-vht').textContent = vht;
                apercu.style.display = 'block';
            }

            // Ajouter les écouteurs d'événements
            document.addEventListener('DOMContentLoaded', function() {
                const affectationSelect = document.getElementById('id_affectation');
                const typeSelect = document.getElementById('type_activite');
                const niveauSelect = document.getElementById('id_niveau');

                if (affectationSelect) {
                    affectationSelect.addEventListener('change', recalculer);
                }
                if (typeSelect) {
                    typeSelect.addEventListener('change', recalculer);
                }
                if (niveauSelect) {
                    niveauSelect.addEventListener('change', recalculer);
                }

                // Calculer au chargement si les champs sont déjà remplis
                recalculer();
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addActivite = new bootstrap.Modal(document.getElementById('addActiviteModal'));
                addActivite.show();
            });
        </script>
    @endif
</x-app-page>
