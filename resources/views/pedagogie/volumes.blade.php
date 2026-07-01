<x-app-page title="Volumes horaires" section="Volumes & Paiements" icon="fa-solid fa-hourglass-half"
    subtitle="Consultation et contrôle des charges horaires par enseignant.">

    <x-data-table search-placeholder="Rechercher un enseignant..." :count="6">
        <x-slot:head>
            <th>Enseignant</th><th>Grade</th><th>Service statutaire</th><th>VHT réalisé</th><th>Heures compl.</th><th>Charge</th>
        </x-slot:head>
        @php
            $vol = [
                ['Konan Kouassi', 'Professeur', '192h', '228h', '36h', 118],
                ['Awa Traoré', 'Maître-Assistant', '192h', '180h', '0h', 94],
                ['Moussa Diabaté', 'Assistant', '—', '96h', '96h', 100],
                ['Sarah Koné', 'Assistant', '192h', '208h', '16h', 108],
                ['Blaise Yao', 'Professeur', '192h', '160h', '0h', 83],
                ['Fatou Ouattara', 'Maître-Assistant', '—', '120h', '120h', 100],
            ];
        @endphp
        @foreach($vol as [$ens, $grade, $serv, $vht, $hc, $pct])
            <tr>
                <td class="fw-semibold">{{ $ens }}</td>
                <td>{{ $grade }}</td><td>{{ $serv }}</td>
                <td class="fw-semibold text-uvci-green">{{ $vht }}</td>
                <td>
                    @if($hc !== '0h')<span class="badge badge-soft-amber">{{ $hc }}</span>@else <span class="text-muted">—</span>@endif
                </td>
                <td style="min-width:140px">
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-fill" style="height:7px">
                            <div class="progress-bar" style="width:{{ min($pct,100) }}%;background:{{ $pct > 100 ? 'var(--uvci-purple)' : 'var(--uvci-green)' }}"></div>
                        </div>
                        <small class="fw-semibold {{ $pct > 100 ? 'text-uvci-purple' : 'text-muted' }}">{{ $pct }}%</small>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-app-page>
