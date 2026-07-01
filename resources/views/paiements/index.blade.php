<x-app-page title="États de paiement" section="Volumes & Paiements" icon="fa-solid fa-file-invoice-dollar"
    subtitle="Génération et suivi des états de paiement des enseignants.">
    <x-slot:actions>
        <button class="btn btn-uvci"><i class="fa-solid fa-file-circle-plus me-1"></i> Générer un état</button>
    </x-slot:actions>

    <x-data-table search-placeholder="Rechercher un état..." :count="6">
        <x-slot:head>
            <th>Réf.</th><th>Enseignant</th><th>Période</th><th>Année</th><th>Montant total</th><th>Statut</th><th class="text-end">Actions</th>
        </x-slot:head>
        @php
            $pay = [
                ['PAY-088', 'Konan Kouassi', 'Oct. 2024', '2024-2025', '900 000 FCFA', 'Généré', 'green'],
                ['PAY-089', 'Awa Traoré', 'Oct. 2024', '2024-2025', '540 000 FCFA', 'En attente', 'amber'],
                ['PAY-090', 'Moussa Diabaté', 'Oct. 2024', '2024-2025', '1 440 000 FCFA', 'Généré', 'green'],
                ['PAY-091', 'Sarah Koné', 'Oct. 2024', '2024-2025', '240 000 FCFA', 'Payé', 'purple'],
                ['PAY-092', 'Blaise Yao', 'Oct. 2024', '2024-2025', '0 FCFA', 'En attente', 'amber'],
                ['PAY-093', 'Fatou Ouattara', 'Oct. 2024', '2024-2025', '414 000 FCFA', 'Généré', 'green'],
            ];
        @endphp
        @foreach($pay as [$ref, $ens, $per, $an, $mnt, $st, $c])
            <tr>
                <td class="font-monospace fw-semibold text-uvci-purple">{{ $ref }}</td>
                <td class="fw-semibold">{{ $ens }}</td>
                <td>{{ $per }}</td><td>{{ $an }}</td>
                <td class="fw-semibold text-uvci-green">{{ $mnt }}</td>
                <td><span class="badge badge-soft-{{ $c }}">{{ $st }}</span></td>
                <td>
                    <div class="action-btns justify-content-end">
                        <button class="btn btn-light border" title="Aperçu"><i class="fa-solid fa-eye text-muted"></i></button>
                        <button class="btn btn-light border" title="Imprimer"><i class="fa-solid fa-print text-uvci-purple"></i></button>
                        <button class="btn btn-light border" title="PDF"><i class="fa-solid fa-file-pdf text-danger"></i></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
