{{-- Example Navigation Template with Dashboard Access Control --}}

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            Your App
        </a>

        <div class="navbar-nav">
            @auth
                {{-- Common navigation items for all authenticated users --}}
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="icon-home"></i> Home
                </a>
                
                <a class="nav-link" href="{{ route('profile') }}">
                    <i class="icon-user"></i> Profile
                </a>
                
                <a class="nav-link" href="{{ route('vehicles.index') }}">
                    <i class="icon-car"></i> Vehicles
                </a>

                {{-- Dashboard access - only for admin users --}}
                @if(auth()->user()->canAccessDashboard())
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle admin-only" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="icon-dashboard"></i> Dashboard
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="icon-dashboard"></i> Dashboard Home
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.users') }}">
                                <i class="icon-users"></i> Manage Users
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings') }}">
                                <i class="icon-settings"></i> Settings
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports') }}">
                                <i class="icon-chart"></i> Reports
                            </a></li>
                        </ul>
                    </div>
                @endif

                {{-- User info and logout --}}
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        {{ auth()->user()->name }}
                        
                        {{-- User type badge --}}
                        @if(auth()->user()->isAdmin())
                            <span class="badge bg-danger">Admin</span>
                        @else
                            <span class="badge bg-primary">User</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="dropdown-item-text">
                                <small class="text-muted">
                                    {{ auth()->user()->getUserTypeDescription() }}
                                </small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Profile Settings</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>

            @else
                {{-- Guest navigation --}}
                <a class="nav-link" href="{{ route('login') }}">Login</a>
                <a class="nav-link" href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Alert message for access denied --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- CSS Styling --}}
<style>
.admin-only {
    background-color: #dc3545 !important;
    color: white !important;
    border-radius: 4px;
    padding: 0.25rem 0.5rem !important;
}

.badge {
    font-size: 0.75em;
    margin-left: 0.5rem;
}
</style>

{{-- 
Usage Examples in Controllers:

// In a dashboard controller
public function index()
{
    // This check is handled by middleware, but you can double-check
    if (!auth()->user()->canAccessDashboard()) {
        abort(403, 'Access denied');
    }
    
    return view('dashboard.index');
}

// In a regular controller (accessible to all authenticated users)
public function profile()
{
    // All authenticated users can access this
    return view('profile');
}

// Conditional logic based on user type
public function someAction()
{
    if (auth()->user()->isAdmin()) {
        // Admin-specific logic
        return $this->handleAdminAction();
    } else {
        // Regular user logic
        return $this->handleUserAction();
    }
}

--}}