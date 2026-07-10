<x-app-page title="Journaux d'activités" section="Administration" icon="fa-solid fa-clipboard-list"
    subtitle="Traçabilité des actions réalisées dans le système.">

    <x-data-table search-placeholder="Rechercher dans les journaux..." :count="$journaux->count()">
        <x-slot:head>
            <th>Date / Heure</th>
            <th>Utilisateur</th>
            <th>Action</th>
            <th>Description</th>
            <th>Adresse IP</th>
        </x-slot:head>

        @forelse ($journaux as $journal)
            <tr>
                <td class="text-muted">{{ $journal->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if ($journal->utilisateur)
                        <span class="badge badge-soft-purple">{{ $journal->utilisateur->login }}</span>
                    @else
                        <span class="badge badge-soft-gray">Système</span>
                    @endif
                </td>
                <td>
                    <span
                        class="badge badge-soft-{{ $journal->action === 'création' ? 'green' : ($journal->action === 'modification' ? 'blue' : ($journal->action === 'suppression' ? 'red' : ($journal->action === 'connexion' ? 'green' : ($journal->action === 'déconnexion' ? 'gray' : 'gray')))) }}">
                        {{ ucfirst($journal->action) }}
                    </span>
                </td>
                <td>{{ $journal->description }}</td>
                <td class="text-muted font-monospace small">{{ $journal->ip_address ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fa-solid fa-clipboard-list fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Aucune activité enregistrée.</p>
                        <small>Les actions des utilisateurs apparaîtront ici.</small>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{ $journaux->links() }}
</x-app-page>
