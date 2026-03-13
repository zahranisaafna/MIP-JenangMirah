@extends('layouts.master')
<style>
    .flatpickr-input[readonly] {
        background-color: white !important;
        color: inherit !important;
        cursor: pointer !important;
    }
</style>

@section('title', 'Beranda Admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Beranda Admin</h1>
            {{-- <p class="text-gray-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}</p> --}}
        </div>
        
        <!-- Filter Controls -->
        <div class="flex flex-wrap gap-3 items-center">
            <div class="w-48">
                <input type="text" id="startDate" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    value="{{ $startDate }}">
            </div>

            <div class="w-48">
                <input type="text" id="endDate" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    value="{{ $endDate }}">
            </div>

            <div class="w-48">
                <select id="periodeFilter" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>

            <button type="button" id="filterBtn" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </div>

        {{-- <div class="flex flex-wrap gap-3">
            <input type="date" id="startDate" 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   value="{{ $startDate }}">
            <input type="date" id="endDate" 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   value="{{ $endDate }}">
            <select id="periodeFilter" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            </select>
            <button type="button" id="filterBtn" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </div> --}}
        {{-- @if($startDateDisplay && $endDateDisplay)
            <p class="text-sm text-gray-500">
                Periode: <span class="font-semibold">{{ $startDateDisplay }}</span>
                s/d 
                <span class="font-semibold">{{ $endDateDisplay }}</span>
            </p>
        @endif --}}
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Produksi -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Produksi</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($statistik['total_produksi']) }}</h3>
                    <p class="text-sm text-green-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i>
                        <span>Unit</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-industry text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Distribusi -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Distribusi</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($statistik['total_distribusi']) }}</h3>
                    <p class="text-sm text-green-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i>
                        <span>Transaksi</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Produksi Bulan Ini -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Produksi Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($statistik['produksi_bulan_ini']) }}</h3>
                    <p class="text-sm text-blue-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-calendar"></i>
                        <span>Unit</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Distribusi Bulan Ini -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Distribusi Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($statistik['distribusi_bulan_ini']) }}</h3>
                    <p class="text-sm text-orange-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Transaksi</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Grafik Produksi -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Grafik Produksi</h3>
                    {{-- <p class="text-sm text-gray-500 mt-1">Tren produksi periode terpilih</p> --}}
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        {{-- <i class="fas fa-ellipsis-h"></i> --}}
                    </button>
                </div>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="produksiChart"></canvas>
            </div>
        </div>

        <!-- Grafik Distribusi -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Grafik Distribusi</h3>
                    {{-- <p class="text-sm text-gray-500 mt-1">Tren distribusi periode terpilih</p> --}}
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        {{-- <i class="fas fa-ellipsis-h"></i> --}}
                    </button>
                </div>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="distribusiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Statistik Data Keseluruhan Distribusi dan Produksi</h3>
                {{-- <p class="text-sm text-gray-500 mt-1">Ringkasan data keseluruhan</p> --}}
            </div>
            <div class="flex gap-3 text-sm">
                <h6 class="px-4 py-2 text-blue-600 bg-blue-50 rounded-lg font-medium">Overview</h6>
                {{-- <button class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors">Produksi</button>
                <button class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors">Distribusi</button> --}}
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-sm text-gray-500 mb-2">Total Produk</p>
                <h4 class="text-2xl font-bold text-gray-900">{{ number_format($statistik['total_produk']) }}</h4>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-sm text-gray-500 mb-2">Total Lokasi</p>
                <h4 class="text-2xl font-bold text-gray-900">{{ number_format($statistik['total_lokasi']) }}</h4>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-sm text-gray-500 mb-2">Rata-rata Produksi</p>
                <h4 class="text-2xl font-bold text-gray-900">
                    {{ $statistik['total_produksi'] > 0 ? number_format($statistik['produksi_bulan_ini'] / date('d')) : 0 }}
                </h4>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-sm text-gray-500 mb-2">Rata-rata Distribusi</p>
                <h4 class="text-2xl font-bold text-gray-900">
                    {{ $statistik['total_distribusi'] > 0 ? number_format($statistik['distribusi_bulan_ini'] / date('d')) : 0 }}
                </h4>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let produksiChart, distribusiChart;
    
    const produksiData = {
        labels: {!! json_encode($produksiData['labels']) !!},
        values: {!! json_encode($produksiData['values']) !!}
    };
    
    const distribusiData = {
        labels: {!! json_encode($distribusiData['labels']) !!},
        values: {!! json_encode($distribusiData['values']) !!}
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#startDate", {
        dateFormat: "Y-m-d",   // nilai yang dikirim ke server
        altInput: true,        // tampilkan input cantik
        altFormat: "d-M-Y",    // yg kelihatan: 15-Nov-2025
        defaultDate: "{{ $startDate }}",
        allowInput: true,
    });

    flatpickr("#endDate", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-M-Y",
        defaultDate: "{{ $endDate }}",
        allowInput: true,
    });
        initCharts();
        document.getElementById('filterBtn').addEventListener('click', updateCharts);
    });
    
    function initCharts() {
        // Produksi Chart - Area Chart
        const ctxProduksi = document.getElementById('produksiChart').getContext('2d');
        produksiChart = new Chart(ctxProduksi, {
            type: 'bar',
            data: {
                labels: produksiData.labels,
                datasets: [{
                    label: 'Jumlah Produksi',
                    data: produksiData.values,
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderRadius: 8,
                    borderSkipped: false,
                    // backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    // borderColor: 'rgba(59, 130, 246, 1)',
                    // borderWidth: 3,
                    // fill: true,
                    // pointRadius: 5,
                    // pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    // pointBorderColor: '#fff',
                    // pointBorderWidth: 2,
                    // pointHoverRadius: 7,
                    // tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                    display: true,
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12
                    }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Produksi: ' + context.parsed.y.toLocaleString() + ' unit';
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Produksi'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0, // buang desimal
                            callback: function(value) {
                                // integer + pemisah ribuan
                                if (!Number.isInteger(value)) return '';
                                return value.toLocaleString('id-ID');
                            },                            
                            font: { size: 12 },
                            color: '#6b7280'
                        }
                    },
                    x: {
                        title: {
                        display: true,
                        text: 'Periode'
                    },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 12 },
                            color: '#6b7280'
                        }
                    }
                }
            }
        });
        
        // Distribusi Chart - Bar Chart
        const ctxDistribusi = document.getElementById('distribusiChart').getContext('2d');
        distribusiChart = new Chart(ctxDistribusi, {
            type: 'bar',
            data: {
                labels: distribusiData.labels,
                datasets: [{
                    label: 'Jumlah Distribusi Produk',
                    data: distribusiData.values,
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: true,
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Distribusi: ' + context.parsed.y.toLocaleString() + ' transaksi';
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Distribusi Produk'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,                       // naiknya per 1
                            precision: 0,                      // paksa 0 angka di belakang koma
                            callback: function(value) {        // pastikan integer
                                return Number.isInteger(value) ? value : '';
                            },                        
                            font: { size: 12 },
                            color: '#6b7280'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Periode'
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 12 },
                            color: '#6b7280'
                        }
                    }
                }
            }
        });
    }
    
    function updateCharts() {
        const periode = document.getElementById('periodeFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        window.location.href = `{{ route('admin.dashboard') }}?periode=${periode}&start_date=${startDate}&end_date=${endDate}`;
    }
</script>
@endpush
{{-- @extends('layouts.master')

@section('page-title', 'Beranda Admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-fw fa-tachometer-alt"></i> Beranda Admin
        </h1>
        <div class="d-flex gap-2">
            <input type="date" id="startDate" class="form-control form-control-sm" value="{{ $startDate }}">
            <input type="date" id="endDate" class="form-control form-control-sm" value="{{ $endDate }}">
            <select id="periodeFilter" class="form-control form-control-sm">
                <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            </select>
            <button type="button" id="filterBtn" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Produksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistik['total_produksi']) }} Unit
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-industry fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Distribusi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistik['total_distribusi']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Produksi Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistik['produksi_bulan_ini']) }} Unit
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Distribusi Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistik['distribusi_bulan_ini']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shipping-fast fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Produksi</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="produksiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Grafik Distribusi</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="distribusiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .chart-area {
        position: relative;
        height: 300px;
    }
    .gap-2 {
        gap: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    let produksiChart, distribusiChart;
    
    const produksiData = {
        labels: {!! json_encode($produksiData['labels']) !!},
        values: {!! json_encode($produksiData['values']) !!}
    };
    
    const distribusiData = {
        labels: {!! json_encode($distribusiData['labels']) !!},
        values: {!! json_encode($distribusiData['values']) !!}
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        document.getElementById('filterBtn').addEventListener('click', updateCharts);
    });
    
    function initCharts() {
        const ctxProduksi = document.getElementById('produksiChart').getContext('2d');
        produksiChart = new Chart(ctxProduksi, {
            type: 'line',
            data: {
                labels: produksiData.labels,
                datasets: [{
                    label: 'Jumlah Produksi',
                    data: produksiData.values,
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        
        const ctxDistribusi = document.getElementById('distribusiChart').getContext('2d');
        distribusiChart = new Chart(ctxDistribusi, {
            type: 'bar',
            data: {
                labels: distribusiData.labels,
                datasets: [{
                    label: 'Jumlah Distribusi',
                    data: distribusiData.values,
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    
    function updateCharts() {
        const periode = document.getElementById('periodeFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        window.location.href = `{{ route('admin.dashboard') }}?periode=${periode}&start_date=${startDate}&end_date=${endDate}`;
    }
</script> --}}