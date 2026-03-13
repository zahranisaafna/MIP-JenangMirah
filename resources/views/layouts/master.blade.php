<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.ico') }}?v=2" type="image/x-icon">


    
    <title>@yield('title') - Sistem Manajemen Produksi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery dynamic rows -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- FORMAT FILTER TANGGAL --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            overflow-x: hidden;
        }
        
        #wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles - Updated gradient to match login */
        #sidebar {
            width: 250px;
            /* background: linear-gradient(145deg, #ce283c 0%, #b7091d 40%, #800014 100%); */
            /* background: linear-gradient(145deg, #7AAEDC 0%, #5D8CC9 40%, #2C3E50 100%); */
            background: linear-gradient(145deg, #F24455 0%, #E5203A 20%, #660F24 40%);
            box-shadow: inset 0 0 15px rgba(0,0,0,0.3);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: margin-left 0.3s ease-in-out;
            /* width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: margin-left 0.3s ease-in-out; */
        }
        .sidebar-brand,
        .sidebar-brand:link,
        .sidebar-brand:visited,
        .sidebar-brand:hover,
        .sidebar-brand:active {
            color: #ffffff !important;
            text-decoration: none !important;
        }

        .sidebar-brand-text {
            color: #ffffff !important;
        }

        .sidebar-brand:hover .sidebar-brand-text,
        .sidebar-brand:active .sidebar-brand-text {
            color: #ffffff !important;
        }
        
        #sidebar.toggled {
            margin-left: -250px;
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            z-index: 1;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-brand-icon {
            margin-right: 0.5rem;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0 1rem 1rem;
        }
        
        .sidebar-heading {
            text-align: center;
            padding: 0 1rem;
            font-weight: 800;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.1rem;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }
        
        .nav-link.active {
            color: #fff;
            font-weight: 700;
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .nav-link i {
            font-size: 0.85rem;
            margin-right: 0.75rem;
            min-width: 2rem;
        }
        /* Kotak putih JELAS untuk menu aktif di sidebar */
        #sidebar .nav-item.active .nav-link,
        #sidebar .nav-item .nav-link.active {
            background: #ffffff !important;
            color: #E5203A !important;  /* teks merah */
            border-radius: 8px;
            font-weight: 700;
            margin: 0 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* Icon di dalam link aktif ikut merah */
        #sidebar .nav-item.active .nav-link i,
        #sidebar .nav-item .nav-link.active i {
            color: #E5203A !important;  /* icon merah */
        }

        /* Span text juga merah */
        #sidebar .nav-item.active .nav-link span,
        #sidebar .nav-item .nav-link.active span {
            color: #E5203A !important;  /* text merah */
        }

        /* Hover: kotak transparan */
        #sidebar .nav-item .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #E5203A !important;
            border-radius: 8px;
            margin: 0 12px;
        }

        #sidebar .nav-item .nav-link:hover i,
        #sidebar .nav-item .nav-link:hover span {
            color: #E5203A !important;
        }

        
        /* Content Wrapper */
        #content-wrapper {
            background-color: #f8f9fc;
            width: 100%;
            overflow-x: hidden;
            margin-left: 250px;
            transition: margin-left 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        #content-wrapper.toggled {
            margin-left: 0;
        }
        
        #content {
            flex: 1 0 auto;
        }
        
        /* Topbar */
        .topbar {
            height: 4.375rem;
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .topbar .nav-link {
            color: #ede5e5;
        }
        
        .topbar .dropdown-menu {
            width: 15rem;
            right: 0;
            left: auto;
        }
        
        .topbar .dropdown-item {
            font-size: 0.85rem;
            padding: 0.5rem 1.5rem;
        }
        
        /* Sidebar Toggle Button */
        #sidebarToggle {
            width: 2.5rem;
            height: 2.5rem;
            text-align: center;
            margin-bottom: 1rem;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.35rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }
        
        #sidebarToggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        
        #sidebarToggleTop {
            height: 2.5rem;
            width: 2.5rem;
        }
        
        /* Card Styles */
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
        }
        
        .shadow {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }
        
        .border-left-primary {
            border-left: 0.25rem solid #667eea !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        
        .text-gray-300 {
            color: #dddfeb !important;
        }
        
        /* Footer - Fixed to bottom */
        .sticky-footer {
            flex-shrink: 0;
            margin-top: auto;
            padding: 2rem 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.toggled {
                margin-left: 0;
            }
            
            #content-wrapper {
                margin-left: 0;
            }
            
            #content-wrapper.toggled {
                margin-left: 0;
            }
        }
        
        @media (min-width: 769px) {
            #sidebarToggleTop {
                display: none;
            }
        }
        /* Bikin tombol SweetAlert OK kembali terlihat */
        .swal2-confirm {
            background-color: #E5203A  !important; /* ungu */
            color: #ffffff !important;           /* teks putih */
            border: none !important;
            font-weight: 600 !important;
            padding: 8px 25px !important;
            border-radius: 6px !important;
        }

        .swal2-confirm:hover {
            background-color: #E5203A  !important;
        }

    </style>
    
    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar (include) -->
        @include('layouts.sidebar')
        <!-- Sidebar End -->
        
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                @include('layouts.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    {{-- @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif --}}

                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer - Now at bottom -->
            <footer class="sticky-footer bg-white" style="padding: 1rem 0; margin-top: auto;">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span style="font-size: 0.875rem;">
                            Copyright &copy; Sistem Manajemen Produksi {{ date('Y') }}
                        </span>
                    </div>
                </div>
            </footer>
            {{-- <footer class="sticky-footer bg-white"
                    style="height: 50px; width: 100%; display: flex; align-items: center; justify-content: center;">

                <div class="container my-auto">
                    <div class="copyright text-center text-nowrap my-auto">
                        <span>Copyright &copy; Sistem Manajemen Produksi {{ date('Y') }}</span>
                    </div>
                </div>
            </footer> --}}
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah jika Anda siap untuk mengakhiri sesi Anda saat ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom scripts for sidebar toggle -->
    <script>
        $(document).ready(function() {
            // Toggle sidebar on desktop
            $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
                e.preventDefault();
                
                // Toggle classes
                $("#sidebar").toggleClass("toggled");
                $("#content-wrapper").toggleClass("toggled");
                
                // Store state in localStorage
                if ($("#sidebar").hasClass("toggled")) {
                    localStorage.setItem('sidebarToggled', 'true');
                } else {
                    localStorage.setItem('sidebarToggled', 'false');
                }
            });
            
            // Restore sidebar state from localStorage
            if (localStorage.getItem('sidebarToggled') === 'true') {
                $("#sidebar").addClass("toggled");
                $("#content-wrapper").addClass("toggled");
            }
            
            // Close sidebar on mobile when clicking outside
            $(document).on('click', function(e) {
                if ($(window).width() < 769) {
                    if (!$(e.target).closest('#sidebar').length && 
                        !$(e.target).closest('#sidebarToggleTop').length && 
                        !$("#sidebar").hasClass("toggled")) {
                        $("#sidebar").addClass("toggled");
                    }
                }
            });
            
            // Auto hide alerts after 5 seconds
            // setTimeout(function() {
            //     $('.alert').fadeOut('slow');
            // }, 5000);
        });
    </script>
    
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const delay = 800;

    @if(session('success'))
        setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: @json(session('success')),
            position: 'center',
            showConfirmButton: true,
            timer: 5000,
            timerProgressBar: true,
            showClass: { popup: 'swal2-show' },
            hideClass: { popup: 'swal2-hide' }
        });
        }, delay);
    @endif

    @if(session('error'))
        setTimeout(() => {
        Swal.fire({
            icon: 'error',
            title: @json(session('error')),
            position: 'center',
            showConfirmButton: true,
            confirmButtonText: 'OK'
        });
        }, delay);
    @endif
    });
    </script>




</body>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</html>