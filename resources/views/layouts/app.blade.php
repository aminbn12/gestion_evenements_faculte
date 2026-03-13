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
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a3c5e;
            --secondary: #c8a951;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --light: #f8f9fa;
        }
        
        body {
            background-color: var(--light);
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #0d2137 100%);
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--secondary);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.sidebar-collapsed {
            margin-left: 70px;
        }
        
        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }
        
        .navbar-custom.sidebar-collapsed {
            margin-left: 70px;
        }
        
        .badge-priority-low { background-color: var(--success); }
        .badge-priority-medium { background-color: var(--warning); color: #212529; }
        .badge-priority-high { background-color: var(--danger); }
        .badge-priority-critical { 
            background-color: var(--primary); 
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        
        .stat-card {
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content,
            .navbar-custom {
                margin-left: 0;
            }
        }
        
        /* Sidebar Toggle Button */
        .sidebar-toggle {
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--secondary);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 101;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background-color: #b8943f;
        }
        
        /* Collapsed Sidebar Styles */
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .sidebar-brand {
            padding: 1rem 0.5rem;
        }
        
        .sidebar.collapsed .sidebar-brand h4,
        .sidebar.collapsed .sidebar-brand small {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            padding: 12px 15px;
            margin: 4px 8px;
            justify-content: center;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.25rem;
        }
        
        .sidebar.collapsed .nav-link::after {
            display: none;
        }
        
        /* Hide text nodes in nav links when collapsed - use flexbox to center */
        .sidebar.collapsed .nav-link {
            position: relative;
        }
        
        .sidebar.collapsed .nav-link:not(:has(i)) {
            display: none;
        }
        
        .sidebar.collapsed .nav-link:has(i)::after {
            content: attr(title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            white-space: nowrap;
            font-size: 12px;
            margin-left: 10px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .sidebar.collapsed .nav-link:has(i):hover::after {
            opacity: 1;
            visibility: visible;
        }
        
        .sidebar.collapsed + .main-content,
        .sidebar.collapsed ~ .navbar-custom {
            margin-left: 70px;
        }
        
        .sidebar.collapsed .sidebar-toggle {
            right: -15px;
        }
        
        .sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }
        
        .sidebar.collapsed .position-absolute {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @if(auth()->check())
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <button class="sidebar-toggle" id="sidebar-toggle-btn" title="Afficher/Masquer la sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
        <div class="p-4 sidebar-brand">
            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none">
                <h4 class="mb-1">
                    <i class="bi bi-mortarboard-fill"></i> Faculté UM6SS
                </h4>
                <small class="text-warning fw-bold">Gestion Événements FM6MD</small>
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
    <nav class="navbar navbar-expand navbar-light navbar-custom">
        <div class="container-fluid">
            <button class="btn btn-link d-md-none" type="button" id="sidebar-toggle">
                <i class="bi bi-list"></i>
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
    <main class="main-content">
        @yield('content')
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Sidebar toggle button (collapse/expand)
        document.getElementById('sidebar-toggle-btn')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const navbarCustom = document.querySelector('.navbar-custom');
            
            sidebar.classList.toggle('collapsed');
            
            // Update main content and navbar margin
            if (mainContent) {
                mainContent.classList.toggle('sidebar-collapsed');
            }
            if (navbarCustom) {
                navbarCustom.classList.toggle('sidebar-collapsed');
            }
        });
        
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    </script>
    
    @stack('scripts')
</body>
</html>
