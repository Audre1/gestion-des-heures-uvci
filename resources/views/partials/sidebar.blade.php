<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo-long.png') }}" alt="logo uvci">
        </a>
    </div>

    <nav class="sidebar-nav">
        {{-- Général --}}
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i> Tableau de bord
        </a>

        {{-- Administration --}}
        <div class="nav-section-title">Administration</div>
        <a href="{{ route('utilisateurs.index') }}"
            class="nav-link {{ request()->routeIs('utilisateurs.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i> Utilisateurs
        </a>

        {{-- <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield"></i> Rôles
        </a> --}}

        <a href="{{ route('annees.index') }}" class="nav-link {{ request()->routeIs('annees.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i> Années académiques
        </a>

        <a href="{{ route('niveaux.index') }}" class="nav-link {{ request()->routeIs('niveaux.*') ? 'active' : '' }}">
            <i class="fa-solid fa-signal"></i> Niveaux de complexité
        </a>

        <a href="{{ route('parametres.index') }}"
            class="nav-link {{ request()->routeIs('parametres.*') ? 'active' : '' }}">
            <i class="fa-solid fa-sliders"></i> Paramètres de calcul
        </a>

        <a href="{{ route('taux.index') }}" class="nav-link {{ request()->routeIs('taux.*') ? 'active' : '' }}">
            <i class="fa-solid fa-money-bill-wave"></i> Taux horaires
        </a>

        <a href="{{ route('sauvegardes.index') }}"
            class="nav-link {{ request()->routeIs('sauvegardes.*') ? 'active' : '' }}">
            <i class="fa-solid fa-database"></i> Sauvegardes
        </a>

        <a href="{{ route('journaux.index') }}"
            class="nav-link {{ request()->routeIs('journaux.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-list"></i> Journaux d'activités
        </a>


        {{-- Gestion pédagogique --}}
        <div class="nav-section-title">Gestion pédagogique</div>
        <a href="{{ route('enseignants.index') }}"
            class="nav-link {{ request()->routeIs('enseignants.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chalkboard-user"></i> Enseignants
        </a>

        <a href="{{ route('grades.index') }}" class="nav-link {{ request()->routeIs('grades.*') ? 'active' : '' }}">
            <i class="fa-solid fa-ranking-star"></i> Grades
        </a>

        <a href="{{ route('departements.index') }}"
            class="nav-link {{ request()->routeIs('departements.*') ? 'active' : '' }}">
            <i class="fa-solid fa-building-columns"></i> Départements
        </a>

        <a href="{{ route('filieres.index') }}"
            class="nav-link {{ request()->routeIs('filieres.*') ? 'active' : '' }}">
            <i class="fa-solid fa-sitemap"></i> Filières
        </a>

        <a href="{{ route('cours.index') }}" class="nav-link {{ request()->routeIs('cours.*') ? 'active' : '' }}">
            <i class="fa-solid fa-book"></i> Cours
        </a>

        <a href="{{ route('affectations.index') }}"
            class="nav-link {{ request()->routeIs('affectations.*') ? 'active' : '' }}">
            <i class="fa-solid fa-link"></i> Affectations
        </a>

        <a href="{{ route('sequences.index') }}"
            class="nav-link {{ request()->routeIs('sequences.*') ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i> Séquences pédagogiques
        </a>

        <a href="{{ route('ressources.index') }}"
            class="nav-link {{ request()->routeIs('ressources.*') ? 'active' : '' }}">
            <i class="fa-solid fa-photo-film"></i> Ressources pédagogiques
        </a>

        <a href="{{ route('types.index') }}" class="nav-link {{ request()->routeIs('types.*') ? 'active' : '' }}">
            <i class="fa-solid fa-shapes"></i> Types de ressources
        </a>

        <a href="{{ route('activites.index') }}"
            class="nav-link {{ request()->routeIs('activites.*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i> Activités pédagogiques
        </a>

        {{-- Volumes & Paiements --}}
        <div class="nav-section-title">Volumes & Paiements</div>
        <a href="{{ route('volumes.index') }}" class="nav-link {{ request()->routeIs('volumes.*') ? 'active' : '' }}">
            <i class="fa-solid fa-hourglass-half"></i> Volumes horaires
        </a>

        <a href="{{ route('complementaires.index') }}"
            class="nav-link {{ request()->routeIs('complementaires.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i> Heures complémentaires
        </a>

        <a href="{{ route('paiements.index') }}"
            class="nav-link {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i> États de paiement
        </a>

        <a href="{{ route('rapports.index') }}"
            class="nav-link {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie"></i> Rapports & Statistiques
        </a>

        {{-- Espace enseignant --}}
        <div class="nav-section-title">Espace Enseignant</div>
        <a href="{{ route('espace.activites') }}"
            class="nav-link {{ request()->routeIs('espace.activites') ? 'active' : '' }}">
            <i class="fa-solid fa-folder-open"></i> Mes activités
        </a>

        <a href="{{ route('espace.volume') }}"
            class="nav-link {{ request()->routeIs('espace.volume') ? 'active' : '' }}">
            <i class="fa-solid fa-stopwatch"></i> Mon volume horaire
        </a>

        <a href="{{ route('espace.complementaires') }}"
            class="nav-link {{ request()->routeIs('espace.complementaires') ? 'active' : '' }}">
            <i class="fa-solid fa-hourglass-end"></i> Mes heures compl.
        </a>

        <a href="{{ route('espace.ressources') }}"
            class="nav-link {{ request()->routeIs('espace.ressources') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i> Mes ressources
        </a>

        <a href="{{ route('espace.documents') }}"
            class="nav-link {{ request()->routeIs('espace.documents') ? 'active' : '' }}">
            <i class="fa-solid fa-download"></i> Mes documents
        </a>

        {{-- Compte --}}
        <div class="nav-section-title">Compte</div>
        <a href="{{ route('profil.index') }}" class="nav-link {{ request()->routeIs('profil.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i> Mon profil
        </a>

        <a href="{{ route('login') }}" class="nav-link">
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
        </a>
    </nav>
</aside>
