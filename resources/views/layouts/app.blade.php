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
        }
        
        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 250px;
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
    </style>
    
    @stack('styles')
</head>
<body>
    @if(auth()->check())
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="p-4">
            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none">
                <h4 class="mb-1">
                    <i class="bi bi-mortarboard-fill"></i> Faculté
                </h4>
                <small class="text-muted">Gestion Événements</small>
            </a>
        </div>
        
        <hr class="border-secondary mx-3">
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                    <i class="bi bi-calendar-event"></i> Événements
                </a>
            </li>
            
            @if(auth()->user()->hasPermission('send-alerts'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}" href="{{ route('alerts.index') }}">
                    <i class="bi bi-bell"></i> Alertes
                </a>
            </li>
            @endif
            
            @if(auth()->user()->hasRole('manager'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                    <i class="bi bi-shield-lock"></i> Rôles
                </a>
            </li>
            @endif
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}" href="{{ route('team.index') }}">
                    <i class="bi bi-diagram-3"></i> Équipe
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}" href="{{ route('leaves.index') }}">
                    <i class="bi bi-calendar-x"></i> Congés
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
        
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    </script>
    
    @stack('scripts')
</body>
</html>
