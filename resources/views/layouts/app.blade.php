<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion Événements - Faculté')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="preload">
    @if(auth()->check())
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="p-4 sidebar-brand text-center d-flex flex-column align-items-center">
            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none d-flex flex-column align-items-center">
                <div class="brand-icon-wrapper bg-white bg-opacity-10 p-2 rounded-3 mb-2 transition-all">
                    <i class="bi bi-mortarboard-fill fs-3 text-white"></i>
                </div>
                <div class="brand-text transition-all overflow-hidden text-nowrap">
                    <h5 class="mb-0 fw-bold">Faculté UM6SS</h5>
                    <small class="text-warning fw-bold" style="font-size: 11px;">Gestion Événements</small>
                </div>
            </a>
        </div>
        
        <hr class="border-secondary mx-3">
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}" title="Événements">
                    <i class="bi bi-calendar-event"></i> Événements
                </a>
            </li>
            
            @if(auth()->user()->hasPermission('send-alerts'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}" href="{{ route('alerts.index') }}" title="Alertes">
                    <i class="bi bi-bell"></i> Alertes
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole('manager'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" title="Utilisateurs">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}" title="Rôles">
                    <i class="bi bi-shield-lock"></i> Rôles
                </a>
            </li>
            @endif
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}" href="{{ route('team.index') }}" title="Équipe">
                    <i class="bi bi-diagram-3"></i> Équipe
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}" href="{{ route('leaves.index') }}" title="Congés">
                    <i class="bi bi-calendar-x"></i> Congés
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}" title="Surveillance Examens">
                    <i class="bi bi-clipboard-check"></i> Surveillance Examens
                </a>
            </li>
        </ul>
        
        <div class="position-absolute bottom-0 w-100 p-3">
            <small class="text-muted text-center d-block">
                © {{ date('Y') }} UM6SS
            </small>
        </div>
    </nav>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-light navbar-custom" id="navbar-custom">
        <div class="container-fluid">
            <!-- Sidebar Toggles -->
            <button class="btn btn-link text-dark d-none d-md-block me-3 p-1" type="button" id="sidebar-toggle-btn" title="Réduire/Agrandir le menu">
                <i class="bi bi-list fs-4"></i>
            </button>
            <button class="btn btn-link text-dark d-md-none p-1" type="button" id="sidebar-toggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            
            <div class="navbar-nav ms-auto">
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div id="notifications-list">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                            <a class="dropdown-item" href="{{ route('notifications.show', $notification->id) }}">
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                <p class="mb-0">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            </a>
                            @empty
                            <span class="dropdown-item text-muted">Aucune notification</span>
                            @endforelse
                        </div>
                    </div>
                </li>
                
                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="avatar me-2">
                        {{ auth()->user()->full_name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </div>
        </div>
    </nav>
    @endif
    
    <!-- Main Content -->
    <main class="main-content" id="main-content">
        @yield('content')
    </main>
    
    <!-- Sync sidebar state before paint -->
    <script>
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.getElementById('sidebar')?.classList.add('collapsed');
            document.getElementById('navbar-custom')?.classList.add('sidebar-collapsed');
            document.getElementById('main-content')?.classList.add('sidebar-collapsed');
        }
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    @stack('scripts')
</body>
</html>

