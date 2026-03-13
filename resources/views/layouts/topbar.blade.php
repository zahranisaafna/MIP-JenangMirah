
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle Buttons -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none mr-3" style="color: #b41111;">
        <i class="fa fa-bars fa-lg"></i>
    </button>
    
    <button id="sidebarToggle" class="btn btn-link d-none d-md-inline-block mr-3" style="color: #b41111;">
        <i class="fa fa-bars fa-lg"></i>
    </button>
    
    <!-- Page Title (Optional) -->
    {{-- <h1 class="h5 mb-0 text-gray-800 d-none d-sm-inline-block">
        @yield('page-title', '')
    </h1> --}}
    <h1 class="h5 mb-0 text-gray-800 d-none d-sm-inline-block">
    @yield('page-title', $pageTitle ?? '')
    </h1>


    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            {{-- <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ auth()->user()->name }}
                    <br>
                    <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
                </span>
                <i class="fas fa-user-circle fa-2x" style="color: #667eea;"></i>
            </a> --}}
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-inline user-name">
                        {{ auth()->user()->name }}
                        <br>
                        <small class="user-role">{{ ucfirst(auth()->user()->role) }}</small>
                    </span>

                    <!-- bisa pilih icon atau image -->
                    <i class="fas fa-user-circle user-avatar" style="color:#b41111;"></i>
                    {{-- <img class="img-profile" src="{{ auth()->user()->avatar_url }}" alt="profile"> --}}
                </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('profile.index') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- End of Topbar -->