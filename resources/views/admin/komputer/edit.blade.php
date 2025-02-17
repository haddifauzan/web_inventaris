@extends('admin.layouts.master')
@section('title', 'Edit Komputer')
@section('content')
<section class="section text-sm">
    <div class="row">
        <div class="col-12">
            <div class="card p-3">
                <div class="card-header text-dark text-center">
                    <h2 class="text-bold mb-0">Form Edit Komputer</h2>
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
                                    <option value="PC" {{ old('model', $barang->model) == 'PC' ? 'selected' : '' }}>PC</option>
                                    <option value="Laptop" {{ old('model', $barang->model) == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                                </select>
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label col-form-label-sm">Tipe/Merk <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('tipe_merk') is-invalid @enderror" 
                                        name="tipe_merk" 
                                        id="selectTipeMerk" 
                                        required>
                                    <option value="">Pilih Tipe/Merk</option>
                                    @foreach($tipeBarang as $tipe)
                                    <option value="{{ $tipe->id_tipe_barang }}" 
                                        {{ old('tipe_merk', $barang->tipe_merk ?? '') == $tipe->tipe_merk ? 'selected' : '' }}
                                            data-merk="{{ $tipe->tipe_merk }}">
                                        {{ $tipe->tipe_merk }}
                                    </option>
                                    @endforeach
                                </select>
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
                                    @php
                                        $osList = ['Windows 7', 'Windows 8', 'Windows 10', 'Windows 11', 'Linux', 'MacOS'];
                                    @endphp
                                    @foreach($osList as $os)
                                        <option value="{{ $os }}" {{ old('operating_system', $barang->operating_system) == $os ? 'selected' : '' }}>
                                            {{ $os }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('operating_system')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label col-form-label-sm">Tahun Perolehan <span class="text-danger">*</span></label>
                                <input type="month" class="form-control form-control-sm @error('tahun_perolehan') is-invalid @enderror" 
                                    name="tahun_perolehan" 
                                    value="{{ old('tahun_perolehan', date('Y-m', strtotime($barang->tahun_perolehan))) }}" 
                                    required>
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
                                    @php
                                        $serial = json_decode($barang->serial, true);
                                    @endphp
                                    <input type="text" class="form-control form-control-sm @error('serial.cpu') is-invalid @enderror" 
                                        name="serial[cpu]" 
                                        value="{{ old('serial.cpu', $serial['cpu'] ?? '') }}"
                                        placeholder="Masukkan serial number CPU">
                                    @error('serial.cpu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label col-form-label-sm">Serial Number Monitor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('serial.monitor') is-invalid @enderror" 
                                        name="serial[monitor]" 
                                        value="{{ old('serial.monitor', $serial['monitor'] ?? '') }}"
                                        placeholder="Masukkan serial number monitor">
                                    @error('serial.monitor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="mb-0 text-bold">Spesifikasi</h5>
                                <div class="ms-2 flex-grow-1">
                                    <hr class="my-0">
                                </div>
                                <button type="button" class="btn btn-primary btn-sm ms-2" id="tambahSpesifikasi">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Spesifikasi
                                </button>
                            </div>
                            <div id="spesifikasiContainer">
                                @php
                                    $spesifikasi = is_string($barang->spesifikasi) ? 
                                        json_decode($barang->spesifikasi, true) : 
                                        (is_array($barang->spesifikasi) ? $barang->spesifikasi : []);
                                @endphp
                                @foreach($spesifikasi as $key => $value)
                                    <div class="spesifikasi-item">
                                        <div class="d-flex gap-2 mb-3">
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="spesifikasi_keys[]" 
                                                   value="{{$key}}"
                                                   placeholder="Nama Spesifikasi">
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="spesifikasi_values[]" 
                                                   value="{{$value}}"
                                                   placeholder="Nilai Spesifikasi">
                                            <button type="button" class="btn btn-danger btn-sm hapus-spesifikasi">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label col-form-label-sm">Kelayakan (%)</label>
                                <div class="d-flex align-items-center">
                                    <input type="range" class="form-range me-2" 
                                           name="kelayakan" 
                                           min="0" 
                                           max="100" 
                                           id="kelayakanRange" 
                                           value="{{ old('kelayakan', $barang->kelayakan) }}" 
                                           required 
                                           style="direction: ltr;">
                                    <span id="kelayakanValue" class="fw-bold">{{ old('kelayakan', $barang->kelayakan) }}</span>%
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label col-form-label-sm">Keterangan</label>
                            <textarea class="form-control form-control-sm @error('keterangan') is-invalid @enderror" 
                                    name="keterangan" 
                                    rows="3"
                                    placeholder="Tambahkan keterangan jika diperlukan">{{ old('keterangan', $barang->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('komputer.index') }}" class="btn btn-secondary btn-sm me-2">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Konfirmasi Penggantian Spesifikasi -->
<div class="modal fade" id="replaceSpecModal" tabindex="-1" aria-labelledby="replaceSpecModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replaceSpecModalLabel">Konfirmasi Penggantian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda ingin mengganti spesifikasi yang ada dengan spesifikasi dari tipe barang yang dipilih?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmReplaceSpec">Ganti Spesifikasi</button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const kelayakanRange = document.getElementById('kelayakanRange');
    const spesifikasiContainer = document.getElementById('spesifikasiContainer');
    const tambahSpesifikasiBtn = document.getElementById('tambahSpesifikasi');
    const selectTipeMerk = document.getElementById('selectTipeMerk');
    const replaceSpecModalEl = document.getElementById('replaceSpecModal');
    const confirmReplaceSpecBtn = document.getElementById('confirmReplaceSpec');
    
    let replaceSpecModal;

    // Default specifications
    const defaultSpesifikasi = {
        'Processor': '',
        'RAM': '',
        'Storage': ''
    };

    // Initialize kelayakan range
    if (kelayakanRange) {
        kelayakanRange.addEventListener('input', function() {
            document.getElementById('kelayakanValue').textContent = this.value;
        });
    }

    // Function to add new specification row
    function tambahSpesifikasiBaru(key = '', value = '') {
        const newItem = document.createElement('div');
        newItem.className = 'spesifikasi-item';
        newItem.innerHTML = `
            <div class="d-flex gap-2 mb-3">
                <input type="text" class="form-control form-control-sm" 
                       name="spesifikasi_keys[]" 
                       value="${key}"
                       placeholder="Nama Spesifikasi">
                <input type="text" class="form-control form-control-sm" 
                       name="spesifikasi_values[]" 
                       value="${value}"
                       placeholder="Nilai Spesifikasi">
                <button type="button" class="btn btn-danger btn-sm hapus-spesifikasi">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        spesifikasiContainer.appendChild(newItem);
    }

    // Function to populate specifications
    function populateSpesifikasi(spesifikasi) {
        if (!spesifikasiContainer) return;

        spesifikasiContainer.innerHTML = '';
        Object.entries(spesifikasi).forEach(([key, value]) => {
            tambahSpesifikasiBaru(key, value);
        });
    }

    // Function to show notifications
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '1050';
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    // Event Listeners
    if (tambahSpesifikasiBtn) {
        tambahSpesifikasiBtn.addEventListener('click', () => tambahSpesifikasiBaru());
    }

    if (spesifikasiContainer) {
        spesifikasiContainer.addEventListener('click', function(e) {
            const hapusBtn = e.target.closest('.hapus-spesifikasi');
            if (!hapusBtn) return;

            const spesifikasiItem = hapusBtn.closest('.spesifikasi-item');
            if (spesifikasiContainer.children.length > 1) {
                spesifikasiItem.remove();
            } else {
                showNotification('Minimal harus ada satu spesifikasi', 'warning');
            }
        });
    }

    if (selectTipeMerk) {
        selectTipeMerk.addEventListener('change', function() {
            const tipeBarangId = this.value;
            
            if (!tipeBarangId) {
                populateSpesifikasi(defaultSpesifikasi);
                return;
            }
            
            fetch(`/api/tipe-barang/${tipeBarangId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        try {
                            console.log('Spesifikasi:', result.data);
                            const spesifikasi = JSON.parse(result.data);

                            // Cek apakah ada spesifikasi saat ini
                            const currentSpecs = Array.from(spesifikasiContainer.querySelectorAll('.spesifikasi-item')).map(item => {
                                const keyInput = item.querySelector('[name="spesifikasi_keys[]"]');
                                const valueInput = item.querySelector('[name="spesifikasi_values[]"]');
                                return {
                                    key: keyInput?.value || '',
                                    value: valueInput?.value || ''
                                };
                            });

                            if (currentSpecs.length > 0 && Object.keys(spesifikasi).length > 0) {
                                // Pastikan modal hanya diinisialisasi sekali
                                if (!replaceSpecModal) {
                                    replaceSpecModal = new bootstrap.Modal(replaceSpecModalEl);
                                }
                                replaceSpecModal.show();

                                // Hapus event listener lama sebelum menambahkan yang baru
                                confirmReplaceSpecBtn.replaceWith(confirmReplaceSpecBtn.cloneNode(true));
                                document.getElementById('confirmReplaceSpec').addEventListener('click', function () {
                                    populateSpesifikasi(spesifikasi);
                                    replaceSpecModal.hide();
                                });

                                return;
                            }

                            // Jika tidak ada spesifikasi lama, langsung isi otomatis
                            populateSpesifikasi(spesifikasi);
                        } catch (e) {
                            console.error('Error parsing spesifikasi:', e);
                            showNotification('Format spesifikasi tidak valid', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Gagal memuat spesifikasi', 'error');
                });
        });
    }

    // Initialize empty container if needed
    if (spesifikasiContainer && spesifikasiContainer.children.length === 0) {
        populateSpesifikasi(defaultSpesifikasi);
    }
});

</script>

@endsection