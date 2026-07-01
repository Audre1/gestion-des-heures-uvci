@props([
    'searchPlaceholder' => 'Rechercher...',
    'count' => null,
    'showFilters' => true,
])

<div class="card">
    <div class="card-body p-0">
        {{-- Barre d'outils --}}
        <div class="d-flex flex-wrap align-items-center gap-2 p-3 border-bottom">
            <div class="search-box" style="max-width:320px">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" class="form-control form-control-sm" placeholder="{{ $searchPlaceholder }}">
            </div>
            @if($showFilters)
                <button class="btn btn-sm btn-light border">
                    <i class="fa-solid fa-filter me-1 text-muted"></i> Filtrer
                </button>
            @endif
            @if($count !== null)
                <span class="badge badge-soft-gray ms-1">{{ $count }} élément(s)</span>
            @endif
            <div class="ms-auto d-flex gap-2">
                <button class="btn btn-sm btn-light border" title="Exporter en Excel">
                    <i class="fa-solid fa-file-excel text-uvci-green"></i>
                </button>
                <button class="btn btn-sm btn-light border" title="Exporter en PDF">
                    <i class="fa-solid fa-file-pdf text-danger"></i>
                </button>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>{{ $head }}</tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pied / pagination --}}
    <div class="card-footer bg-white d-flex flex-wrap align-items-center justify-content-between border-top">
        <span class="text-muted small">Affichage de 1 à {{ $count ?? 10 }} résultats</span>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled"><a class="page-link" href="#"><i class="fa-solid fa-chevron-left"></i></a></li>
                <li class="page-item active"><a class="page-link" href="#" style="background:var(--uvci-green);border-color:var(--uvci-green)">1</a></li>
                <li class="page-item"><a class="page-link text-uvci-green" href="#">2</a></li>
                <li class="page-item"><a class="page-link text-uvci-green" href="#">3</a></li>
                <li class="page-item"><a class="page-link text-uvci-green" href="#"><i class="fa-solid fa-chevron-right"></i></a></li>
            </ul>
        </nav>
    </div>
</div>
