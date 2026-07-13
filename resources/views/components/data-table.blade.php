@props([
    'searchPlaceholder' => 'Rechercher...',
    'count'         => null,
    'showFilters'   => true,
    'pageSize'      => 10,
    'id'            => 'table-' . uniqid(),
])

<div class="card"
     x-data="DataTable({
         tableId: '{{ $id }}',
         pageSize: {{ $pageSize }}
     })">

    {{-- Barre d'outils --}}
    <div class="card-body p-0">

        <div class="data-table-toolbar">

            {{-- Recherche --}}
            <div class="data-table-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search"
                       class="form-control form-control-sm"
                       placeholder="{{ $searchPlaceholder }}"
                       x-model="q"
                       @input.debounce.250ms="rechercher">
            </div>

            {{-- Filtres --}}
            @if ($showFilters && $filters ?? false)
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle"
                            type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-filter me-1 text-muted"></i>
                        Filtres
                    </button>
                    <div class="dropdown-menu p-3" style="min-width: 280px">
                        {{ $filters }}
                        <hr class="my-2">
                        <button type="button" class="btn btn-sm btn-uvci w-100" onclick="document.querySelectorAll('.dt-filter-select').forEach(s => s.value = ''); this.closest('[x-data]').__x.$data.rechercher()">
                            <i class="fa-solid fa-rotate me-1"></i> Réinitialiser
                        </button>
                    </div>
                </div>
            @endif

            {{-- Compteur --}}
            <span class="badge badge-soft-gray data-table-count"
                  x-text="info.compteur"></span>

            {{-- Actions d'export --}}
            <div class="data-table-actions">
                <button class="btn btn-sm btn-light border"
                        title="Exporter en CSV"
                        @click="exporterCSV">
                    <i class="fa-solid fa-file-excel text-uvci-green"></i>
                </button>
                <button class="btn btn-sm btn-light border"
                        title="Imprimer"
                        @click="window.print()">
                    <i class="fa-solid fa-file-pdf text-danger"></i>
                </button>
            </div>

        </div>

        {{-- Tableau --}}
        <div class="table-responsive">
            <table class="table align-middle" id="{{ $id }}">
                <thead>
                    <tr>{{ $head }}</tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        {{-- Aucun résultat --}}
        <div x-show="info.vide" class="text-center py-5 text-muted" x-cloak>
            <i class="fa-solid fa-search fa-3x mb-3"></i>
            <p class="mb-0 fw-semibold">Aucun résultat</p>
            <small>Modifiez vos critères de recherche.</small>
        </div>

    </div>

    {{-- Pagination --}}
    <div class="card-footer bg-white border-top dt-footer"
         x-show="!info.vide">

        <div class="dt-pagination">
            <small class="text-muted" x-text="info.pagination"></small>

            <div class="dt-pages" x-show="info.pages > 1">
                <button class="dt-page" :class="{ 'dt-disabled': page === 1 }"
                        @click="page > 1 && (page--, afficher())"
                        :disabled="page === 1"
                        aria-label="Précédent">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <template x-for="(p, i) in info.visibles" :key="i">
                    <button class="dt-page"
                            :class="{ 'dt-active': p === page, 'dt-disabled': p === '…' }"
                            x-text="p"
                            @click="typeof p === 'number' && (page = p, afficher())"
                            :disabled="p === '…'">
                    </button>
                </template>

                <button class="dt-page" :class="{ 'dt-disabled': page === info.pages }"
                        @click="page < info.pages && (page++, afficher())"
                        :disabled="page === info.pages"
                        aria-label="Suivant">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
function DataTable({ tableId, pageSize }) {
    return {
        q: '',
        page: 1,
        triCol: null,
        triSens: 'asc',

        get el()     { return document.getElementById(tableId) },
        get tbody()  { return this.el?.querySelector('tbody') },
        get lignes() { return Array.from(this.tbody?.querySelectorAll('tr') ?? []) },

        get filtrees() {
            let data = this.lignes.map(l => ({ el: l, texte: l.textContent.toLowerCase() }));

            const mot = this.q.trim().toLowerCase();
            if (mot) data = data.filter(d => d.texte.includes(mot));

            if (this.triCol !== null) {
                data.sort((a, b) => {
                    const va = (a.el.querySelectorAll('td')[this.triCol]?.textContent.trim().toLowerCase() ?? '');
                    const vb = (b.el.querySelectorAll('td')[this.triCol]?.textContent.trim().toLowerCase() ?? '');
                    const na = parseFloat(va.replace(/[^0-9,.-]/g, '').replace(',', '.'));
                    const nb = parseFloat(vb.replace(/[^0-9,.-]/g, '').replace(',', '.'));
                    const cmp = isNaN(na) || isNaN(nb) ? va.localeCompare(vb) : na - nb;
                    return this.triSens === 'asc' ? cmp : -cmp;
                });
            }
            return data;
        },

        get info() {
            const total = this.filtrees.length;
            const pages = Math.max(1, Math.ceil(total / pageSize));
            const p = this.page;
            const visibles = [];
            if (pages <= 7) {
                for (let i = 1; i <= pages; i++) visibles.push(i);
            } else {
                visibles.push(1);
                if (p > 3) visibles.push('…');
                for (let i = Math.max(2, p - 1); i <= Math.min(pages - 1, p + 1); i++) visibles.push(i);
                if (p < pages - 2) visibles.push('…');
                visibles.push(pages);
            }
            return {
                total, pages, visibles,
                compteur: `${total} élément${total > 1 ? 's' : ''}`,
                vide: total === 0,
                pagination: total === 0 ? 'Aucun résultat'
                    : `Affichage ${(p - 1) * pageSize + 1}–${Math.min(p * pageSize, total)} sur ${total}`,
            };
        },

        afficher() {
            const debut = (this.page - 1) * pageSize;
            const fin = this.page * pageSize;
            this.lignes.forEach(l => l.style.display = 'none');
            this.filtrees.slice(debut, fin).forEach(d => d.el.style.display = '');
        },

        rechercher() { this.page = 1; this.afficher(); },

        trier(idx) {
            if (this.triCol === idx) {
                this.triSens = this.triSens === 'asc' ? 'desc' : 'asc';
            } else {
                this.triCol = idx;
                this.triSens = 'asc';
            }
            this.page = 1;
            this.afficher();
            this.majIconesTri();
        },

        exporterCSV() {
            const lignes = [];
            const thead = this.el?.querySelector('thead');
            if (!thead) return;
            const ths = thead.querySelectorAll('th');
            lignes.push(Array.from(ths).map(th => JSON.stringify(th.textContent.trim())).join(','));
            this.filtrees.forEach(d => {
                const tds = d.el.querySelectorAll('td');
                lignes.push(Array.from(tds).map(td => JSON.stringify(td.textContent.trim())).join(','));
            });
            const csv = '\uFEFF' + lignes.join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `export-${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(a); a.click();
            document.body.removeChild(a); URL.revokeObjectURL(url);
        },

        filtrerColonne(index, valeur) {
            this.lignes.forEach(l => {
                const td = l.querySelectorAll('td')[index];
                if (!td) return;
                const texte = td.textContent.trim().toLowerCase();
                if (!valeur || texte === valeur.toLowerCase()) {
                    l.style.display = '';
                } else {
                    l.style.display = 'none';
                }
            });
            this.page = 1;
        },

        majIconesTri() {
            const ths = this.el?.querySelectorAll('th') ?? [];
            ths.forEach(th => th.querySelector('.sort-icon')?.remove());
            if (this.triCol === null) return;
            const th = ths[this.triCol];
            if (!th) return;
            const icone = document.createElement('span');
            icone.className = 'sort-icon ms-1';
            icone.innerHTML = this.triSens === 'asc' ? '▲' : '▼';
            th.appendChild(icone);
        },

        init() {
            this.$nextTick(() => {
                this.afficher();
                const ths = this.el?.querySelectorAll('th') ?? [];
                ths.forEach((th, i) => {
                    if (th.textContent.trim().toLowerCase() === 'actions') return;
                    th.style.cursor = 'pointer';
                    th.title = 'Trier';
                    th.addEventListener('click', () => this.trier(i));
                });
            });
        },
    };
}

// Filtre le tableau par colonne — appelé depuis les selects dans le slot $filters
function filtrerDataTable(colIndex) {
    const sel = event.target;
    const val = sel.value.trim();
    const card = sel.closest('.card');
    if (!card) return;

    // Récupérer l'instance Alpine du composant
    const alpine = card.__x;
    if (!alpine) return;

    // Appliquer le filtre via la méthode Alpine
    alpine.$data.filtrerColonne(colIndex, val);
}
</script>
<style>
[x-cloak] { display: none !important; }

.data-table-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
    background: #fafbfd;
}

.data-table-search {
    position: relative;
    max-width: 420px;
    flex: 1 1 300px;
}
.data-table-search i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.85rem;
    z-index: 2;
}
.data-table-search input {
    padding-left: 34px !important;
    background: #fff;
}

.data-table-count {
    flex-shrink: 0;
}

.data-table-actions {
    margin-left: auto;
    display: flex;
    gap: 0.4rem;
    flex-shrink: 0;
}

.table thead th {
    user-select: none;
    transition: background 0.15s;
}
.table thead th:hover {
    background: #f0f1f5 !important;
}
.sort-icon {
    font-size: 9px;
    margin-left: 4px;
    opacity: 0.6;
}

/* ── Pagination (custom) ─────────────────────────────────── */
.dt-footer { padding: 0.6rem 1rem !important; }
.dt-pagination {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    width: 100%;
}
.dt-pages {
    display: flex;
    align-items: center;
    gap: 2px;
}
.dt-page {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 0.5rem;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: #fff;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}
.dt-page:hover:not(.dt-active):not(.dt-disabled) {
    background: var(--uvci-green-light);
    border-color: var(--uvci-green);
    color: var(--uvci-green-dark);
}
.dt-page.dt-active {
    background: var(--uvci-green);
    border-color: var(--uvci-green);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0, 165, 78, 0.3);
}
.dt-page.dt-disabled { opacity: 0.35; cursor: default; background: #f9fafb; }
.dt-page:disabled { cursor: default; }

/* ── Filtres dans le dropdown ────────────────────────────── */
.dt-filter-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.2rem;
    display: block;
}
.dt-filter-select {
    font-size: 0.82rem;
}

@media (max-width: 576px) {
    .data-table-toolbar { flex-direction: column; align-items: stretch; }
    .data-table-search { max-width: 100%; }
    .data-table-actions { margin-left: 0; }
    .dt-pagination { flex-direction: column; text-align: center; }
    .dt-pages { justify-content: center; }
}
</style>
@endpush