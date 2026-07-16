<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tableau de bord') — UVCI</title>
    <link rel="icon" href="{{ asset('images/logo-simple.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/uvci.css') }}" rel="stylesheet">
    @yield('styles')
</head>

<body>
    <x-notifications />

    <div class="app-layout">
        @include('partials.sidebar')
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="app-main">
            @include('partials.topbar')
            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Modale de confirmation de déconnexion --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-right-from-bracket text-danger me-2"></i>
                        Confirmation de déconnexion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="logout-form-modal" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        // Ouverture / fermeture de la sidebar sur mobile
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
        document.getElementById('menuToggle')?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert.alert-success').forEach(alert => {
                setTimeout(() => {
                    alert.classList.add('d-none');
                }, 5000);
            });
        });
    </script>
    @stack('scripts')
    @yield('scripts')
</body>

</html>
