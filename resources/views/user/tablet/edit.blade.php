@extends('user.layouts.master')
@section('title', 'Edit Tablet')
@section('content')
<section class="section text-sm">
    <div class="row">
        <div class="col-12">
            <div class="card p-3">
                <div class="card-header text-dark text-center">
                    <h2 class="text-bold mb-0">Form Edit Tablet</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('tablet.update', $barang->id_barang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="jenis_barang" value="Tablet">

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
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label col-form-label-sm">Serial Number Tablet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('serial') is-invalid @enderror"
                                           name="serial" value="{{ old('serial', $barang->serial ?? '') }}"
                                           placeholder="Masukkan serial number tablet">
                                    @error('serial')
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
                                @if(old('spesifikasi_keys'))
                                    @foreach(old('spesifikasi_keys') as $key => $value)
                                        <div class="spesifikasi-item">
                                            <div class="d-flex gap-2 mb-3">
                                                <input type="text" class="form-control form-control-sm"
                                                       name="spesifikasi_keys[]"
                                                       value="{{ $value }}"
                                                       placeholder="Nama Spesifikasi">
                                                <input type="text" class="form-control form-control-sm"
                                                       name="spesifikasi_values[]"
                                                       value="{{ old('spesifikasi_values')[$key] }}"
                                                       placeholder="Nilai Spesifikasi">
                                                <button type="button" class="btn btn-danger btn-sm hapus-spesifikasi">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach(json_decode($barang->spesifikasi ?? '{"Processor":""}') as $key => $value)
                                        <div class="spesifikasi-item">
                                            <div class="d-flex gap-2 mb-3">
                                                <input type="text" class="form-control form-control-sm"
                                                       name="spesifikasi_keys[]"
                                                       value="{{ $key }}"
                                                       placeholder="Nama Spesifikasi">
                                                <input type="text" class="form-control form-control-sm"
                                                       name="spesifikasi_values[]"
                                                       value="{{ $value }}"
                                                       placeholder="Nilai Spesifikasi">
                                                <button type="button" class="btn btn-danger btn-sm hapus-spesifikasi">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label col-form-label-sm">Keterangan</label>
                            <textarea class="form-control form-control-sm @error('keterangan') is-invalid @enderror"
                                      name="keterangan" rows="3"
                                      placeholder="Tambahkan keterangan jika diperlukan">{{ old('keterangan', $barang->keterangan ?? '') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('tablet.index') }}" class="btn btn-secondary btn-sm me-2">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spesifikasiContainer = document.getElementById('spesifikasiContainer');
        const tambahSpesifikasiBtn = document.getElementById('tambahSpesifikasi');
        const selectTipeMerk = document.getElementById('selectTipeMerk');

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
                return;
            }

            fetch(`/api/tipe-barang/tablet/${tipeBarangId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        const spesifikasi = JSON.parse(result.data);
                        populateSpesifikasi(spesifikasi);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Gagal memuat spesifikasi', 'error');
                });
        });

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