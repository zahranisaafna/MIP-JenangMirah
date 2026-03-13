<ul class="navbar-nav sidebar" id="sidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex flex-column align-items-center justify-content-center" href="{{ route('dashboard') }}" style="padding: 10rem 0;">
        <div class="sidebar-brand-icon" style="margin-bottom: 20px;">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:100px; height:100px;">
        </div>
        <div class="sidebar-brand-text">
            <span style="display:block; margin-bottom: 20px;">MANAJEMEN</span>
            <span style="display:block; margin-bottom: 20px;">INVENTORY</span>
            <span style="display:block; margin-bottom: 20px;">PRODUKSI</span>
            <span style="display:block; margin-bottom: 20px;">JENANG MIRAH</span>
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Beranda -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard', 'produksi.dashboard', 'owner.dashboard') ? 'active' : '' }}">
        <a class="nav-link py-3" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span style="font-size: 1.2rem;">Beranda</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider my-2">

    @if(auth()->user()->role === 'admin')
    <!-- Heading -->
    {{-- <div class="sidebar-heading" style="padding: 1.2rem 1rem; margin-bottom: 0.5rem;">
        DATA BAHAN & PEMBELIAN
    </div> --}}

    <!-- Nav Item - Bahan Baku -->
    <li class="nav-item {{ request()->routeIs('bahan-baku.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('bahan-baku.index') }}">
            <i class="fas fa-fw fa-boxes"></i>
            <span style="font-size: 1.2rem;">Data Bahan Baku</span>
        </a>
    </li>

    <!-- Nav Item - Supplier -->
    <li class="nav-item {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('supplier.index') }}">
            <i class="fas fa-fw fa-truck"></i>
            <span style="font-size: 1.2rem;">Supplier</span>
        </a>
    </li>

    <!-- Nav Item - Pembelian -->
    <li class="nav-item {{ request()->routeIs('pembelian.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('pembelian.index') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span style="font-size: 1.2rem;">Pembelian</span>
        </a>
    </li>

    <!-- Nav Item - Resep -->
    <li class="nav-item {{ request()->routeIs('resep.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('resep.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span style="font-size: 1.2rem;">Komposisi Produk</span>
        </a>
    </li> 
    
    <!-- Divider -->
    <hr class="sidebar-divider my-2">
    
    <!-- Heading -->
    {{-- <div class="sidebar-heading" style="padding: 0.75rem 1rem; margin-bottom: 0.5rem;">
        Pengaturan Pengguna
    </div> --}}
    
    <!-- Nav Item - User Management -->
    <li class="nav-item {{ request()->routeIs('setting-user.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('setting-user.index') }}">
            <i class="fas fa-fw fa-users-cog"></i>
            <span style="font-size: 1.2rem;">Kelola Pengguna</span>
        </a>
    </li>
    @endif

    @if(auth()->user()->role === 'karyawanproduksi')
    <!-- Heading -->

    {{-- <div class="sidebar-heading" style="padding: 0.75rem 1rem; margin-bottom: 0.5rem;">
        Data Resep Produk & Produksi
    </div> --}}
    
    {{-- <!-- Nav Item - Resep -->
    <li class="nav-item {{ request()->routeIs('resep.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('resep.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span style="font-size: 1.2rem;">Resep Produk</span>
        </a>
    </li> --}}
    
    <!-- Nav Item - Produksi -->
    <li class="nav-item {{ request()->routeIs('produksi.*') && !request()->routeIs('produksi.dashboard') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('produksi.index') }}">
            <i class="fas fa-fw fa-industry"></i>
            <span style="font-size: 1.2rem;">Produksi</span>
        </a>
    </li>

    <!-- Nav Item - Distribusi -->
    <li class="nav-item {{ request()->routeIs('distribusi.*') ? 'active' : '' }}">
       <a class="nav-link py-2" href="{{ route('distribusi.index') }}">
            <i class="fas fa-fw fa-shipping-fast"></i>
            <span style="font-size: 1.2rem;">Distribusi Produk</span>
        </a>
    </li>
    @endif

    @if(auth()->user()->role === 'owner')
    <!-- Heading -->
    {{-- <div class="sidebar-heading" style="padding: 0.75rem 1rem; margin-bottom: 0.5rem;">
        Laporan
    </div> --}}

    <!-- Nav Item - Laporan Produksi -->
    <li class="nav-item {{ request()->routeIs('laporan.produksi.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('laporan.produksi.index') }}">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span style="font-size: 1.2rem;">Laporan Produksi</span>
        </a>
    </li>

    <!-- Nav Item - Laporan Distribusi -->
    <li class="nav-item {{ request()->routeIs('laporan.distribusi.*') ? 'active' : '' }}">
        <a class="nav-link py-2" href="{{ route('laporan.distribusi.index') }}">
            <i class="fas fa-fw fa-truck"></i>
            <span style="font-size: 1.2rem;">Laporan Distribusi</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Nav Item - Logout -->
    <li class="nav-item">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </li>
</ul>
<!-- End of Sidebar -->


{{-- <ul class="navbar-nav sidebar" id="sidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:40px; height:40px;">
        </div>
        <div class="sidebar-brand-text mx-2">MIP Jenang Mirah</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Beranda -->
    <li class="nav-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Beranda</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    @if(auth()->user()->role === 'admin')
    <!-- Heading -->
    <div class="sidebar-heading">
        DATA BAHAN & PEMBELIAN
    </div>

    <!-- Nav Item - Bahan Baku -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('bahan-baku.index') }}">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Data Bahan Baku</span>
        </a>
    </li>

    <!-- Nav Item - Supplier -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('supplier.index') }}">
            <i class="fas fa-fw fa-truck"></i>
            <span>Supplier</span>
        </a>
    </li>

    <!-- Nav Item - Pembelian -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('pembelian.index') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Pembelian</span>
        </a>
    </li>
    
    <!-- Divider -->
    <hr class="sidebar-divider">
    
    <!-- Heading -->
    <div class="sidebar-heading">
        Pengaturan Pengguna
    </div>
    
    <!-- Nav Item - User Management -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('setting-user.index') }}">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Kelola Pengguna</span>
        </a>
    </li>
    @endif

    @if(auth()->user()->role === 'karyawanproduksi')
    <!-- Heading -->
    <div class="sidebar-heading">
        Data Resep Produk & Produksi
    </div>
    
    <!-- Nav Item - Resep -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-book"></i>
            <span>Resep Produk</span>
        </a>
    </li>
    
    <!-- Nav Item - Produksi -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-industry"></i>
            <span>Produksi</span>
        </a>
    </li>

    <!-- Nav Item - Distribusi -->
    <li class="nav-item">
       <a class="nav-link" href="#">
            <i class="fas fa-fw fa-shipping-fast"></i>
            <span>Distribusi Produk</span>
        </a>
    </li>
    @endif

    @if(auth()->user()->role === 'owner')
    <!-- Heading -->
    <div class="sidebar-heading">
        Laporan
    </div>

    <!-- Nav Item - Laporan Produksi -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Laporan Produksi</span>
        </a>
    </li>

    <!-- Nav Item - Laporan Distribusi -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Laporan Distribusi</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Nav Item - Logout -->
    <li class="nav-item">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </li>
</ul>
<!-- End of Sidebar --> --}}