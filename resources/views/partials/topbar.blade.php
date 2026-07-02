<header class="topbar">
    <button class="topbar-icon-btn menu-toggle" id="menuToggle" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" class="form-control" placeholder="Rechercher un enseignant, un cours...">
    </div>

    <div class="topbar-actions">
        {{-- Sélecteur d'année académique --}}
        <div class="dropdown d-none d-md-block me-2">
            <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-calendar-days text-uvci-green me-1"></i> 2025-2026
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item active" href="#">2025-2026</a></li>
                <li><a class="dropdown-item" href="#">2024-2025</a></li>
                <li><a class="dropdown-item" href="#">2023-2024</a></li>
                <li><a class="dropdown-item" href="#">2022-2023</a></li>
            </ul>
        </div>

        <button class="topbar-icon-btn" aria-label="Notifications">
            <i class="fa-regular fa-bell"></i><span class="dot"></span>
        </button>
        <button class="topbar-icon-btn" aria-label="Messages">
            <i class="fa-regular fa-envelope"></i>
        </button>

        {{-- Menu utilisateur --}}
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown">
                {{-- @if (Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="avatar-img">
                @else --}}
                    <img src="{{ asset('images/avatar-default.jpg') }}" alt="Avatar" class="avatar-img">
                {{-- @endif --}}
                <div class="user-meta">
                    <div class="fw-semibold" style="line-height:1.1">{{ Auth::user()->name ?? 'Utilisateur' }}</div>
                    <div class="text-muted" style="font-size:.75rem">{{ Auth::user()->role ?? 'Utilisateur' }}</div>
                </div>
                <i class="fa-solid fa-chevron-down text-muted ms-1" style="font-size:.7rem"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profil.index') }}"><i
                            class="fa-solid fa-user me-2 text-muted"></i> Mon profil</a></li>
                <li><a class="dropdown-item" href="{{ route('parametres.index') }}"><i
                            class="fa-solid fa-gear me-2 text-muted"></i> Paramètres</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i
                            class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion</a></li>
            </ul>
        </div>
    </div>
</header>
