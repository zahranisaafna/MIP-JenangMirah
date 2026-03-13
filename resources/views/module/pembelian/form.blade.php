@extends('layouts.master')
@section('title', !empty($pembelian->id_pembelian) ? 'Edit Pembelian' : 'Create Pembelian')
@section('content')
<style>
    .flatpickr-input[readonly] {
        background-color: white !important;
        color: inherit !important;
        cursor: pointer !important;
    }
</style>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ !empty($pembelian->id_pembelian) ? 'Edit' : 'Tambah' }} Pembelian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li>
                        <li class="breadcrumb-item active">{{ !empty($pembelian->id_pembelian) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @php
                $isEdit = isset($pembelian) && !empty($pembelian->id_pembelian);
                $formAction = $isEdit 
                    ? route('pembelian.update', $pembelian->id_pembelian) 
                    : route('pembelian.store');
            @endphp

            <form action="{{ $formAction }}" method="POST" id="formPembelian">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- Card Info Pembelian -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Isi data pembelian bahan baku beserta detail jumlah dan pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tanggal_pembelian">Tanggal Pembelian <span class="text-danger">*</span></label>
                                    <input type="text"
                                    class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                                    id="tanggal_pembelian"
                                    name="tanggal_pembelian"
                                    value="{{ old('tanggal_pembelian', !empty($pembelian->tanggal_pembelian) ? date('Y-m-d', strtotime($pembelian->tanggal_pembelian)) : date('Y-m-d')) }}"
                                    required>
                                    @error('tanggal_pembelian')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="metode_pembayaran">Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select class="form-control @error('metode_pembayaran') is-invalid @enderror" 
                                            id="metode_pembayaran" 
                                            name="metode_pembayaran" 
                                            {{ $isEdit ? 'disabled' : 'required' }}>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="cash" {{ old('metode_pembayaran', $pembelian->metode_pembayaran ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="transfer" {{ old('metode_pembayaran', $pembelian->metode_pembayaran ?? '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    </select>
                                    @error('metode_pembayaran')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status_pembayaran">Status Pembayaran <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status_pembayaran') is-invalid @enderror" 
                                            id="status_pembayaran" 
                                            name="status_pembayaran" 
                                            required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="lunas" {{ old('status_pembayaran', $pembelian->status_pembayaran ?? '') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="belum_lunas" {{ old('status_pembayaran', $pembelian->status_pembayaran ?? '') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                    </select>
                                    @error('status_pembayaran')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tanggal_jatuh_tempo">Tanggal Jatuh Tempo</label>
                                    <input type="text" 
                                        class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                                        id="tanggal_jatuh_tempo" 
                                        name="tanggal_jatuh_tempo" 
                                        value="{{ old('tanggal_jatuh_tempo', !empty($pembelian->tanggal_jatuh_tempo) ? date('Y-m-d', strtotime($pembelian->tanggal_jatuh_tempo)) : '') }}">
                                    @error('tanggal_jatuh_tempo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika lunas</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                            id="keterangan"
                                            name="keterangan"
                                            rows="2"
                                            placeholder="Contoh: Bahan dibeli offline di toko (opsional)">{{ old('keterangan', $pembelian->keterangan ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                            id="keterangan" 
                                            name="keterangan" 
                                            rows="2"
                                            placeholder="Contoh: Bahan dibeli offline di toko (opsional)">{{ old('keterangan', $pembelian->keterangan ?? '') }}</textarea>
                                    @error('keterangan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}

                    </div>
                </div>

                @if(!$isEdit)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Detail Pembelian Bahan Baku</h5>
                            <button type="button" class="btn btn-success btn-sm" id="btnTambahDetail">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableDetail">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 20%">Bahan Baku</th>
                                        <th style="width: 20%">Supplier</th>
                                        <th style="width: 10%">Jumlah</th>
                                        <th style="width: 15%">Harga Satuan</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th style="width: 12%">Tgl Diterima</th>
                                        <th style="width: 10%">Kondisi</th>
                                        <th style="width: 5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detailContainer">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Pembelian(Rp):</strong></td>
                                        <td colspan="4">
                                            <strong id="totalPembelian">0</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Memperbarui detail pembelian bahan baku beserta informasi terkait</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableDetail">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 15%">Bahan Baku</th>
                                        <th style="width: 15%">Supplier</th>
                                        <th style="width: 12%">Jumlah</th>
                                        <th style="width: 12%">Harga Satuan</th>
                                        <th style="width: 12%">Subtotal</th>
                                        <th style="width: 12%">Tgl Diterima</th>
                                        <th style="width: 10%">Kondisi</th>
                                        <th style="width: 12%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detailEditContainer">
                                    @php $totalEdit = 0; @endphp
                                    @foreach($detailPembelian as $detail)
                                    @php $totalEdit += $detail->subtotal; @endphp
                                    <tr data-id="{{ $detail->id_detail_pembelian }}">
                                        <td>{{ $detail->nama_bahan ?? '-' }}</td>
                                        <td>{{ $detail->nama_supplier ?? '-' }}</td>
                                        <td>
                                            <input type="hidden" name="detail[{{ $loop->index }}][id_detail_pembelian]" value="{{ $detail->id_detail_pembelian }}">
                                            <input type="number" name="detail[{{ $loop->index }}][jumlah]" 
                                                   class="form-control form-control-sm inputJumlahEdit" 
                                                   value="{{ $detail->jumlah }}" 
                                                   step="1" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="detail[{{ $loop->index }}][harga_satuan]" 
                                                   class="form-control form-control-sm inputHargaEdit" 
                                                   value="{{ $detail->harga_satuan }}" 
                                                   step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm subtotalEdit" 
                                                   readonly value="Rp {{ number_format($detail->subtotal, 0, ',', '.') }}"
                                                   data-value="{{ $detail->subtotal }}">
                                        </td>
                                        <td>
                                            <input type="text" name="detail[{{ $loop->index }}][tanggal_diterima]" 
                                                class="form-control form-control-sm tanggal_diterima_input" 
                                                value="{{ date('Y-m-d', strtotime($detail->tanggal_diterima)) }}" required>
                                        </td>
                                        <td>
                                            <select name="detail[{{ $loop->index }}][kondisi]" class="form-control form-control-sm" required>
                                                <option value="baik" {{ $detail->kondisi == 'baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="rusak" {{ $detail->kondisi == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                                <option value="kadaluarsa" {{ $detail->kondisi == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btnHapusDetail" 
                                                    data-id="{{ $detail->id_detail_pembelian }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Pembelian(Rp):</strong></td>
                                        <td colspan="4">
                                            <strong id="totalPembelianEdit">{{ number_format($totalEdit, 0, ',', '.') }},-</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>          
                @endif

                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
{{-- flatpickr --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tanggal Pembelian
    flatpickr("#tanggal_pembelian", {
        dateFormat: "Y-m-d",   // nilai ke server
        altInput: true,
        altFormat: "d-M-Y",    // tampilan: 12-Nov-2025
        allowInput: false,
    });

    // Tanggal Jatuh Tempo
    flatpickr("#tanggal_jatuh_tempo", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-M-Y",
        allowInput: true,
    });

    // Semua Tanggal Diterima yang sudah ada (mode edit)
    flatpickr(".tanggal_diterima_input", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-M-Y",
        allowInput: true,
    });
});
</script>

<script>
let rowIndex = 0;
const bahanBaku = @json($bahanBaku);
const suppliers = @json($suppliers);
const isEdit = {{ $isEdit ? 'true' : 'false' }};

$(document).ready(function() {
    @if(!$isEdit)
    tambahRow();
    @endif
    
    $('#btnTambahDetail').click(function() {
        tambahRow();
    });
    
    $(document).on('click', '.btnHapusRow', function() {
        $(this).closest('tr').remove();
        hitungTotal();
    });
    
    $(document).on('input', '.inputJumlah, .inputHarga', function() {
        hitungSubtotal($(this).closest('tr'));
        hitungTotal();
    });

    // Edit mode handlers
    $(document).on('input', '.inputJumlahEdit, .inputHargaEdit', function() {
        hitungSubtotalEdit($(this).closest('tr'));
        hitungTotalEdit();
    });

    $(document).on('click', '.btnHapusDetail', function() {
        if (!confirm('Yakin ingin menghapus detail ini? Stok akan dikembalikan.')) {
            return;
        }

        const btn = $(this);
        const id = btn.data('id');
        const row = btn.closest('tr');

        $.ajax({
            url: '/pembelian/detail/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    row.remove();
                    hitungTotalEdit();
                    alert('Detail berhasil dihapus');
                } else {
                    alert('Gagal menghapus detail: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
            }
        });
    });
    
    $('#formPembelian').submit(function(e) {
        @if(!$isEdit)
        const rowCount = $('#detailContainer tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 item pembelian!');
            return false;
        }
        @else
        const rowCount = $('#detailEditContainer tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Tidak boleh menghapus semua detail pembelian!');
            return false;
        }

        // Pastikan semua input detail terisi
        let isValid = true;
        $('#detailEditContainer tr').each(function() {
            const jumlah = $(this).find('.inputJumlahEdit').val();
            const harga = $(this).find('.inputHargaEdit').val();
            
            if (!jumlah || parseFloat(jumlah) <= 0 || !harga || parseFloat(harga) < 0) {
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Pastikan semua jumlah dan harga terisi dengan benar!');
            return false;
        }
        @endif
        
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    });
});

function tambahRow() {
    rowIndex++;
    
    let optionsBahan = '<option value="">-- Pilih Bahan --</option>';
    bahanBaku.forEach(bahan => {
        optionsBahan += `<option value="${bahan.id_bahan_baku}">${bahan.nama_bahan} (${bahan.satuan})</option>`;
    });
    
    let optionsSupplier = '<option value="">-- Pilih Supplier --</option>';
    suppliers.forEach(supplier => {
        optionsSupplier += `<option value="${supplier.id_supplier}">${supplier.nama_supplier}</option>`;
    });
    
    const row = `
        <tr>
            <td>
                <select name="detail[${rowIndex}][id_bahan_baku]" class="form-control form-control-sm" required>
                    ${optionsBahan}
                </select>
            </td>
            <td>
                <select name="detail[${rowIndex}][id_supplier]" class="form-control form-control-sm" required>
                    ${optionsSupplier}
                </select>
            </td>
            <td>
                <input type="number" name="detail[${rowIndex}][jumlah]" 
                       class="form-control form-control-sm inputJumlah" 
                       step="1" min="0" placeholder="0" required>
            </td>
            <td>
                <input type="number" name="detail[${rowIndex}][harga_satuan]" 
                       class="form-control form-control-sm inputHarga" 
                       step="0.01" min="0" placeholder="0" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm subtotal" 
                       readonly value="Rp 0">
            </td>
            <td>
                <input type="text" name="detail[${rowIndex}][tanggal_diterima]" 
                    class="form-control form-control-sm tanggal_diterima_input" 
                    value="${new Date().toISOString().split('T')[0]}" required>
            </td>
            <td>
                <select name="detail[${rowIndex}][kondisi]" class="form-control form-control-sm" required>
                    <option value="baik" selected>Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="kadaluarsa">Kadaluarsa</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btnHapusRow" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#detailContainer').append(row);
    // re-init semua input tanggal_diterima (termasuk yang baru)
    flatpickr(".tanggal_diterima_input", {
        dateFormat: "Y-m-d",   // nilai ke server
        altInput: true,
        altFormat: "d-M-Y",    // tampilan: 07-Dec-2025
        allowInput: true,
    });

}

function hitungSubtotal(row) {
    const jumlah = parseFloat(row.find('.inputJumlah').val()) || 0;
    const harga = parseFloat(row.find('.inputHarga').val()) || 0;
    const subtotal = jumlah * harga;
    
    row.find('.subtotal').val('Rp ' + formatRupiah(subtotal));
    row.find('.subtotal').data('value', subtotal);
}

function hitungTotal() {
    let total = 0;
    
    $('.subtotal').each(function() {
        const value = $(this).data('value') || 0;
        total += parseFloat(value);
    });
    
    $('#totalPembelian').text('Rp ' + formatRupiah(total));
}

function hitungSubtotalEdit(row) {
    const jumlah = parseFloat(row.find('.inputJumlahEdit').val()) || 0;
    const harga = parseFloat(row.find('.inputHargaEdit').val()) || 0;
    const subtotal = jumlah * harga;
    
    row.find('.subtotalEdit').val('Rp ' + formatRupiah(subtotal));
    row.find('.subtotalEdit').data('value', subtotal);
}

function hitungTotalEdit() {
    let total = 0;
    
    $('.subtotalEdit').each(function() {
        const value = $(this).data('value') || 0;
        total += parseFloat(value);
    });
    
    $('#totalPembelianEdit').text('Rp ' + formatRupiah(total));
}

function formatRupiah(angka) {
    return angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>
@endpush

@endsection

{{-- @extends('layouts.master')
@section('title', !empty($pembelian->id_pembelian) ? 'Edit Pembelian' : 'Create Pembelian')
@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ !empty($pembelian->id_pembelian) ? 'Edit' : 'Tambah' }} Pembelian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li>
                        <li class="breadcrumb-item active">{{ !empty($pembelian->id_pembelian) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @php
                $isEdit = isset($pembelian) && !empty($pembelian->id_pembelian);
                $formAction = $isEdit 
                    ? route('pembelian.update', $pembelian->id_pembelian) 
                    : route('pembelian.store');
            @endphp

            <form action="{{ $formAction }}" method="POST" id="formPembelian">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- Card Info Pembelian -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Pembelian</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_pembelian">Tanggal Pembelian <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_pembelian') is-invalid @enderror" 
                                           id="tanggal_pembelian" 
                                           name="tanggal_pembelian" 
                                           value="{{ old('tanggal_pembelian', !empty($pembelian->tanggal_pembelian) ? date('Y-m-d', strtotime($pembelian->tanggal_pembelian)) : date('Y-m-d')) }}"
                                           {{ $isEdit ? 'readonly' : 'required' }}>
                                    @error('tanggal_pembelian')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="metode_pembayaran">Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select class="form-control @error('metode_pembayaran') is-invalid @enderror" 
                                            id="metode_pembayaran" 
                                            name="metode_pembayaran" 
                                            {{ $isEdit ? 'disabled' : 'required' }}>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="cash" {{ old('metode_pembayaran', $pembelian->metode_pembayaran ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="transfer" {{ old('metode_pembayaran', $pembelian->metode_pembayaran ?? '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    </select>
                                    @error('metode_pembayaran')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status_pembayaran">Status Pembayaran <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status_pembayaran') is-invalid @enderror" 
                                            id="status_pembayaran" 
                                            name="status_pembayaran" 
                                            required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="lunas" {{ old('status_pembayaran', $pembelian->status_pembayaran ?? '') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="belum_lunas" {{ old('status_pembayaran', $pembelian->status_pembayaran ?? '') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                    </select>
                                    @error('status_pembayaran')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_jatuh_tempo">Tanggal Jatuh Tempo</label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                                           id="tanggal_jatuh_tempo" 
                                           name="tanggal_jatuh_tempo" 
                                           value="{{ old('tanggal_jatuh_tempo', !empty($pembelian->tanggal_jatuh_tempo) ? date('Y-m-d', strtotime($pembelian->tanggal_jatuh_tempo)) : '') }}">
                                    @error('tanggal_jatuh_tempo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika lunas</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="2"
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $pembelian->keterangan ?? '') }}</textarea>
                            @error('keterangan')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if(!$isEdit)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Detail Pembelian</h3>
                            <button type="button" class="btn btn-success btn-sm" id="btnTambahDetail">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableDetail">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 20%">Bahan Baku</th>
                                        <th style="width: 20%">Supplier</th>
                                        <th style="width: 10%">Jumlah</th>
                                        <th style="width: 15%">Harga Satuan</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th style="width: 12%">Tgl Diterima</th>
                                        <th style="width: 10%">Kondisi</th>
                                        <th style="width: 5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detailContainer">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Pembelian:</strong></td>
                                        <td colspan="4">
                                            <strong id="totalPembelian">Rp 0</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Pembelian (Edit)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableDetail">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Bahan Baku</th>
                                        <th>Supplier</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Subtotal</th>
                                        <th>Tgl Diterima</th>
                                        <th>Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalEdit = 0; @endphp
                                    @foreach($detailPembelian as $detail)
                                    @php $totalEdit += $detail->subtotal; @endphp
                                    <tr>
                                        <td>{{ $detail->nama_bahan ?? '-' }}</td>
                                        <td>{{ $detail->nama_supplier ?? '-' }}</td>
                                        <td>{{ number_format($detail->jumlah, 2) }} {{ $detail->satuan ?? '' }}</td>
                                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        <td>{{ date('d/m/Y', strtotime($detail->tanggal_diterima)) }}</td>
                                        <td>
                                            @if($detail->kondisi == 'baik')
                                                <span class="badge badge-success">Baik</span>
                                            @elseif($detail->kondisi == 'rusak')
                                                <span class="badge badge-danger">Rusak</span>
                                            @else
                                                <span class="badge badge-warning">Kadaluarsa</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Pembelian:</strong></td>
                                        <td colspan="3">
                                            <strong id="totalPembelian">Rp {{ number_format($totalEdit, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>          
                @endif

                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
let rowIndex = 0;
const bahanBaku = @json($bahanBaku);
const suppliers = @json($suppliers);

$(document).ready(function() {
    @if(!$isEdit)
    tambahRow();
    @endif
    
    $('#btnTambahDetail').click(function() {
        tambahRow();
    });
    
    $(document).on('click', '.btnHapusRow', function() {
        $(this).closest('tr').remove();
        hitungTotal();
    });
    
    $(document).on('input', '.inputJumlah, .inputHarga', function() {
        hitungSubtotal($(this).closest('tr'));
        hitungTotal();
    });
    
    $('#formPembelian').submit(function(e) {
        @if(!$isEdit)
        const rowCount = $('#detailContainer tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 item pembelian!');
            return false;
        }
        @endif
        
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    });
});

function tambahRow() {
    rowIndex++;
    
    let optionsBahan = '<option value="">-- Pilih Bahan --</option>';
    bahanBaku.forEach(bahan => {
        optionsBahan += `<option value="${bahan.id_bahan_baku}">${bahan.nama_bahan} (${bahan.satuan})</option>`;
    });
    
    let optionsSupplier = '<option value="">-- Pilih Supplier --</option>';
    suppliers.forEach(supplier => {
        optionsSupplier += `<option value="${supplier.id_supplier}">${supplier.nama_supplier}</option>`;
    });
    
    const row = `
        <tr>
            <td>
                <select name="detail[${rowIndex}][id_bahan_baku]" class="form-control form-control-sm" required>
                    ${optionsBahan}
                </select>
            </td>
            <td>
                <select name="detail[${rowIndex}][id_supplier]" class="form-control form-control-sm" required>
                    ${optionsSupplier}
                </select>
            </td>
            <td>
                <input type="number" name="detail[${rowIndex}][jumlah]" 
                       class="form-control form-control-sm inputJumlah" 
                       step="0.01" min="0.01" placeholder="0" required>
            </td>
            <td>
                <input type="number" name="detail[${rowIndex}][harga_satuan]" 
                       class="form-control form-control-sm inputHarga" 
                       step="0.01" min="0" placeholder="0" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm subtotal" 
                       readonly value="Rp 0">
            </td>
            <td>
                <input type="date" name="detail[${rowIndex}][tanggal_diterima]" 
                       class="form-control form-control-sm" 
                       value="${new Date().toISOString().split('T')[0]}" required>
            </td>
            <td>
                <select name="detail[${rowIndex}][kondisi]" class="form-control form-control-sm" required>
                    <option value="baik" selected>Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="kadaluarsa">Kadaluarsa</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btnHapusRow" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#detailContainer').append(row);
}

function hitungSubtotal(row) {
    const jumlah = parseFloat(row.find('.inputJumlah').val()) || 0;
    const harga = parseFloat(row.find('.inputHarga').val()) || 0;
    const subtotal = jumlah * harga;
    
    row.find('.subtotal').val('Rp ' + formatRupiah(subtotal));
    row.find('.subtotal').data('value', subtotal);
}

function hitungTotal() {
    let total = 0;
    
    $('.subtotal').each(function() {
        const value = $(this).data('value') || 0;
        total += parseFloat(value);
    });
    
    $('#totalPembelian').text('Rp ' + formatRupiah(total));
}

function formatRupiah(angka) {
    return angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>
@endpush

@endsection --}}