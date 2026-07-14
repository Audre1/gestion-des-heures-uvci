<header class="topbar">
    <button class="topbar-icon-btn menu-toggle" id="menuToggle" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    {{-- Barre de recherche adaptée au rôle --}}
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" class="form-control" placeholder="{{ $searchPlaceholder }}"
               id="globalSearch" autocomplete="off">
    </div>

    <div class="topbar-actions">
        {{-- Sélecteur d'année académique --}}
        <div class="dropdown d-none d-md-block me-2">
            <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown">
                <i class="fa-solid fa-calendar-days text-uvci-green"></i>
                <span>{{ $currentYear?->libelle ?? 'Année non définie' }}</span>
            </button>
            <ul class="dropdown-menu">
                @forelse ($allYears as $year)
                    @php $badge = $yearStatusBadge($year->statut); @endphp
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ $year->statut === 'en_cours' ? 'active' : '' }}"
                           href="#"
                           data-year-id="{{ $year->id }}">
                            {{ $year->libelle }}
                            @if ($badge)
                                <span class="badge {{ $badge['class'] }} ms-2" style="font-size:.65rem">{{ $badge['label'] }}</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li><span class="dropdown-item text-muted">Aucune année</span></li>
                @endforelse
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

                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit"
                            class="dropdown-item text-danger w-100 text-start border-0 bg-transparent">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>