<header class="topbar">
    <button class="topbar-icon-btn menu-toggle" id="menuToggle" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    {{-- Barre de recherche adaptée au rôle --}}
    <div class="search-box position-relative">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" class="form-control" placeholder="{{ $searchPlaceholder }}" id="globalSearch"
            autocomplete="off">

        {{-- Dropdown des résultats de recherche --}}
        <div id="searchResults" class="search-results-dropdown dropdown-menu w-100 shadow" style="display: none;">
            <div class="search-loading text-center py-3" id="searchLoading" style="display: none;">
                <i class="fa-solid fa-spinner fa-spin"></i> Recherche en cours...
            </div>
            <div class="search-content" id="searchContent"></div>
            <div class="search-no-results text-center py-3 text-muted" id="searchNoResults" style="display: none;">
                <i class="fa-solid fa-search mb-2"></i>
                <p class="mb-0">Aucun résultat trouvé</p>
            </div>
        </div>
    </div>

    <div class="topbar-actions">
        {{-- Sélecteur d'année académique --}}
        <div class="dropdown d-none d-md-block me-2">
            <button class="btn btn-sm btn-light border d-flex align-items-center gap-1" data-bs-toggle="dropdown">
                <i class="fa-solid fa-calendar-days text-uvci-green"></i>
                <span>{{ $currentYear?->libelle ?? 'Année non définie' }}</span>
            </button>
            <ul class="dropdown-menu">
                @if ($currentYear)
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between active"
                            href="#">{{ $currentYear->libelle }} <span class="badge bg-gray ms-2"
                                style="font-size:.65rem">En cours</span>
                        </a>
                    </li>
                @else
                    <li><span class="dropdown-item text-muted">Aucune année</span></li>
                @endif
            </ul>
        </div>

        {{-- Menu utilisateur --}}
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown">
                <img src="{{ asset('images/avatar-default.jpg') }}" alt="Avatar" class="avatar-img">

                <div class="user-meta">
                    <div class="fw-semibold" style="line-height:1.1">{{ $userName }}</div>
                    <div class="text-muted" style="font-size:.75rem">{{ $roleLabel }}</div>
                </div>

                <i class="fa-solid fa-chevron-down text-muted ms-1" style="font-size:.7rem"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('profil.index') }}">
                        <i class="fa-solid fa-user me-2 text-muted"></i> Mon profil
                    </a>
                </li>

                @if ($userRole === 'admin')
                    <li>
                        <a class="dropdown-item" href="{{ route('parametres.index') }}">
                            <i class="fa-solid fa-gear me-2 text-muted"></i> Paramètres
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('sauvegardes.index') }}">
                            <i class="fa-solid fa-database me-2 text-muted"></i> Sauvegardes
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('journaux.index') }}">
                            <i class="fa-solid fa-clipboard-list me-2 text-muted"></i> Journaux
                        </a>
                    </li>
                @endif

                @if ($userRole === 'admin' || $userRole === 'secretaire')
                    <li>
                        <a class="dropdown-item" href="{{ route('rapports.index') }}">
                            <i class="fa-solid fa-chart-pie me-2 text-muted"></i> Rapports
                        </a>
                    </li>
                @endif

                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <button type="button" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent"
                        data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
                    </button>
                </li>
            </ul>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearch');
            const searchResults = document.getElementById('searchResults');
            const searchLoading = document.getElementById('searchLoading');
            const searchContent = document.getElementById('searchContent');
            const searchNoResults = document.getElementById('searchNoResults');

            let searchTimeout;
            let currentResultIndex = -1;
            let resultItems = [];

            // Debounce pour éviter trop de requêtes
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();

                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            // Fermer le dropdown en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });

            // Fermer avec Escape
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchResults.style.display = 'none';
                    searchInput.blur();
                }
            });

            // Navigation au clavier
            searchInput.addEventListener('keydown', function(e) {
                if (searchResults.style.display === 'none') return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentResultIndex = Math.min(currentResultIndex + 1, resultItems.length - 1);
                    highlightResult();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentResultIndex = Math.max(currentResultIndex - 1, 0);
                    highlightResult();
                } else if (e.key === 'Enter' && currentResultIndex >= 0) {
                    e.preventDefault();
                    resultItems[currentResultIndex].querySelector('a').click();
                }
            });

            function performSearch(query) {
                searchResults.style.display = 'block';
                searchLoading.style.display = 'block';
                searchContent.innerHTML = '';
                searchNoResults.style.display = 'none';
                currentResultIndex = -1;
                resultItems = [];

                fetch(`{{ route('search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.style.display = 'none';

                        if (Object.keys(data.results).length === 0 ||
                            Object.values(data.results).every(arr => arr.length === 0)) {
                            searchNoResults.style.display = 'block';
                            return;
                        }

                        displayResults(data.results);
                    })
                    .catch(error => {
                        console.error('Erreur de recherche:', error);
                        searchLoading.style.display = 'none';
                        searchNoResults.style.display = 'block';
                    });
            }

            function displayResults(results) {
                const categoryLabels = {
                    enseignants: 'Enseignants',
                    cours: 'Cours',
                    filieres: 'Filières',
                    departements: 'Départements',
                    activites: 'Activités',
                    ressources: 'Ressources'
                };

                for (const [category, items] of Object.entries(results)) {
                    if (items.length === 0) continue;

                    const categoryDiv = document.createElement('div');
                    categoryDiv.className = 'search-category';
                    categoryDiv.innerHTML = `
                    <div class="search-category-header px-3 py-2 bg-light fw-bold small text-muted">
                        <span>${categoryLabels[category] || category}</span>
                    </div>`;

                    items.forEach(item => {
                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'search-result-item px-3 py-2 border-bottom';
                        itemDiv.innerHTML = `
                <a href="${item.url}" class="text-decoration-none text-dark d-block">
                    <div class="fw-semibold">${item.label}</div>
                    <div class="small text-muted">${item.subtitle}</div>
                </a>
            `;
                        categoryDiv.appendChild(itemDiv);
                        resultItems.push(itemDiv);
                    });

                    searchContent.appendChild(categoryDiv);
                }
            }

            function highlightResult() {
                resultItems.forEach((item, index) => {
                    if (index === currentResultIndex) {
                        item.classList.add('bg-light');
                    } else {
                        item.classList.remove('bg-light');
                    }
                });
            }
        });
    </script>
@endpush
