@extends('layouts.master')
@section('title', !empty($produksi->id_produksi) ? 'Edit Produksi' : 'Tambah Produksi')
@section('content')
<style>
    .flatpickr-input[readonly] {
        background-color: white !important;
        color: inherit !important;
        cursor: pointer !important;
    }
    .input-final-locked {
        background-color: #e9ecef !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        pointer-events: none;
    }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ !empty($produksi->id_produksi) ? 'Edit' : 'Tambah' }} Produksi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('produksi.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produksi.index') }}">Produksi</a></li>
                        <li class="breadcrumb-item active">{{ !empty($produksi->id_produksi) ? 'Edit' : 'Tambah' }}</li>
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
                $isEdit = isset($produksi) && !empty($produksi->id_produksi);
                $isSelesai = $isEdit && $produksi->status === 'selesai';
                $formAction = $isEdit 
                    ? route('produksi.update', $produksi->id_produksi) 
                    : route('produksi.store');
            @endphp
            @if($isSelesai)
                <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-lock mr-2"></i>
                    <span>Produksi ini telah <strong>selesai</strong>. Data tidak dapat diubah lagi untuk mencegah kesalahan input.</span>
                </div>
            @endif
            <form action="{{ $formAction }}" method="POST" id="formProduksi">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- Card Info Produksi -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Isi informasi utama mengenai proses produksi yang sedang dilakukan.</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tanggal_produksi">Tanggal Produksi <span class="text-danger">*</span></label>
                                    <input type="text" 
                                        class="form-control @error('tanggal_produksi') is-invalid @enderror" 
                                        id="tanggal_produksi" 
                                        name="tanggal_produksi" 
                                        value="{{ old('tanggal_produksi', !empty($produksi->tanggal_produksi) ? date('Y-m-d', strtotime($produksi->tanggal_produksi)) : date('Y-m-d')) }}"
                                        {{ $isEdit ? 'readonly' : 'required' }}>
                                    @error('tanggal_produksi')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="waktu_mulai">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="text" 
                                        class="form-control @error('waktu_mulai') is-invalid @enderror {{ $isSelesai ? 'input-final-locked' : '' }}" 
                                        id="waktu_mulai" 
                                        name="waktu_mulai" 
                                        {{-- value="{{ old('waktu_mulai', !empty($produksi->waktu_mulai) ? $produksi->waktu_mulai : date('H:i:s')) }}"
                                        {{ $isEdit ? 'readonly' : 'required' }}> --}}
                                        value="{{ old('waktu_mulai', !empty($produksi->waktu_mulai) ? $produksi->waktu_mulai : date('H:i:s'))}}"
                                        {{ $isSelesai ? 'readonly' : 'required' }}>
                                    <small class="text-muted">Gunakan format 24 jam (contoh: 14:30:00)</small>

                                    @error('waktu_mulai')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if($isEdit)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="waktu_selesai">Waktu Selesai</label>
                                    <input type="text" 
                                        class="form-control @error('waktu_selesai') is-invalid @enderror {{ $isSelesai ? 'input-final-locked' : '' }}" 
                                        id="waktu_selesai" 
                                        name="waktu_selesai" 
                                        value="{{ old('waktu_selesai', $produksi->waktu_selesai) }}"
                                                {{ $isSelesai ? 'readonly' : 'required' }}>
                                    <small class="text-muted">Format 24 jam (contoh: 16:45:00)</small>

                                    @error('waktu_selesai')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if($isEdit)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Status</label>

                                    @if($produksi->status === 'selesai')
                                        {{-- STATUS FINAL --}}
                                        <input type="text"
                                            class="form-control"
                                            value="Selesai"
                                            readonly>

                                        <input type="hidden" name="status" value="selesai">
                                    @else
                                        {{-- STATUS MASIH BISA DIUBAH --}}
                                        <select class="form-control @error('status') is-invalid @enderror"
                                                name="status"
                                                required>
                                            <option value="pending" {{ old('status', $produksi->status) == 'pending' ? 'selected' : '' }}>
                                                Pending
                                            </option>
                                            <option value="proses" {{ old('status', $produksi->status) == 'proses' ? 'selected' : '' }}>
                                                Proses
                                            </option>
                                            <option value="selesai" {{ old('status', $produksi->status) == 'selesai' ? 'selected' : '' }}>
                                                Selesai
                                            </option>
                                            <option value="gagal" {{ old('status', $produksi->status) == 'gagal' ? 'selected' : '' }}>
                                                Gagal
                                            </option>
                                        </select>
                                    @endif

                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            {{-- <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <input type="text"
                                        class="form-control"
                                        value="{{ $produksi->status }}"
                                        readonly> 
                                    {{-- Ubah jadi readonly input --}}                                   
                                    {{-- <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required> --}}
                                        {{-- <option value="">-- Pilih Status --</option>
                                        <option value="pending" {{ old('status', $produksi->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="proses" {{ old('status', $produksi->status ?? '') == 'proses' ? 'selected' : '' }}>Proses</option>
                                        <option value="selesai" {{ old('status', $produksi->status ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="gagal" {{ old('status', $produksi->status ?? '') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>  --}}
                            @endif
                            <div class="col-md-{{ $isEdit ? 4 : 8 }}">
                                <div class="form-group">
                                    <label for="catatan">Catatan</label>
                                    <textarea class="form-control @error('catatan') is-invalid @enderror {{ $isSelesai ? 'input-final-locked' : '' }}" 
                                            id="catatan" 
                                            name="catatan" 
                                            rows="2"
                                            placeholder="Contoh: Proses Produksi Jenang Ketan (opsional)" {{ $isSelesai ? 'readonly' : '' }}>{{ old('catatan', $produksi->catatan ?? '') }}</textarea>
                                    @error('catatan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>                            
                            </div>                            
                        </div>

                        {{-- <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" 
                                      name="catatan" 
                                      rows="2"
                                      placeholder="Catatan tambahan (opsional)">{{ old('catatan', $produksi->catatan ?? '') }}</textarea>
                            @error('catatan')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div> --}}
                    </div>
                </div>

                <!-- Card Detail Produksi -->
                @if(!$isEdit)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Isi rincian produksi sesuai komposisi dan jumlah yang akan dibuat</h5>
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
                                        <th style="width: 20%">Resep</th>
                                        <th style="width: 20%">Produk</th>
                                        <th style="width: 15%">Jumlah Target</th>
                                        <th style="width: 40%">Kebutuhan Bahan</th>
                                        <th style="width: 5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detailContainer">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Memperbarui rincian produksi sesuai dengan komposisi Produk</h5>
                         @if($isSelesai)
                            <span class="badge badge-info badge-final-info">
                                <i class="fas fa-lock mr-1"></i> Data Terkunci — Produksi Selesai
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableDetail">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 15%">Resep</th>
                                        <th style="width: 15%">Produk</th>
                                        <th style="width: 10%">Target</th>
                                        <th style="width: 12%">Berhasil</th>
                                        <th style="width: 12%">Gagal</th>
                                        <th style="width: 10%">Persentase</th>
                                        <th style="width: 26%">Keterangan Gagal</th>
                                    </tr>
                                </thead>
                                <tbody id="detailEditContainer">
                                    @foreach($detailProduksi as $detail)
                                    <tr>
                                        <td>{{ $detail->resep->nama_resep ?? '-' }}</td>
                                        <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                                        <td class="text-center">{{ $detail->jumlah_target }} {{ $detail->resep->satuan_output }}</td>
                                        <td>
                                            <input type="hidden" name="detail[{{ $loop->index }}][id_detail_produksi]" value="{{ $detail->id_detail_produksi }}">
                                            <input type="number" name="detail[{{ $loop->index }}][jumlah_berhasil]" 
                                                   class="form-control form-control-sm inputBerhasilEdit {{ $isSelesai ? 'input-final-locked' : '' }}" 
                                                   value="{{ $detail->jumlah_berhasil }}" 
                                                   min="0" {{ $isSelesai ? 'disabled readonly' : 'required' }}>
                                            @if($isSelesai)
                                                <input type="hidden" name="detail[{{ $loop->index }}][jumlah_berhasil]" value="{{ $detail->jumlah_berhasil }}">
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" name="detail[{{ $loop->index }}][jumlah_gagal]" 
                                                   class="form-control form-control-sm inputGagalEdit {{ $isSelesai ? 'input-final-locked' : '' }}" 
                                                   value="{{ $detail->jumlah_gagal }}" 
                                                   min="0" {{ $isSelesai ? 'disabled readonly' : 'required' }}>
                                            @if($isSelesai)
                                                <input type="hidden" name="detail[{{ $loop->index }}][jumlah_gagal]" value="{{ $detail->jumlah_gagal }}">
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $detail->persentase_keberhasilan >= 80 ? 'badge-success' : ($detail->persentase_keberhasilan >= 50 ? 'badge-warning' : 'badge-danger') }}">
                                                {{ number_format($detail->persentase_keberhasilan, 2) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <textarea name="detail[{{ $loop->index }}][keterangan_gagal]" 
                                                    class="form-control form-control-sm {{ $isSelesai ? 'input-final-locked' : '' }}" 
                                                    rows="1"
                                                    {{ $isSelesai ? 'readonly' : '' }}>{{ $detail->keterangan_gagal }}</textarea>
                                            {{-- <textarea name="detail[{{ $loop->index }}][keterangan_gagal]" 
                                                      class="form-control form-control-sm" 
                                                      rows="1"{{ $isSelesai ? 'readonly' : '' }}>{{ $detail->keterangan_gagal }}</textarea> --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>          
                @endif

                <div class="card-footer">
                    @if(!$isSelesai)
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    @else
                        <button type="button" class="btn btn-primary" disabled title="Produksi sudah selesai, tidak dapat disimpan ulang">
                            <i class="fas fa-lock"></i> Data Terkunci
                        </button>
                    @endif
                    <a href="{{ route('produksi.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
                {{-- <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('produksi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div> --}}
            </form>
        </div>
    </section>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambahProduk">
                <input type="hidden" id="modal_mode" value="add">
                <input type="hidden" id="modal_id_produk" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Resep <span class="text-danger">*</span></label>
                        <select class="form-control" id="modal_id_resep" required>
                            <option value="">-- Pilih Resep --</option>
                            @foreach($resepList as $resep)
                            <option value="{{ $resep->id_resep }}">{{ $resep->nama_resep }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text"
                                class="form-control"
                                id="modal_nama_produk"
                                maxlength="20"
                                placeholder="Contoh: Jenang Ketan"
                                required>
                    </div>
                    <div class="form-group">
                    <label>Kategori <span class="text-danger">*</span></label>
                    <select class="form-control" id="modal_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Jenang">Jenang</option>
                        <option value="Roti">Roti</option>
                        <option value="Camilan">Camilan</option>
                        <option value="Kue Tradisional">Kue Tradisional</option>
                    </select>
                    </div>

                    {{-- <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_nama_produk" maxlength="20" required>
                    </div> --}}
                    {{-- <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_kategori" maxlength="15" required>
                    </div> --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Jual (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="modal_harga_jual" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Satuan <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_satuan" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <option value="pcs">pcs</option>
                                    {{-- <option value="kg">kg</option>
                                    <option value="gram">gram</option>
                                    <option value="liter">liter</option>
                                    <option value="butir">butir</option> --}}
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <label>Stok Minimum <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="modal_stok_minimum" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" id="modal_deskripsi" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>

                    <button type="button" class="btn btn-danger" id="btnDeleteProduk" style="display:none;">
                        Hapus
                    </button>

                    <button type="button" class="btn btn-warning" id="btnUpdateProduk" style="display:none;">
                        Update
                    </button>

                    <button type="submit" class="btn btn-primary" id="btnSaveProduk">Simpan Produk</button>
                </div>

                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                </div> --}}
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tanggal Produksi, kirim ke server: Y-m-d, tampil: 25-Nov-2025
    flatpickr("#tanggal_produksi", {
        dateFormat: "Y-m-d",   // value ke server
        altInput: true,
        altFormat: "d-M-Y",    // tampilan: 25-Nov-2025
        defaultDate: "{{ old('tanggal_produksi', !empty($produksi->tanggal_produksi) ? date('Y-m-d', strtotime($produksi->tanggal_produksi)) : date('Y-m-d')) }}",
        allowInput: false,
    });

    if (!isSelesai) {
        flatpickr("#waktu_mulai", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i:S",
            altInput: true,
            altFormat: "H:i:S",
            time_24hr: true,
            defaultDate: "{{ old('waktu_mulai', !empty($produksi->waktu_mulai) ? $produksi->waktu_mulai : date('H:i:s')) }}",
        });

        flatpickr("#waktu_selesai", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i:S",
            altInput: true,
            altFormat: "H:i:S",
            time_24hr: true,
            @if(!empty($produksi->waktu_selesai))
            defaultDate: "{{ old('waktu_selesai', $produksi->waktu_selesai) }}",
            @endif
        });
    }
    // // Waktu Mulai - format 24 jam, simpan HH:mm:ss
    // flatpickr("#waktu_mulai", {
    //     enableTime: true,
    //     noCalendar: true,
    //     dateFormat: "H:i:S",   // value ke server -> 14:30:00
    //     altInput: true,
    //     altFormat: "H:i:S",    // tampilan di input -> 14:30:00
    //     time_24hr: true,
    //     defaultDate: "{{ old('waktu_mulai', !empty($produksi->waktu_mulai) ? $produksi->waktu_mulai : date('H:i:s')) }}",
    // });

    // // Waktu Selesai (hanya muncul di edit)
    // flatpickr("#waktu_selesai", {
    //     enableTime: true,
    //     noCalendar: true,
    //     dateFormat: "H:i:S",
    //     altInput: true,
    //     altFormat: "H:i:S",
    //     time_24hr: true,
    //     @if(!empty($produksi->waktu_selesai))
    //     defaultDate: "{{ old('waktu_selesai', $produksi->waktu_selesai) }}",
    //     @endif
    // });

});


let rowIndex = 0;
const resepList = @json($resepList);
const produkList = @json($produkList);
const isEdit = {{ $isEdit ? 'true' : 'false' }};
const isSelesai = {{ $isSelesai ? 'true' : 'false' }};
let selectProdukAktif = null;

$(document).ready(function() {
    @if(!$isEdit)
    tambahRow();
    @endif
    
    $('#btnTambahDetail').click(function() {
        tambahRow();
    });
    
    $(document).on('click', '.btnHapusRow', function() {
        $(this).closest('tr').remove();
    });
    
    $(document).on('change', '.selectResep, .inputTarget', function() {
        const row = $(this).closest('tr');
        loadKebutuhanBahan(row);
    });

    // Handle tambah produk baru
    $(document).on('click', '.btnTambahProduk', function() {
        selectProdukAktif = $(this).closest('tr').find('.selectProduk');

        $('#modal_mode').val('add');
        $('#modal_id_produk').val('');
        $('#formTambahProduk')[0].reset();

        $('#btnSaveProduk').show();
        $('#btnUpdateProduk').hide();
        $('#btnDeleteProduk').hide();

        $('#modalTambahProduk').modal('show');
    });
    $(document).on('click', '.btnEditProduk', function() {
        const row = $(this).closest('tr');
        const select = row.find('.selectProduk');
        const idProduk = select.val();

        if (!idProduk) return alert('Pilih produk dulu yang mau diedit.');

        selectProdukAktif = select;
        const opt = select.find('option:selected');

        $('#modal_mode').val('edit');
        $('#modal_id_produk').val(idProduk);

        $('#modal_id_resep').val(opt.attr('data-id_resep') || '');
        $('#modal_nama_produk').val(opt.text() || '');
        $('#modal_kategori').val(opt.attr('data-kategori') || '');
        $('#modal_harga_jual').val(opt.attr('data-harga_jual') || '');
        $('#modal_satuan').val(opt.attr('data-satuan') || '');
        $('#modal_stok_minimum').val(opt.attr('data-stok_minimum') || '');
        $('#modal_deskripsi').val(opt.attr('data-deskripsi') || '');

        $('#btnSaveProduk').hide();
        $('#btnUpdateProduk').show();
        $('#btnDeleteProduk').show();

        $('#modalTambahProduk').modal('show');
    });
    $('#btnUpdateProduk').on('click', function() {
        const idProduk = $('#modal_id_produk').val();

        $.ajax({
            url: `{{ url('produksi/produk') }}/${idProduk}`,  // pastikan route PUT kamu di sini
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                id_resep: $('#modal_id_resep').val(),
                nama_produk: $('#modal_nama_produk').val(),
                kategori: $('#modal_kategori').val(),
                harga_jual: $('#modal_harga_jual').val(),
                satuan: $('#modal_satuan').val(),
                stok_minimum: $('#modal_stok_minimum').val(),
                deskripsi: $('#modal_deskripsi').val()
            },
            success: function(res) {
                if (!res.success) return alert(res.message || 'Gagal update produk');

                // update semua dropdown yg punya produk ini (biar sinkron)
                $(`.selectProduk option[value="${idProduk}"]`).each(function() {
                    $(this).text(res.produk.nama_produk)
                        .attr('data-id_resep', res.produk.id_resep ?? '')
                        .attr('data-kategori', res.produk.kategori ?? '')
                        .attr('data-harga_jual', res.produk.harga_jual ?? 0)
                        .attr('data-satuan', res.produk.satuan ?? '')
                        .attr('data-stok_minimum', res.produk.stok_minimum ?? 0)
                        .attr('data-deskripsi', (res.produk.deskripsi ?? '').replace(/"/g,'&quot;'))
                        .removeData('');
                });

                alert('Produk berhasil diupdate!');
                $('#modalTambahProduk').modal('hide');
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Terjadi kesalahan saat update produk');
            }
        });
    });
    $(document).on('click', '.btnHapusProduk', function() {
        const row = $(this).closest('tr');
        const select = row.find('.selectProduk');
        const idProduk = select.val();

        if (!idProduk) return alert('Pilih produk dulu yang mau dihapus.');

        $('#modal_mode').val('edit');
        $('#modal_id_produk').val(idProduk);

        if (!confirm('Yakin mau hapus produk ini?')) return;

        $.ajax({
            url: `{{ url('produksi/produk') }}/${idProduk}`, // pastikan route DELETE kamu di sini
            type: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function(res) {
                if (!res.success) return alert(res.message || 'Gagal hapus produk');

                // hapus option di semua dropdown
                $(`.selectProduk option[value="${idProduk}"]`).remove();

                alert('Produk berhasil dihapus!');
                $('#modalTambahProduk').modal('hide');
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Terjadi kesalahan saat hapus produk');
            }
        });
    });

    // $(document).on('click', '.btnTambahProduk', function() {
    //     selectProdukAktif = $(this).closest('tr').find('.selectProduk');
    //     $('#modalTambahProduk').modal('show');
    // });

    $('#formTambahProduk').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("produksi.store.produk") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_resep: $('#modal_id_resep').val(),
                nama_produk: $('#modal_nama_produk').val(),
                kategori: $('#modal_kategori').val(),
                harga_jual: $('#modal_harga_jual').val(),
                satuan: $('#modal_satuan').val(),
                stok_minimum: $('#modal_stok_minimum').val(),
                deskripsi: $('#modal_deskripsi').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Produk berhasil ditambahkan!');
                    
                    // Tambahkan ke daftar produk di JS
                    produkList.push(response.produk);
                    
                    // Tambahkan option baru ke SEMUA dropdown produk
                    // $('.selectProduk').each(function() {
                    //     $(this).append(
                    //         `<option value="${response.produk.id_produk}">${response.produk.nama_produk}</option>`
                    //     );
                    // });
                    $('.selectProduk').each(function() {
                        $(this).append(`
                        <option value="${response.produk.id_produk}"
                            data-id_resep="${response.produk.id_resep ?? ''}"
                            data-kategori="${response.produk.kategori ?? ''}"
                            data-harga_jual="${response.produk.harga_jual ?? 0}"
                            data-satuan="${response.produk.satuan ?? ''}"
                            data-stok_minimum="${response.produk.stok_minimum ?? 0}"
                            data-deskripsi="${(response.produk.deskripsi ?? '').replace(/"/g,'&quot;')}"
                        >${response.produk.nama_produk}</option>
                        `);
                    });

                    // Auto PILIH produk baru di dropdown baris yang tadi diklik
                    if (selectProdukAktif) {
                        selectProdukAktif.val(response.produk.id_produk).trigger('change');
                    }
                    
                    $('#modalTambahProduk').modal('hide');
                    $('#formTambahProduk')[0].reset();
                } else {
                    alert('Gagal: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
            }
        });
    });
    
    $('#formProduksi').submit(function(e) {
        if (isSelesai) {
            e.preventDefault();
            alert('Produksi sudah selesai dan tidak dapat diubah lagi.');
            return false;
        }
        @if(!$isEdit)
        const rowCount = $('#detailContainer tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 item produksi!');
            return false;
        }
        @else
        const rowCount = $('#detailEditContainer tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Tidak boleh menghapus semua detail produksi!');
            return false;
        }
        @endif
        
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    });
});

function tambahRow() {
    rowIndex++;
    
    let optionsResep = '<option value="">-- Pilih Resep --</option>';
    resepList.forEach(resep => {
        optionsResep += `<option value="${resep.id_resep}" data-kapasitas="${resep.kapasitas_produksi}" data-satuan="${resep.satuan_output}">${resep.nama_resep}</option>`;
    });
    
    // let optionsProduk = '<option value="">-- Pilih Produk --</option>';
    // produkList.forEach(produk => {
    //     optionsProduk += `<option value="${produk.id_produk}">${produk.nama_produk}</option>`;
    // });
    let optionsProduk = '<option value="">-- Pilih Produk --</option>';
    produkList.forEach(produk => {
        optionsProduk += `
        <option value="${produk.id_produk}"
            data-id_resep="${produk.id_resep ?? ''}"
            data-kategori="${produk.kategori ?? ''}"
            data-harga_jual="${produk.harga_jual ?? 0}"
            data-satuan="${produk.satuan ?? ''}"
            data-stok_minimum="${produk.stok_minimum ?? 0}"
            data-deskripsi="${(produk.deskripsi ?? '').replace(/"/g,'&quot;')}"
        >${produk.nama_produk}</option>
        `;
    });

    
    const row = `
        <tr>
            <td>
                <select name="details[${rowIndex}][id_resep]" class="form-control form-control-sm selectResep" required>
                    ${optionsResep}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <select name="details[${rowIndex}][id_produk]" class="form-control form-control-sm selectProduk" required>
                        ${optionsProduk}
                    </select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success btn-sm btnTambahProduk" title="Tambah Produk Baru">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm btnEditProduk" title="Edit Produk">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btnHapusProduk" title="Hapus Produk">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </td>
            <td>
                <input type="number" name="details[${rowIndex}][jumlah_target]" 
                       class="form-control form-control-sm inputTarget" 
                       min="1" placeholder="0" required>
            </td>
            <td>
                <div class="kebutuhan-container"></div>
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

function loadKebutuhanBahan(row) {
    const idResep = row.find('.selectResep').val();
    const jumlahTarget = row.find('.inputTarget').val();
    const container = row.find('.kebutuhan-container');
    
    if (!idResep || !jumlahTarget) {
        container.html('');
        return;
    }

    container.html('<small class="text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat...</small>');

    fetch(`{{ route('produksi.kebutuhan-bahan') }}?id_resep=${idResep}&jumlah_target=${jumlahTarget}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<small>';
                let semuaCukup = true;
                
                data.kebutuhan.forEach(bahan => {
                    const statusClass = bahan.cukup ? 'text-success' : 'text-danger';
                    const statusIcon = bahan.cukup ? '✓' : '✗';
                    html += `<span class="${statusClass}">${statusIcon} ${bahan.nama_bahan}: ${bahan.jumlah_dibutuhkan_stok.toFixed(2)} ${bahan.satuan_stok}</span><br>`;
                    if (!bahan.cukup) semuaCukup = false;
                });
                
                html += '</small>';
                
                if (!semuaCukup) {
                    html += '<br><small class="text-danger"><strong>Stok tidak cukup!</strong></small>';
                }
                
                container.html(html);
            }
        })
        .catch(error => {
            container.html('<small class="text-danger">Gagal memuat</small>');
        });
}
$(document).on('change', '.selectResep', function() {
    const row = $(this).closest('tr');
    const idResep = $(this).val();
    const selectProduk = row.find('.selectProduk');

    selectProduk.html('<option value="">Loading...</option>');

    $.get('{{ url("produksi/produk-by-resep") }}/' + idResep, function(data)  {
        let html = '<option value="">-- Pilih Produk --</option>';
        data.forEach(p => {
            html += `
            <option value="${p.id_produk}"
                data-id_resep="${p.id_resep ?? ''}"
                data-kategori="${p.kategori ?? ''}"
                data-harga_jual="${p.harga_jual ?? 0}"
                data-satuan="${p.satuan ?? ''}"
                data-stok_minimum="${p.stok_minimum ?? 0}"
                data-deskripsi="${(p.deskripsi ?? '').toString().replace(/"/g,'&quot;')}"
            >${p.nama_produk}</option>
            `;
        });

        // data.forEach(p => {
        //     html += `<option value="${p.id_produk}">${p.nama_produk}</option>`;
        // });
        selectProduk.html(html);
    });
});

</script>
@endpush

@endsection