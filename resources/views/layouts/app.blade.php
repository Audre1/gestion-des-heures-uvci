<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tableau de bord') — UVCI</title>
    <link rel="icon" href="{{ asset('images/logo-simple.png') }}">

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    @yield('scripts')
</body>

</html>
