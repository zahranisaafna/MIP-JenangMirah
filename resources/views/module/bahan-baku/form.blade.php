@extends('layouts.master')
@section('title', !empty($bahanBaku->id_bahan_baku) ? 'Edit Bahan Baku' : 'Tambah Bahan Baku')
@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ !empty($bahanBaku->id_bahan_baku) ? 'Edit' : 'Tambah' }} Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-baku.index') }}">Bahan Baku</a></li>
                        <li class="breadcrumb-item active">{{ !empty($bahanBaku->id_bahan_baku) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form untuk menambahkan data bahan baku yang akan digunakan dalam produksi</h5>
                </div>
                
                {{-- DEBUG: Hapus setelah berhasil --}}
                {{-- <div class="alert alert-info m-3">
                    <strong>Debug Info:</strong><br>
                    Is Edit: {{ !empty($bahanBaku->id_bahan_baku) ? 'YES' : 'NO' }}<br>
                    ID: {{ $bahanBaku->id_bahan_baku ?? 'NULL' }}<br>
                    Action: {{ !empty($bahanBaku->id_bahan_baku) ? route('bahan-baku.update', $bahanBaku->id_bahan_baku) : route('bahan-baku.store') }}
                </div> --}}

                @php
                    $isEdit = !empty($bahanBaku->id_bahan_baku);
                    $formAction = $isEdit 
                        ? route('bahan-baku.update', $bahanBaku->id_bahan_baku) 
                        : route('bahan-baku.store');
                @endphp
                
                <form action="{{ $formAction }}" method="POST" id="formBahanBaku">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="card-body">
                        <div class="row">
                            <!-- Nama Bahan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_bahan">Nama Bahan <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_bahan') is-invalid @enderror" 
                                           id="nama_bahan" 
                                           name="nama_bahan" 
                                           value="{{ old('nama_bahan', $bahanBaku->nama_bahan ?? '') }}"
                                           placeholder="Contoh: Gula Pasir"
                                           maxlength="20"
                                           required>
                                    @error('nama_bahan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kategori">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control @error('kategori') is-invalid @enderror" 
                                            id="kategori" 
                                            name="kategori" 
                                            required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Bahan Utama" {{ old('kategori', $bahanBaku->kategori ?? '') == 'Bahan Utama' ? 'selected' : '' }}>Bahan Utama</option>
                                        <option value="Pemanis" {{ old('kategori', $bahanBaku->kategori ?? '') == 'Pemanis' ? 'selected' : '' }}>Pemanis</option>
                                        <option value="Bahan Tambahan" {{ old('kategori', $bahanBaku->kategori ?? '') == 'Bahan Tambahan' ? 'selected' : '' }}>Bahan Tambahan</option>
                                        <option value="Bahan Pendukung" {{ old('kategori', $bahanBaku->kategori ?? '') == 'Bahan Pendukung' ? 'selected' : '' }}>Bahan Pendukung</option>
                                        <option value="Bumbu" {{ old('kategori', $bahanBaku->kategori ?? '') == 'Bumbu' ? 'selected' : '' }}>Bumbu</option>
                                        <option value="Pewarna" {{ old('kategori', $bahanBaku->kategori ?? '') == 'Pewarna' ? 'selected' : '' }}>Pewarna</option>
                                        <option value="Lainnya" {{ old('kategori', $bahanBaku->kategori ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('kategori')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Satuan -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="satuan">Satuan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('satuan') is-invalid @enderror" 
                                            id="satuan" 
                                            name="satuan" 
                                            required>
                                        <option value="">-- Pilih Satuan --</option>
                                        {{-- <option value="sak" {{ old('satuan', $bahanBaku->satuan ?? '') == 'sak' ? 'selected' : '' }}>sak</option> --}}
                                        {{-- <option value="pail" {{ old('satuan', $bahanBaku->satuan ?? '') == 'pail' ? 'selected' : '' }}>pail</option> --}}
                                        <option value="kg" {{ old('satuan', $bahanBaku->satuan ?? '') == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="butir" {{ old('satuan', $bahanBaku->satuan ?? '') == 'butir' ? 'selected' : '' }}>butir</option>
                                        <option value="biji" {{ old('satuan', $bahanBaku->satuan ?? '') == 'biji' ? 'selected' : '' }}>biji</option>
                                        <option value="liter" {{ old('satuan', $bahanBaku->satuan ?? '') == 'liter' ? 'selected' : '' }}>liter</option>
                                        {{-- <option value="jirigen" {{ old('satuan', $bahanBaku->satuan ?? '') == 'jirigen' ? 'selected' : '' }}>jirigen</option> --}}
                                        <option value="pcs" {{ old('satuan', $bahanBaku->satuan ?? '') == 'pcs' ? 'selected' : '' }}>pcs</option>
                                        <option value="ons" {{ old('satuan', $bahanBaku->satuan ?? '') == 'ons' ? 'selected' : '' }}>ons</option>
                                        {{-- <option value="karton" {{ old('satuan', $bahanBaku->satuan ?? '') == 'karton' ? 'selected' : '' }}>karton</option> --}}
                                    </select>
                                    @error('satuan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Stok Minimum -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stok_minimum">Stok Minimum <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('stok_minimum') is-invalid @enderror" 
                                           id="stok_minimum" 
                                           name="stok_minimum" 
                                           value="{{ old('stok_minimum', $bahanBaku->stok_minimum ?? '') }}"
                                           {{-- step="0.01"
                                           min="0"
                                           placeholder="0.00" --}}
                                           step="1"
                                           min="0"
                                           placeholder="0"
                                           required>
                                    @error('stok_minimum')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Stok Saat Ini -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stok_saat_ini">Stok Saat Ini <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('stok_saat_ini') is-invalid @enderror" 
                                           id="stok_saat_ini" 
                                           name="stok_saat_ini" 
                                           value="{{ old('stok_saat_ini', $bahanBaku->stok_saat_ini ?? '') }}"
                                           {{-- step="0.01"
                                           min="0"
                                           placeholder="0.00" --}}
                                           step="1"
                                           min="0"
                                           placeholder="0"                                           
                                           required>
                                    @error('stok_saat_ini')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Harga Rata-rata -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="harga_rata_rata">Harga Rata-rata (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('harga_rata_rata') is-invalid @enderror" 
                                           id="harga_rata_rata" 
                                           name="harga_rata_rata" 
                                           value="{{ old('harga_rata_rata', $bahanBaku->harga_rata_rata ?? '') }}"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00"
                                           required>
                                    @error('harga_rata_rata')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tanggal Kadaluarsa -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_kadaluarsa">Tanggal Kadaluarsa</label>
                                    <input type="text" 
                                        class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror" 
                                        id="tanggal_kadaluarsa" 
                                        name="tanggal_kadaluarsa" 
                                        value="{{ old('tanggal_kadaluarsa', !empty($bahanBaku->tanggal_kadaluarsa) ? date('Y-m-d', strtotime($bahanBaku->tanggal_kadaluarsa)) : '') }}">
                                    @error('tanggal_kadaluarsa')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika tidak ada tanggal kadaluarsa</small>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="3"
                                      placeholder="Contoh: Gula Pasir merk Gulaku (opsional)">{{ old('deskripsi', $bahanBaku->deskripsi ?? '') }}</textarea>
                            @error('deskripsi')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#tanggal_kadaluarsa", {
        dateFormat: "Y-m-d",  // nilai yg dikirim ke server
        altInput: true,       // tampilkan input cantik
        altFormat: "d-M-Y",   // yg kelihatan: 15-Nov-2025
        allowInput: true
        // defaultDate tidak wajib, flatpickr otomatis baca dari value=""
    });
});
document.getElementById('formBahanBaku').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;

    try {
        const res = await fetch('{{ route("bahan-baku.check-duplicate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nama_bahan: document.getElementById('nama_bahan').value,
                exclude_id: '{{ $isEdit ? $bahanBaku->id_bahan_baku : "" }}'
            })
        });

        const data = await res.json();

        if (data.duplicates.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Sudah Terdaftar!',
                html: 'Field berikut sudah digunakan:<br><br><b>' + data.duplicates.join(', ') + '</b><br><br>Silakan gunakan data yang berbeda.',
                confirmButtonText: 'Oke, Perbaiki'
            });
            return;
        }

        form.submit();

    } catch (err) {
        form.submit();
    }
});
</script>
@endpush

@endsection