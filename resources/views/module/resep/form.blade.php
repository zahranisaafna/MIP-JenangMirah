@extends('layouts.master')
@section('title', !empty($resep->id_resep) ? 'Edit Komposisi Produk' : 'Tambah Komposisi Produk')
@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ !empty($resep->id_resep) ? 'Edit' : 'Tambah' }} Komposisi Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('resep.index') }}">Komposisi Produk</a></li>
                        <li class="breadcrumb-item active">{{ !empty($resep->id_resep) ? 'Edit' : 'Tambah' }}</li>
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
                $isEdit = isset($resep) && !empty($resep->id_resep);
                $formAction = $isEdit 
                    ? route('resep.update', $resep->id_resep) 
                    : route('resep.store');
            @endphp

            <form action="{{ $formAction }}" method="POST" id="formResep">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- Card Info Resep -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Masukkan data terkait komposisi produk untuk satu batch produksi.</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="nama_resep">Nama Resep <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_resep') is-invalid @enderror" 
                                           id="nama_resep" 
                                           name="nama_resep" 
                                           value="{{ old('nama_resep', $resep->nama_resep ?? '') }}" 
                                           maxlength="20"
                                           placeholder="Contoh: Roti Tawar"
                                           required>
                                    @error('nama_resep')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="waktu_produksi">Waktu Produksi (menit) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('waktu_produksi') is-invalid @enderror" 
                                           id="waktu_produksi" 
                                           name="waktu_produksi" 
                                           value="{{ old('waktu_produksi', $resep->waktu_produksi ?? '') }}" 
                                           min="1"
                                           placeholder="60"
                                           required>
                                    @error('waktu_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="kapasitas_produksi">Kapasitas Produksi <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('kapasitas_produksi') is-invalid @enderror" 
                                           id="kapasitas_produksi" 
                                           name="kapasitas_produksi" 
                                           value="{{ old('kapasitas_produksi', $resep->kapasitas_produksi ?? '') }}" 
                                           min="1"
                                           placeholder="10"
                                           required>
                                    @error('kapasitas_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="satuan_output">Satuan Output <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('satuan_output') is-invalid @enderror" 
                                           id="satuan_output" 
                                           name="satuan_output" 
                                           value="{{ old('satuan_output', $resep->satuan_output ?? '') }}" 
                                           maxlength="3"
                                           placeholder=" Contoh: pcs"
                                           required>
                                    @error('satuan_output')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    {{-- <small class="form-text text-muted">Contoh: pcs, kg, ltr, box</small> --}}
                                </div>
                            </div>
                            <div class="col-md-1"> 
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="aktif" {{ old('status', $resep->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="non_aktif" {{ old('status', $resep->status ?? '') == 'non_aktif' ? 'selected' : '' }}>Non Aktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="catatan">Catatan</label>
                                    <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                              id="catatan" 
                                              name="catatan" 
                                              rows="1"
                                              placeholder="Contoh: Roti Tawar Kemasan Besar (opsional)">{{ old('catatan', $resep->catatan ?? '') }}</textarea>
                                    @error('catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Komposisi -->
                @if(!$isEdit)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Isi bahan baku beserta jumlah yang dibutuhkan untuk komposisi produk</h5>
                            <button type="button" class="btn btn-success btn-sm" id="btnTambahKomposisi">
                                <i class="fas fa-plus"></i> Tambah Bahan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableKomposisi">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 6%">Bahan Baku</th>
                                        <th class="text-center" style="width: 4%">Jumlah Diperlukan</th>
                                        <th class="text-center" style="width: 4%">Satuan</th>
                                        <th class="text-center" style="width: 35%">Keterangan</th>
                                        <th class="text-center" style="width: 2%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="komposisiContainer">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Memperbarui isi bahan baku beserta jumlah yang dibutuhkan untuk komposisi produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableKomposisi">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 6%">Bahan Baku</th>
                                        <th class="text-center" style="width: 4%">Jumlah Diperlukan</th>
                                        <th class="text-center" style="width: 4%">Satuan</th>
                                        <th class="text-center" style="width: 35%">Keterangan</th>
                                        <th class="text-center" style="width: 2%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="komposisiEditContainer">
                                    @foreach($komposisiReseps as $komposisi)
                                    <tr data-id="{{ $komposisi->id_komposisi }}">
                                        <td>{{ $komposisi->nama_bahan ?? '-' }}</td>
                                        <td>
                                            <input type="hidden" name="komposisi[{{ $loop->index }}][id_komposisi]" value="{{ $komposisi->id_komposisi }}">
                                            <input type="number" name="komposisi[{{ $loop->index }}][jumlah_diperlukan]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $komposisi->jumlah_diperlukan }}" 
                                                   step="0.1" min="0.1" required>
                                        </td>
                                        <td>
                                            <select name="komposisi[{{ $loop->index }}][satuan]" 
                                                    class="form-control form-control-sm" required>
                                                <option value="">-- Pilih Satuan --</option>
                                                <option value="kg"    {{ $komposisi->satuan == 'kg' ? 'selected' : '' }}>kg</option>
                                                <option value="biji"  {{ $komposisi->satuan == 'biji' ? 'selected' : '' }}>biji</option>
                                                <option value="liter" {{ $komposisi->satuan == 'liter' ? 'selected' : '' }}>liter</option>
                                                <option value="pcs"   {{ $komposisi->satuan == 'pcs' ? 'selected' : '' }}>pcs</option>
                                                <option value="ons"   {{ $komposisi->satuan == 'ons' ? 'selected' : '' }}>ons</option>

                                            </select>
                                        </td>

                                        <td>
                                            <input type="text" name="komposisi[{{ $loop->index }}][keterangan]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $komposisi->keterangan ?? '' }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm btnHapusKomposisi" 
                                                    data-id="{{ $komposisi->id_komposisi }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
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
                        <a href="{{ route('resep.index') }}" class="btn btn-secondary">
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
const isEdit = {{ $isEdit ? 'true' : 'false' }};

$(document).ready(function() {
    @if(!$isEdit)
    tambahRow();
    @endif
    
    $('#btnTambahKomposisi').click(function() {
        tambahRow();
    });
    
    $(document).on('click', '.btnHapusRow', function() {
        $(this).closest('tr').remove();
    });

    // Edit mode handlers
    $(document).on('click', '.btnHapusKomposisi', function() {
        if (!confirm('Yakin ingin menghapus komposisi ini?')) {
            return;
        }

        const btn = $(this);
        const id = btn.data('id');
        const row = btn.closest('tr');

        $.ajax({
            url: '/resep/komposisi/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    row.remove();
                    alert('Komposisi berhasil dihapus');
                } else {
                    alert('Gagal menghapus komposisi: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
            }
        });
    });
    
    $('#formResep').submit(function(e) {
        @if(!$isEdit)
        const rowCount = $('#komposisiContainer tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 komposisi bahan baku!');
            return false;
        }
        @else
        const rowCount = $('#komposisiEditContainer tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Tidak boleh menghapus semua komposisi!');
            return false;
        }

        // Pastikan semua input komposisi terisi
        let isValid = true;
        $('#komposisiEditContainer tr').each(function() {
            const jumlah = $(this).find('input[name*="[jumlah_diperlukan]"]').val();
            const satuan = $(this).find('select[name*="[satuan]"]').val();
            
            if (!jumlah || parseFloat(jumlah) <= 0 || !satuan) {
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Pastikan semua jumlah dan satuan terisi dengan benar!');
            return false;
        }
        @endif
        
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    });
});

function tambahRow() {
    rowIndex++;
    
    let optionsBahan = '<option value="">-- Pilih Bahan Baku --</option>';
    bahanBaku.forEach(bahan => {
        optionsBahan += `<option value="${bahan.id_bahan_baku}">${bahan.nama_bahan} (Stok: ${bahan.stok_saat_ini} ${bahan.satuan})</option>`;
    });
    
    const row = `
        <tr>
            <td>
                <select name="komposisi[${rowIndex}][id_bahan_baku]" class="form-control form-control-sm" required>
                    ${optionsBahan}
                </select>
            </td>
            <td>
                <input type="number" name="komposisi[${rowIndex}][jumlah_diperlukan]" 
                       class="form-control form-control-sm" 
                       step="0.1" min="0.1" placeholder="0" required>
            </td>
            <td>
                <select name="komposisi[${rowIndex}][satuan]" 
                        class="form-control form-control-sm" required>
                    <option value="">-- Pilih Satuan --</option>
                    <option value="kg">kg</option>
                    <option value="biji">biji</option>
                    <option value="liter">liter</option>
                    <option value="pcs">pcs</option>
                    <option value="ons">ons</option>
                </select>
            </td>
            <td>
                <input type="text" name="komposisi[${rowIndex}][keterangan]" 
                       class="form-control form-control-sm" 
                       placeholder="Contoh: beras ketan kualitas premium (opsional)">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btnHapusRow" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#komposisiContainer').append(row);
}
</script>
@endpush

@endsection