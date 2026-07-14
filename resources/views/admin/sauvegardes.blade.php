<x-app-page title="Sauvegardes" section="Administration" icon="fa-solid fa-database"
    subtitle="Sauvegarde et restauration des données du système.">
    <x-slot:actions>
        <form action="{{ route('sauvegardes.create') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-uvci"><i class="fa-solid fa-cloud-arrow-up me-1"></i> Lancer une
                sauvegarde</button>
        </form>
    </x-slot:actions>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon green"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <div>
                        <div class="stat-value" style="font-size:1.1rem">{{ $lastBackupDate ?? 'Aucune' }}</div>
                        <div class="stat-label">Dernière sauvegarde</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon purple"><i class="fa-solid fa-hard-drive"></i></div>
                    <div>
                        <div class="stat-value">{{ $totalSize ?? '0 o' }}</div>
                        <div class="stat-label">Taille des données</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon blue"><i class="fa-solid fa-shield-halved"></i></div>
                    <div>
                        <div class="stat-value">Manuelle</div>
                        <div class="stat-label">Fréquence</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-data-table search-placeholder="Rechercher une sauvegarde..." :count="count($backups)" :show-filters="false"
        export-title="Liste des sauvegardes">
        <x-slot:head>
            <th>Fichier</th>
            <th>Date</th>
            <th>Taille</th>
            <th>Type</th>
            <th class="text-end">Actions</th>
        </x-slot:head>
        @forelse ($backups as $backup)
            <tr>
                <td><i class="fa-solid fa-file-zipper text-uvci-purple me-2"></i>{{ $backup['filename'] }}</td>
                <td>{{ $backup['date'] }}</td>
                <td>{{ $backup['size'] }}</td>
                <td><span class="badge badge-soft-gray">Manuelle</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        <a href="{{ route('sauvegardes.download', $backup['filename']) }}" class="btn btn-light border"
                            title="Télécharger"><i class="fa-solid fa-download text-uvci-green"></i></a>
                        <button type="button" class="btn btn-light border" title="Restaurer" data-bs-toggle="modal"
                            data-bs-target="#restoreBackupModal{{ md5($backup['filename']) }}">
                            <i class="fa-solid fa-rotate-left text-uvci-purple"></i>
                        </button>
                        <button type="button" class="btn btn-light border" title="Supprimer" data-bs-toggle="modal"
                            data-bs-target="#deleteBackupModal{{ md5($backup['filename']) }}">
                            <i class="fa-solid fa-trash text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>

            {{-- Modale de restauration --}}
            <div class="modal fade" id="restoreBackupModal{{ md5($backup['filename']) }}" tabindex="-1"
                aria-labelledby="restoreBackupModalLabel{{ md5($backup['filename']) }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title fw-bold" id="restoreBackupModalLabel{{ md5($backup['filename']) }}">
                                <i class="fa-solid fa-rotate-left me-2"></i>
                                Confirmer la restauration
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">
                                Voulez-vous vraiment restaurer cette sauvegarde ?
                            </p>

                            <div class="alert alert-warning mb-0">
                                <strong>
                                    <i class="fa-solid fa-file-zipper me-2"></i>
                                    {{ $backup['filename'] }}
                                </strong><br>
                                <span class="small">Date : {{ $backup['date'] }} | Taille :
                                    {{ $backup['size'] }}</span>
                            </div>

                            <p class="text-danger small mt-3 mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Cette action remplacera toutes les données actuelles.
                            </p>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                Annuler
                            </button>

                            <form action="{{ route('sauvegardes.restore', $backup['filename']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa-solid fa-rotate-left me-1"></i>
                                    Oui, restaurer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modale de suppression --}}
            <div class="modal fade" id="deleteBackupModal{{ md5($backup['filename']) }}" tabindex="-1"
                aria-labelledby="deleteBackupModalLabel{{ md5($backup['filename']) }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteBackupModalLabel{{ md5($backup['filename']) }}">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Confirmer la suppression
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3">
                                Voulez-vous vraiment supprimer cette sauvegarde ?
                            </p>

                            <div class="alert alert-warning mb-0">
                                <strong>
                                    <i class="fa-solid fa-file-zipper me-2"></i>
                                    {{ $backup['filename'] }}
                                </strong><br>
                                <span class="small">Date : {{ $backup['date'] }} | Taille :
                                    {{ $backup['size'] }}</span>
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

                            <form action="{{ route('sauvegardes.destroy', $backup['filename']) }}" method="POST">
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
                        <i class="fa-solid fa-database fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune sauvegarde disponible.</p>
                        <small>Lancez une sauvegarde pour commencer.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>
</x-app-page>
