@extends('layouts.master')
@section('title', isset($distribusi) ? 'Edit Distribusi' : 'Create Distribusi')
{{-- @section('title', !empty($distribusi->id_distribusi) ? 'Edit Distribusi' : 'Create Distribusi') --}}
@section('content')
<style>
    .flatpickr-input[readonly] {
        background-color: #fff !important;
        cursor: pointer !important;
    }
</style>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ !empty($distribusi->id_distribusi) ? 'Edit' : 'Tambah' }} Distribusi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('distribusi.index') }}">Distribusi</a></li>
                        <li class="breadcrumb-item active">{{ !empty($distribusi->id_distribusi) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @php
                $isEdit = isset($distribusi) && !empty($distribusi->id_distribusi);
                $formAction = $isEdit 
                    ? route('distribusi.update', $distribusi->id_distribusi) 
                    : route('distribusi.store');
            @endphp

            <form action="{{ $formAction }}" method="POST" id="formDistribusi">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- Card Info Distribusi -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Informasi distribusi produk yang dikirim ke tujuan tertentu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_distribusi">Tanggal Distribusi <span class="text-danger">*</span></label>
                                    <input type="text"
                                        id="tanggal_distribusi"
                                        name="tanggal_distribusi"
                                        class="form-control"
                                        value="{{ old('tanggal_distribusi', $distribusi->tanggal_distribusi ?? date('Y-m-d')) }}"
                                        required> 
                                        {{-- value="{{ date('Y-m-d', strtotime($distribusi->tanggal_distribusi))}}" --}}
                                        {{-- {{ $distribusi->status === 'selesai' ? 'readonly' : '' }}> --}}
                                    {{-- <input type="text"
                                        class="form-control @error('tanggal_distribusi') is-invalid @enderror"
                                        id="tanggal_distribusi"
                                        name="tanggal_distribusi"
                                        value="{{ old('tanggal_distribusi', !empty($distribusi->tanggal_distribusi) ? date('Y-m-d', strtotime($distribusi->tanggal_distribusi)) : date('Y-m-d')) }}"
                                        {{ $isEdit ? 'readonly' : 'required' }}> --}}
                                    @error('tanggal_distribusi')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenis_distribusi">Jenis Distribusi <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_distribusi') is-invalid @enderror" 
                                            id="jenis_distribusi" 
                                            name="jenis_distribusi" 
                                            {{ $isEdit ? 'disabled' : 'required' }}>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="internal" {{ old('jenis_distribusi', $distribusi->jenis_distribusi ?? '') == 'internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="eksternal" {{ old('jenis_distribusi', $distribusi->jenis_distribusi ?? '') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                                    </select>
                                    @error('jenis_distribusi')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if($isEdit)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    @if($distribusi->status === 'pending')
                                        <select name="status" class="form-control" required>
                                            <option value="pending">Pending</option>
                                            <option value="selesai">Selesai</option>
                                        </select>
                                    @else
                                        <input type="text" class="form-control" value="{{ ucfirst($distribusi->status) }}" readonly>
                                        <input type="hidden" name="status" value="{{ $distribusi->status }}">
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- @if($isEdit)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="pending" {{ old('status', $distribusi->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="selesai" {{ old('status', $distribusi->status ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="batal" {{ old('status', $distribusi->status ?? '') == 'batal' ? 'selected' : '' }}>Batal</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif --}}
                            <div class="col-md-{{ $isEdit ? 4 : 6 }}">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                            id="keterangan" 
                                            name="keterangan" 
                                            rows="2"
                                            placeholder="Isi '-' jika tidak ada keterangan">{{ old('keterangan', $distribusi->keterangan ?? '') }}</textarea>
                                    @error('keterangan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!$isEdit)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Isi Formulir data distribusi terkait lokasi tujuan dan produk yang didistribusikan</h5>
                            <button type="button" class="btn btn-success btn-sm" id="btnTambahDetail">
                                <i class="fas fa-plus"></i> Tambah Lokasi Tujuan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="detailContainer">
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Memperbarui formulir data distribusi terkait lokasi tujuan dan produk yang didistribusikan (Edit)</h5>
                    </div>
                    <div class="card-body">
                        @foreach($distribusi->distribusiDetails as $index => $detail)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5>Tujuan: {{ $detail->lokasi->nama_lokasi ?? '-' }}</h5>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="detail[{{ $index }}][id_distribusi_detail]" value="{{ $detail->id_distribusi_detail }}">
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Status Detail</label>
                                            @if($detail->status_detail === 'pending')
                                                <select name="detail[{{ $index }}][status_detail]"
                                                        class="form-control"
                                                        required>
                                                    <option value="pending" selected>Pending</option>
                                                    <option value="diterima">Diterima</option>
                                                </select>
                                            @else
                                                <input type="text"
                                                    class="form-control"
                                                    value="Diterima"
                                                    readonly>
                                            @endif
                                            {{-- <select name="detail[{{ $index }}][status_detail]" class="form-control" required>
                                                <option value="pending" {{ $detail->status_detail == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="diterima" {{ $detail->status_detail == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                            </select> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nama Penerima</label>
                                            <input type="text" name="detail[{{ $index }}][nama_penerima]" 
                                                   class="form-control" value="{{ $detail->nama_penerima }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <textarea name="detail[{{ $index }}][catatan]" class="form-control" rows="1">{{ $detail->catatan }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <h6>Item Distribusi:</h6>
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Jumlah</th>
                                            <th>Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detail->itemDistribusis as $item)
                                        <tr>
                                            <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                            <td>{{ $item->jumlah }} {{ $item->satuan }}</td>
                                            <td>
                                                @if($item->kondisi == 'baik')
                                                    <span class="badge badge-success">Baik</span>
                                                @elseif($item->kondisi == 'rusak')
                                                    <span class="badge badge-danger">Rusak</span>
                                                @else
                                                    <span class="badge badge-warning">Kadaluarsa</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('distribusi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Modal Tambah Lokasi -->
<div class="modal fade" id="modalTambahLokasi" tabindex="-1" role="dialog" aria-labelledby="modalTambahLokasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahLokasiLabel">
                    <i class="fas fa-map-marker-alt"></i> Tambah Lokasi Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambahLokasi">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_nama_lokasi">Nama Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_nama_lokasi" name="nama_lokasi" 
                                       placeholder="Contoh: Gudang Pusat" maxlength="50" required>
                                <small class="text-danger d-none" id="error_nama_lokasi"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_jenis_lokasi">Jenis Lokasi <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_jenis_lokasi" name="jenis_lokasi" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="gudang">Gudang</option>
                                    <option value="toko">Toko</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_alamat">Alamat</label>
                        <textarea class="form-control" id="modal_alamat" name="alamat" rows="2" 
                                  placeholder="Alamat lengkap lokasi"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_kapasitas">Kapasitas <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="modal_kapasitas" name="kapasitas" 
                                       placeholder="Contoh: 1000" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_satuan_kapasitas">Satuan Kapasitas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_satuan_kapasitas" name="satuan_kapasitas" 
                                       placeholder="Contoh: m³, kg, unit" maxlength="10" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_penanggung_jawab">Penanggung Jawab <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_penanggung_jawab" name="penanggung_jawab" 
                                       placeholder="Nama penanggung jawab" maxlength="100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_no_telepon">No. Telepon</label>
                                <input type="text" class="form-control" id="modal_no_telepon" name="no_telepon" 
                                       placeholder="08xxxxxxxxxx" maxlength="15">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="modal_status" name="status" required>
                            <option value="aktif" selected>Aktif</option>
                            <option value="non_aktif">Non Aktif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSimpanLokasi">
                    <i class="fas fa-save"></i> Simpan Lokasi
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tanggal Distribusi (header)
    flatpickr("#tanggal_distribusi", {
        dateFormat: "Y-m-d",   // value ke server
        altInput: true,
        altFormat: "d-M-Y",    // tampilan: 15-Nov-2025
        defaultDate: "{{ isset($distribusi) ? $distribusi->tanggal_distribusi : date('Y-m-d') }}"
        // defaultDate: "{{ old('tanggal_distribusi', !empty($distribusi->tanggal_distribusi) ? date('Y-m-d', strtotime($distribusi->tanggal_distribusi)) : date('Y-m-d')) }}"
    });
});
let detailIndex = 0;
let lokasi = @json($lokasi);
const produk = @json($produk);
const isEdit = {{ $isEdit ? 'true' : 'false' }};

$(document).ready(function() {
    @if(!$isEdit)
    tambahDetailLokasi();
    @endif
    
    $('#btnTambahDetail').click(function() {
        tambahDetailLokasi();
    });
    
    $(document).on('click', '.btnHapusDetail', function() {
        $(this).closest('.detail-card').remove();
    });

    $(document).on('click', '.btnTambahItem', function() {
        const container = $(this).closest('.detail-card').find('.items-container');
        tambahItemRow(container);
    });

    $(document).on('click', '.btnHapusItem', function() {
        $(this).closest('tr').remove();
    });

    // Modal Tambah Lokasi
    $(document).on('click', '.btnTambahLokasiModal', function() {
        $('#formTambahLokasi')[0].reset();
        $('#error_nama_lokasi').addClass('d-none').text('');
        $('#modalTambahLokasi').modal('show');
    });

    // Validasi nama lokasi saat input
    $('#modal_nama_lokasi').on('input', function() {
        const namaLokasi = $(this).val().trim().toLowerCase();
        const isDuplicate = lokasi.some(lok => lok.nama_lokasi.toLowerCase() === namaLokasi);
        
        if (isDuplicate) {
            $('#error_nama_lokasi').removeClass('d-none').text('Nama lokasi sudah ada, tidak boleh duplikat!');
            $('#btnSimpanLokasi').prop('disabled', true);
        } else {
            $('#error_nama_lokasi').addClass('d-none').text('');
            $('#btnSimpanLokasi').prop('disabled', false);
        }
    });

    // Simpan Lokasi Baru
    $('#btnSimpanLokasi').click(function() {
        const form = $('#formTambahLokasi');
        
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        const namaLokasi = $('#modal_nama_lokasi').val().trim();
        const isDuplicate = lokasi.some(lok => lok.nama_lokasi.toLowerCase() === namaLokasi.toLowerCase());
        
        if (isDuplicate) {
            alert('Nama lokasi sudah ada, tidak boleh duplikat!');
            return;
        }

        const btnSimpan = $(this);
        btnSimpan.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '{{ route("lokasi.store.ajax") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nama_lokasi: $('#modal_nama_lokasi').val(),
                jenis_lokasi: $('#modal_jenis_lokasi').val(),
                alamat: $('#modal_alamat').val(),
                kapasitas: $('#modal_kapasitas').val(),
                satuan_kapasitas: $('#modal_satuan_kapasitas').val(),
                penanggung_jawab: $('#modal_penanggung_jawab').val(),
                no_telepon: $('#modal_no_telepon').val(),
                status: $('#modal_status').val()
            },
            success: function(response) {
                if (response.success) {
                    // Tambahkan lokasi baru ke array
                    lokasi.push(response.data);
                    
                    // Update semua dropdown lokasi
                    updateLokasiDropdown();
                    
                    $('#modalTambahLokasi').modal('hide');
                    
                    // Tampilkan notifikasi sukses
                    toastr.success('Lokasi berhasil ditambahkan!');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan saat menyimpan lokasi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function() {
                btnSimpan.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Lokasi');
            }
        });
    });
    
    $('#formDistribusi').submit(function(e) {
        @if(!$isEdit)
        const detailCount = $('.detail-card').length;
        
        if (detailCount === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 lokasi tujuan!');
            return false;
        }

        let hasItems = true;
        $('.detail-card').each(function() {
            const itemCount = $(this).find('.items-container tr').length;
            if (itemCount === 0) {
                hasItems = false;
                return false;
            }
        });

        if (!hasItems) {
            e.preventDefault();
            alert('Setiap lokasi tujuan harus memiliki minimal 1 item!');
            return false;
        }
        @endif
        
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    });
});

function updateLokasiDropdown() {
    $('.select-lokasi').each(function() {
        const currentValue = $(this).val();
        let optionsLokasi = '<option value="">-- Pilih Lokasi --</option>';
        
        lokasi.forEach(lok => {
            optionsLokasi += `<option value="${lok.id_lokasi}">${lok.nama_lokasi} (${lok.jenis_lokasi})</option>`;
        });
        
        $(this).html(optionsLokasi);
        
        if (currentValue) {
            $(this).val(currentValue);
        }
    });
}

function tambahDetailLokasi() {
    let optionsLokasi = '<option value="">-- Pilih Lokasi --</option>';
    lokasi.forEach(lok => {
        optionsLokasi += `<option value="${lok.id_lokasi}">${lok.nama_lokasi} (${lok.jenis_lokasi})</option>`;
    });
    
    const detailCard = `
        <div class="card mb-3 detail-card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Lokasi Tujuan ${detailIndex + 1}</h5>
                    <button type="button" class="btn btn-danger btn-sm btnHapusDetail">
                        <i class="fas fa-trash"></i> Hapus Tujuan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lokasi Tujuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="details[${detailIndex}][id_lokasi]" class="form-control select-lokasi" required>
                                    ${optionsLokasi}
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success btnTambahLokasiModal" title="Tambah Lokasi Baru">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Detail <span class="text-danger">*</span></label>
                            <input type="text"
                            name="details[${detailIndex}][tanggal_detail]"
                            class="form-control tanggal-detail"
                            required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="details[${detailIndex}][nama_penerima]" 
                                   class="form-control" placeholder="Nama penerima" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="details[${detailIndex}][catatan]" 
                                      class="form-control" rows="1" placeholder="Catatan (opsional)"></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6>Item Distribusi</h6>
                    <button type="button" class="btn btn-success btn-sm btnTambahItem">
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 35%">Produk</th>
                                <th style="width: 15%">Stok Tersedia</th>
                                <th style="width: 15%">Jumlah</th>
                                <th style="width: 15%">Kondisi</th>
                                <th style="width: 15%">Keterangan</th>
                                <th style="width: 5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="items-container" data-detail-index="${detailIndex}">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    $('#detailContainer').append(detailCard);
    // Inisialisasi flatpickr untuk tanggal detail baris baru
    const selector = `input[name="details[${detailIndex}][tanggal_detail]"]`;
    flatpickr(selector, {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-M-Y",
        defaultDate: "{{ date('Y-m-d') }}"
    });
    // Tambah 1 item row secara otomatis
    tambahItemRow($('.detail-card').last().find('.items-container'));
    
    detailIndex++;
}

function tambahItemRow(container) {
    const detailIdx = container.data('detail-index');
    const itemIdx = container.find('tr').length;
    
    let optionsProduk = '<option value="">-- Pilih Produk --</option>';
    produk.forEach(prod => {
        optionsProduk += `<option value="${prod.id_produk}" data-stok="${prod.stok_tersedia}" data-satuan="${prod.satuan}">${prod.nama_produk} (${prod.stok_tersedia} ${prod.satuan})</option>`;
    });
    
    const row = `
        <tr>
            <td>
                <select name="details[${detailIdx}][items][${itemIdx}][id_produk]" 
                        class="form-control form-control-sm select-produk" required>
                    ${optionsProduk}
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm stok-info" readonly value="-">
            </td>
            <td>
                <input type="number" name="details[${detailIdx}][items][${itemIdx}][jumlah]" 
                       class="form-control form-control-sm" min="1" placeholder="0" required>
            </td>
            <td>
                <select name="details[${detailIdx}][items][${itemIdx}][kondisi]" 
                        class="form-control form-control-sm" required>
                    <option value="baik" selected>Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="kadaluarsa">Kadaluarsa</option>
                </select>
            </td>
            <td>
                <input type="text" name="details[${detailIdx}][items][${itemIdx}][keterangan]" 
                       class="form-control form-control-sm" placeholder="Keterangan">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btnHapusItem">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    container.append(row);
}

$(document).on('change', '.select-produk', function() {
    const selected = $(this).find(':selected');
    const stok = selected.data('stok');
    const satuan = selected.data('satuan');
    const row = $(this).closest('tr');
    
    if (stok !== undefined) {
        row.find('.stok-info').val(stok + ' ' + satuan);
    } else {
        row.find('.stok-info').val('-');
    }
});
</script>
@endpush

@endsection