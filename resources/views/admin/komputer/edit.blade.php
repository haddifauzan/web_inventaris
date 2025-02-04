@extends('admin.layouts.master')
@section('title', 'Update Komputer')
@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm my-2 p-3">
        <div class="card-header text-dark text-center">
            <h2 class="text-bold mb-0">Form Update Komputer</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('komputer.update', $barang->id_barang) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="jenis_barang" value="Komputer">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label col-form-label-sm">Model <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm @error('model') is-invalid @enderror" 
                                name="model" required>
                            <option value="">Pilih Model</option>
                            <option value="PC" {{ $barang->model == 'PC' ? 'selected' : '' }}>PC</option>
                            <option value="Laptop" {{ $barang->model == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                        </select>
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label col-form-label-sm">Tipe/Merk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('tipe_merk') is-invalid @enderror" 
                               name="tipe_merk" value="{{ old('tipe_merk', $barang->tipe_merk) }}" 
                               placeholder="Contoh: Asus, Lenovo, Dell, HP" required>
                        @error('tipe_merk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label col-form-label-sm">Operating System <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm @error('operating_system') is-invalid @enderror" 
                                name="operating_system" required>
                            <option value="">Pilih Operating System</option>
                            <option value="Windows 7" {{ $barang->operating_system == 'Windows 7' ? 'selected' : '' }}>Windows 7</option>
                            <option value="Windows 8" {{ $barang->operating_system == 'Windows 8' ? 'selected' : '' }}>Windows 8</option>
                            <option value="Windows 10" {{ $barang->operating_system == 'Windows 10' ? 'selected' : '' }}>Windows 10</option>
                            <option value="Windows 11" {{ $barang->operating_system == 'Windows 11' ? 'selected' : '' }}>Windows 11</option>
                            <option value="Linux" {{ $barang->operating_system == 'Linux' ? 'selected' : '' }}>Linux</option>
                            <option value="MacOS" {{ $barang->operating_system == 'MacOS' ? 'selected' : '' }}>MacOS</option>
                        </select>
                        @error('operating_system')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label col-form-label-sm">Tahun Perolehan <span class="text-danger">*</span></label>
                        <input type="month" class="form-control form-control-sm @error('tahun_perolehan') is-invalid @enderror" 
                               name="tahun_perolehan" value="{{ old('tahun_perolehan', $barang->tahun_perolehan ? date('Y-m', strtotime($barang->tahun_perolehan)) : '') }}" required>
                        @error('tahun_perolehan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="mb-0 text-bold">Serial Number</h5>
                        <div class="ms-2 flex-grow-1">
                            <hr class="my-0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Serial Number CPU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('serial.cpu') is-invalid @enderror" 
                                   name="serial[cpu]" value="{{ old('serial.cpu', json_decode($barang->serial)->cpu) }}"
                                   placeholder="Masukkan serial number CPU" >
                            @error('serial.cpu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Serial Number Monitor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('serial.monitor') is-invalid @enderror" 
                                   name="serial[monitor]" value="{{ old('serial.monitor', json_decode($barang->serial)->monitor) }}"
                                   placeholder="Masukkan serial number monitor">
                            @error('serial.monitor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="mb-0 text-bold">Spesifikasi </h5>
                        <div class="ms-2 flex-grow-1">
                            <hr class="my-0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label col-form-label-sm">Processor</label>
                                <input type="text" class="form-control form-control-sm" name="spesifikasi[processor]" 
                                       value="{{ old('spesifikasi.processor', $barang->spesifikasi['processor'] ?? '') }}"
                                       placeholder="Contoh: Intel Core i5-10400F">
                            </div>
                            <div class="mb-3">
                                <label class="form-label col-form-label-sm">RAM</label>
                                <input type="text" class="form-control form-control-sm" name="spesifikasi[ram]" 
                                       value="{{ old('spesifikasi.ram', $barang->spesifikasi['ram'] ?? '') }}"
                                       placeholder="Contoh: 16GB DDR4">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label col-form-label-sm">Storage</label>
                                <input type="text" class="form-control form-control-sm" name="spesifikasi[storage]" 
                                       value="{{ old('spesifikasi.storage', $barang->spesifikasi['storage'] ?? '') }}"
                                       placeholder="Contoh: 512GB SSD + 1TB HDD">
                            </div>
                            <div class="mb-3">
                                <label class="form-label col-form-label-sm">GPU (jika ada)</label>
                                <input type="text" class="form-control form-control-sm" name="spesifikasi[gpu]" 
                                       value="{{ old('spesifikasi.gpu', $barang->spesifikasi['gpu'] ?? '') }}"
                                       placeholder="Contoh: NVIDIA RTX 3060">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label col-form-label-sm">Kelayakan (%)</label>
                        <div class="d-flex align-items-center">
                            <input type="range" class="form-range me-2" name="kelayakan" min="0" max="100" id="kelayakanRange" value="{{ old('kelayakan', $barang->kelayakan) }}" required style="direction: ltr;">
                            <span id="kelayakanValue" class="fw-bold">{{ old('kelayakan', $barang->kelayakan) }}</span>%
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('kelayakanRange').addEventListener('input', function() {
                        document.getElementById('kelayakanValue').textContent = this.value;
                    });
                </script>
                <div class="mb-3">
                    <label class="form-label col-form-label-sm">Keterangan</label>
                    <textarea class="form-control form-control-sm @error('keterangan') is-invalid @enderror" 
                              name="keterangan" rows="3"
                              placeholder="Tambahkan keterangan jika diperlukan">{{ old('keterangan', $barang->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('komputer.index') }}" class="btn btn-secondary btn-sm me-2">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <button type="reset" class="btn btn-danger btn-sm me-2">
                        <i class="bi bi-arrow-repeat me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection