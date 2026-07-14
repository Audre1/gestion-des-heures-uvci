<x-app-page title="États de paiement" section="Volumes & Paiements" icon="fa-solid fa-file-invoice-dollar"
    subtitle="Génération et suivi des états de paiement des enseignants.">

    <x-slot:actions>
        <form action="{{ route('paiements.index') }}" method="GET" class="d-flex gap-2">
            <select name="annee_id" class="form-select form-select-sm" style="width: 200px;">
                <option value="">Toutes les années</option>
                @foreach ($annees as $annee)
                    <option value="{{ $annee->id }}" {{ $anneeId == $annee->id ? 'selected' : '' }}>
                        {{ $annee->libelle }}
                        @if ($anneeActive && $annee->id == $anneeActive->id)
                            (Active)
                        @endif
                    </option>
                @endforeach
            </select>
            <select name="statut" class="form-select form-select-sm" style="width: 150px;">
                <option value="">Tous statuts</option>
                <option value="en_attente" {{ $statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="valide" {{ $statut === 'valide' ? 'selected' : '' }}>Validé</option>
                <option value="paye" {{ $statut === 'paye' ? 'selected' : '' }}>Payé</option>
                <option value="rejete" {{ $statut === 'rejete' ? 'selected' : '' }}>Rejeté</option>
            </select>
            <button type="submit" class="btn btn-sm btn-uvci">
                <i class="fa-solid fa-filter me-1"></i> Filtrer
            </button>
        </form>
        <button type="button" class="btn btn-sm btn-uvci" data-bs-toggle="modal" data-bs-target="#generateModal">
            <i class="fa-solid fa-file-circle-plus me-1"></i> Générer un état
        </button>
    </x-slot:actions>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon amber"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <div class="stat-value">{{ $statsParStatut['en_attente'] }}</div>
                        <div class="stat-label">En attente</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $statsParStatut['valide'] }}</div>
                        <div class="stat-label">Validés</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon purple"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div>
                        <div class="stat-value">{{ $statsParStatut['paye'] }}</div>
                        <div class="stat-label">Payés</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon red"><i class="fa-solid fa-times-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $statsParStatut['rejete'] }}</div>
                        <div class="stat-label">Rejetés</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-data-table search-placeholder="Rechercher un état..." :count="$etatsPaiement->count()">
        <x-slot:filters>
            <label class="dt-filter-label">Grade</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(2)">
                <option value="">Tous</option>
                @foreach (collect($etatsPaiement)->pluck('enseignant.grade.libelle')->unique()->filter()->sort()->values() as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
            <label class="dt-filter-label">Année</label>
            <select class="form-select form-select-sm dt-filter-select mb-3" onchange="filtrerDataTable(4)">
                <option value="">Toutes</option>
                @foreach (collect($etatsPaiement)->pluck('anneeAcademique.libelle')->unique()->filter()->sort()->values() as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
            <label class="dt-filter-label">Statut</label>
            <select class="form-select form-select-sm dt-filter-select" onchange="filtrerDataTable(6)">
                <option value="">Tous</option>
                <option value="en_attente">En attente</option>
                <option value="valide">Validé</option>
                <option value="paye">Payé</option>
                <option value="rejete">Rejeté</option>
            </select>
        </x-slot:filters>
        <x-slot:head>
            <th>Numéro</th>
            <th>Enseignant</th>
            <th>Grade</th>
            <th>Période</th>
            <th>Année</th>
            <th>Montant total</th>
            <th>Statut</th>
            <th>Date génération</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @forelse($etatsPaiement as $etat)
            <tr>
                <td class="font-monospace fw-semibold text-uvci-purple">{{ $etat->numero_paiement }}</td>
                <td class="fw-semibold">
                    {{ $etat->enseignant->utilisateur->nom }}
                    {{ $etat->enseignant->utilisateur->prenom }}
                </td>
                <td>{{ $etat->enseignant->grade->libelle ?? 'N/A' }}</td>
                <td>{{ $etat->periode }}</td>
                <td>{{ $etat->anneeAcademique->libelle ?? 'N/A' }}</td>
                <td class="fw-semibold text-uvci-green">{{ number_format($etat->montant_total, 0, ',', ' ') }} FCFA
                </td>
                <td>
                    @if ($etat->statut === 'en_attente')
                        <span class="badge bg-warning text-white">En attente</span>
                    @elseif($etat->statut === 'valide')
                        <span class="badge bg-success text-white">Validé</span>
                    @elseif($etat->statut === 'paye')
                        <span class="badge bg-info text-white">Payé</span>
                    @elseif($etat->statut === 'rejete')
                        <span class="badge bg-danger text-white">Rejeté</span>
                    @endif
                </td>
                <td>{{ $etat->date_generation ? $etat->date_generation->format('d/m/Y H:i') : 'N/A' }}</td>
                <td class="text-end">
                    <div class="action-btns justify-content-end">
                        @if ($etat->statut === 'en_attente')
                            <button type="button" class="btn btn-sm btn-light border" title="Valider"
                                data-bs-toggle="modal" data-bs-target="#validerModal{{ $etat->id }}">
                                <i class="fa-solid fa-check text-success"></i>
                            </button>
                        @endif
                        @if ($etat->statut === 'valide')
                            <button type="button" class="btn btn-sm btn-light border" title="Marquer payé"
                                data-bs-toggle="modal" data-bs-target="#marquerPayeModal{{ $etat->id }}">
                                <i class="fa-solid fa-money-bill-wave text-info"></i>
                            </button>
                        @endif
                        @if (in_array($etat->statut, ['en_attente', 'valide']))
                            <button type="button" class="btn btn-sm btn-light border" title="Rejeter"
                                data-bs-toggle="modal" data-bs-target="#rejeterModal{{ $etat->id }}">
                                <i class="fa-solid fa-times text-danger"></i>
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-light border" title="Supprimer"
                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $etat->id }}">
                            <i class="fa-solid fa-trash text-muted"></i>
                        </button>
                    </div>
                </td>
            </tr>

            {{-- Modale de validation --}}
            @if ($etat->statut === 'en_attente')
                <div class="modal fade" id="validerModal{{ $etat->id }}" tabindex="-1"
                    aria-labelledby="validerModalLabel{{ $etat->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title fw-bold" id="validerModalLabel{{ $etat->id }}">
                                    <i class="fa-solid fa-check me-2"></i>
                                    Valider l'état de paiement
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Fermer"></button>
                            </div>

                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Voulez-vous valider l'état de paiement suivant ?
                                </p>

                                <div class="alert alert-info mb-0">
                                    <strong>
                                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                                        {{ $etat->numero_paiement }}
                                    </strong><br>
                                    <span class="small">Enseignant: {{ $etat->enseignant->utilisateur->nom }}
                                        {{ $etat->enseignant->utilisateur->prenom }}</span><br>
                                    <span class="small">Montant:
                                        {{ number_format($etat->montant_total, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                    Annuler
                                </button>

                                <form action="{{ route('paiements.valider', $etat->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-check me-1"></i>
                                        Oui, valider
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modale de marquer payé --}}
            @if ($etat->statut === 'valide')
                <div class="modal fade" id="marquerPayeModal{{ $etat->id }}" tabindex="-1"
                    aria-labelledby="marquerPayeModalLabel{{ $etat->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title fw-bold" id="marquerPayeModalLabel{{ $etat->id }}">
                                    <i class="fa-solid fa-money-bill-wave me-2"></i>
                                    Marquer comme payé
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Fermer"></button>
                            </div>

                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Voulez-vous marquer cet état de paiement comme payé ?
                                </p>

                                <div class="alert alert-info mb-0">
                                    <strong>
                                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                                        {{ $etat->numero_paiement }}
                                    </strong><br>
                                    <span class="small">Montant:
                                        {{ number_format($etat->montant_total, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                    Annuler
                                </button>

                                <form action="{{ route('paiements.marquerPaye', $etat->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa-solid fa-money-bill-wave me-1"></i>
                                        Oui, marquer payé
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modale de rejet --}}
            @if (in_array($etat->statut, ['en_attente', 'valide']))
                <div class="modal fade" id="rejeterModal{{ $etat->id }}" tabindex="-1"
                    aria-labelledby="rejeterModalLabel{{ $etat->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title fw-bold" id="rejeterModalLabel{{ $etat->id }}">
                                    <i class="fa-solid fa-times me-2"></i>
                                    Rejeter l'état de paiement
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Fermer"></button>
                            </div>

                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Voulez-vous rejeter l'état de paiement suivant ?
                                </p>

                                <div class="alert alert-warning mb-0">
                                    <strong>
                                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                                        {{ $etat->numero_paiement }}
                                    </strong><br>
                                    <span class="small">Enseignant: {{ $etat->enseignant->utilisateur->nom }}
                                        {{ $etat->enseignant->utilisateur->prenom }}</span><br>
                                    <span class="small">Montant:
                                        {{ number_format($etat->montant_total, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                    Annuler
                                </button>

                                <form action="{{ route('paiements.rejeter', $etat->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa-solid fa-times me-1"></i>
                                        Oui, rejeter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modale de suppression --}}
            <div class="modal fade" id="deleteModal{{ $etat->id }}" tabindex="-1"
                aria-labelledby="deleteModalLabel{{ $etat->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $etat->id }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">
                                Voulez-vous vraiment supprimer l'état de paiement suivant ?
                            </p>

                            <div class="alert alert-warning mb-0">
                                <strong>
                                    <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                                    {{ $etat->numero_paiement }}
                                </strong><br>
                                <span class="small">Enseignant: {{ $etat->enseignant->utilisateur->nom }}
                                    {{ $etat->enseignant->utilisateur->prenom }}</span><br>
                                <span class="small">Montant: {{ number_format($etat->montant_total, 0, ',', ' ') }}
                                    FCFA</span><br>
                                <span class="small">Statut: {{ $etat->statut }}</span>
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

                            <form action="{{ route('paiements.destroy', $etat->id) }}" method="POST"
                                class="d-inline">
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
                <td colspan="9" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-file-invoice-dollar fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucun état de paiement trouvé.</p>
                        <small>Générez un nouvel état de paiement pour commencer.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- Modal de génération d'état de paiement --}}
    <div class="modal fade" id="generateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-file-circle-plus me-2 text-primary"></i>
                        Générer un état de paiement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('paiements.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Enseignant <span class="text-danger">*</span></label>
                            <select name="id_enseignant" class="form-select" required>
                                <option value="">Sélectionner un enseignant</option>
                                @php
                                    $enseignants = \App\Models\Enseignant::with('utilisateur')->get();
                                @endphp
                                @foreach ($enseignants as $ens)
                                    <option value="{{ $ens->id }}">{{ $ens->utilisateur->nom }}
                                        {{ $ens->utilisateur->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Année académique <span class="text-danger">*</span></label>
                            <select name="id_annee" class="form-select" required>
                                <option value="">Sélectionner une année</option>
                                @foreach ($annees as $annee)
                                    <option value="{{ $annee->id }}"
                                        {{ $anneeActive && $annee->id == $anneeActive->id ? 'selected' : '' }}>
                                        {{ $annee->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Période <span class="text-danger">*</span></label>
                            <input type="text" name="periode" class="form-control" placeholder="Ex: Octobre 2026"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-uvci">
                            <i class="fa-solid fa-file-circle-plus me-1"></i> Générer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-page>
