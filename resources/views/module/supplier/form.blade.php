@extends('layouts.master')
@section('title', !empty($supplier->id_supplier) ? 'Edit Supplier' : 'Tambah Supplier')
@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ !empty($supplier->id_supplier) ? 'Edit' : 'Tambah' }} Supplier</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('supplier.index') }}">Supplier</a></li>
                        <li class="breadcrumb-item active">{{ !empty($supplier->id_supplier) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Isi informasi supplier baru yang akan memasok bahan baku</h5>
                </div>
                
                @php
                    $isEdit = !empty($supplier->id_supplier);
                    $formAction = $isEdit 
                        ? route('supplier.update', $supplier->id_supplier) 
                        : route('supplier.store');
                @endphp
                
                <form action="{{ $formAction }}" method="POST">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="card-body">
                        <div class="row">
                            <!-- Bahan Baku -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_bahan_baku">Bahan Baku <span class="text-danger">*</span></label>
                                    <select class="form-control @error('id_bahan_baku') is-invalid @enderror" 
                                            id="id_bahan_baku" 
                                            name="id_bahan_baku" 
                                            required>
                                        <option value="">-- Pilih Bahan Baku --</option>
                                        @foreach($bahanBakus as $bahan)
                                            <option value="{{ $bahan->id_bahan_baku }}" 
                                                {{ old('id_bahan_baku', $supplier->id_bahan_baku ?? '') == $bahan->id_bahan_baku ? 'selected' : '' }}>
                                                {{ $bahan->nama_bahan }} ({{ $bahan->kategori }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_bahan_baku')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Pilih bahan baku yang disupply</small>
                                </div>
                            </div>

                            <!-- Nama Supplier -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_supplier">Nama Supplier <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_supplier') is-invalid @enderror" 
                                           id="nama_supplier" 
                                           name="nama_supplier" 
                                           value="{{ old('nama_supplier', $supplier->nama_supplier ?? '') }}"
                                           placeholder="Contoh: PT. Sumber Makmur"
                                           maxlength="20"
                                           required>
                                    @error('nama_supplier')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Alamat -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat" 
                                              name="alamat" 
                                              rows="3"
                                              placeholder="Contoh: Jl. Merdeka No.123, Jakarta"
                                              required>{{ old('alamat', $supplier->alamat ?? '') }}</textarea>
                                    @error('alamat')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- No. Telepon -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="no_telepon">No. Telepon <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('no_telepon') is-invalid @enderror" 
                                           id="no_telepon" 
                                           name="no_telepon" 
                                           value="{{ old('no_telepon', $supplier->no_telepon ?? '') }}"
                                           placeholder="08xxxxxxxxxx"
                                           maxlength="15"
                                           pattern="[0-9]+"
                                           required>
                                    @error('no_telepon')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Maksimal 15 digit angka</small>
                                </div>
                            </div>

                            <!-- Kontak Person -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kontak_person">Kontak Person <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('kontak_person') is-invalid @enderror" 
                                           id="kontak_person" 
                                           name="kontak_person" 
                                           value="{{ old('kontak_person', $supplier->kontak_person ?? '') }}"
                                           placeholder="Contoh: Budi Santoso"
                                           maxlength="20"
                                           required>
                                    @error('kontak_person')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="aktif" {{ old('status', $supplier->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="non_aktif" {{ old('status', $supplier->status ?? '') == 'non_aktif' ? 'selected' : '' }}>Non Aktif</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@endsection