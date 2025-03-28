@extends('user.layouts.master')
@section('title', 'Tambah switch')
@section('content')
<section class="section text-sm">
    <div class="row">
        <div class="col-12">
            <div class="card p-3">
                <div class="card-header text-dark text-center">
                    <h2 class="text-bold mb-0">Form Tambah Switch</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('switch.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jenis_barang" value="Switch">

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label col-form-label-sm">Tipe/Merk <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('tipe_merk') is-invalid @enderror"
                                        name="tipe_merk"
                                        id="selectTipeMerk"
                                        required>
                                    <option value="">Pilih Tipe/Merk</option>
                                    @foreach($tipeBarang as $tipe)
                                        <option value="{{ $tipe->id_tipe_barang }}"
                                                {{ old('tipe_merk') == $tipe->id_tipe_barang ? 'selected' : '' }}
                                                data-merk="{{ $tipe->tipe_merk }}">
                                            {{ $tipe->tipe_merk }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipe_merk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label col-form-label-sm">Tahun Perolehan <span class="text-danger">*</span></label>
                                <input type="month" class="form-control form-control-sm @error('tahun_perolehan') is-invalid @enderror"
                                       name="tahun_perolehan" value="{{ old('tahun_perolehan', date('Y-m')) }}" required>
                                @error('tahun_perolehan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label col-form-label-sm">Serial Number Switch <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('serial') is-invalid @enderror"
                                           name="serial" value="{{ old('serial') }}"
                                           placeholder="Masukkan serial number switch">
                                    @error('serial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label col-form-label-sm">Status Barang <span class="text-danger text-sm">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" 
                                                id="statusBaru" value="Baru"
                                                {{ old('status_barang', 'Baru') == 'Baru' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="statusBaru">Baru</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" 
                                                id="statusBackup" value="Backup"
                                                {{ old('status_barang') == 'Backup' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusBackup">Backup</label>
                                        </div>
                                    </div>
                                    @error('status_barang')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                <!-- Default spesifikasi fields -->
                                <div class="spesifikasi-item">
                                    <div class="d-flex gap-2 mb-3">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="spesifikasi_keys[]" 
                                               value="Port Count"
                                               placeholder="Nama Spesifikasi">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="spesifikasi_values[]" 
                                               value="{{old('spesifikasi.Port Count')}}"
                                               placeholder="Nilai Spesifikasi">
                                        <button type="button" class="btn btn-danger btn-sm hapus-spesifikasi">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">  
                            <label class="form-label col-form-label-sm">Keterangan</label>
                            <textarea class="form-control form-control-sm @error('keterangan') is-invalid @enderror"
                                      name="keterangan" rows="3"
                                      placeholder="Tambahkan keterangan jika diperlukan">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('switch.index') }}" class="btn btn-secondary btn-sm me-2">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="reset" class="btn btn-danger btn-sm me-2">
                                <i class="bi bi-arrow-repeat me-1"></i>Reset
                            </button>
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

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda ingin mengisi otomatis spesifikasi yang tersedia sesuai dengan tipe atau merk yang disediakan?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmYes">Ya, Isi Otomatis</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spesifikasiContainer = document.getElementById('spesifikasiContainer');
        const tambahSpesifikasiBtn = document.getElementById('tambahSpesifikasi');
        const selectTipeMerk = document.getElementById('selectTipeMerk');
    
        // Default spesifikasi fields
        const defaultSpesifikasi = {
            'Port Count': '',
            'Speed': ''
        };
    
        // Initialize with default fields
        populateSpesifikasi(defaultSpesifikasi);
    
        tambahSpesifikasiBtn.addEventListener('click', function() {
            tambahSpesifikasiBaru();
        });
    
        spesifikasiContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('hapus-spesifikasi') || 
                e.target.parentElement.classList.contains('hapus-spesifikasi')) {
                const button = e.target.classList.contains('hapus-spesifikasi') ? 
                              e.target : e.target.parentElement;
                const spesifikasiItem = button.closest('.spesifikasi-item');
                if (spesifikasiContainer.children.length > 1) {
                    spesifikasiItem.remove();
                } else {
                    showNotification('Minimal harus ada satu spesifikasi', 'warning');
                }
            }
        });
    
        selectTipeMerk.addEventListener('change', function() {
            const tipeBarangId = this.value;
            
            if (!tipeBarangId) {
                populateSpesifikasi(defaultSpesifikasi);
                return;
            }
            
            fetch(`/api/tipe-barang/switch/${tipeBarangId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        // Simpan data spesifikasi ke variabel sementara
                        const spesifikasi = JSON.parse(result.data);

                        // Tampilkan modal konfirmasi
                        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                        confirmModal.show();

                        // Jika tombol "Ya, Isi Otomatis" ditekan
                        document.getElementById('confirmYes').addEventListener('click', function () {
                            populateSpesifikasi(spesifikasi);
                            confirmModal.hide();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Gagal memuat spesifikasi', 'error');
                });

        });
    
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
    
        function populateSpesifikasi(spesifikasi) {
            spesifikasiContainer.innerHTML = '';
            Object.entries(spesifikasi).forEach(([key, value]) => {
                tambahSpesifikasiBaru(key, value);
            });
        }
    
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
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    });
</script>

@endsection