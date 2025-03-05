<div class="mb-3">
    <a href="{{ route('switch.create') }}" class="btn btn-primary btn-sm ms-2">
        <i class="bi bi-plus me-1"></i>Tambah Switch
    </a>
</div>
<div class="table-responsive">
    <table class="table table-sm small table-striped" id="backupTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Serial</th>
                <th>Tahun</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $switch)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $switch->model }}</td>
                <td>{{ $switch->tipe_merk }}</td>
                <td>{{ $switch->serial }}</td>
                <td>{{ \Carbon\Carbon::parse($switch->tahun_perolehan)->format('M Y') }}</td>
                <td data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ $switch->menuBackup->keterangan ?? "-" }}">
                    {{ Str::limit($switch->menuBackup->keterangan ?? "-", 50) }}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#aktivasiModal{{ $switch->id_barang }}">
                            <i class="bi bi-check-circle"></i> Aktif
                        </button>                        
                        <button type="button" class="btn btn-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $switch->id_barang }}"
                                {{ !$switch->riwayat()->exists() ? 'disabled' : '' }}
                                title="{{ !$switch->riwayat()->exists() ? 'Belum ada riwayat penggunaan barang ini' : 'Musnahkan' }}">
                            <i class="bi bi-trash-fill"></i> Musnah
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@foreach ( $data as $index => $switch )
{{-- Modal Aktivasi --}}
<div class="modal fade" id="aktivasiModal{{ $switch->id_barang }}" tabindex="-1" aria-labelledby="aktivasiModalLabel{{ $switch->id_barang }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aktivasiModalLabel{{ $switch->id_barang }}">Aktivasi Switch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('switch.aktivasi', $switch->id_barang) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Model</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $switch->model }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Tipe/Merk</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $switch->tipe_merk }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label col-form-label-sm">Spesifikasi</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    @php
                                        $spesifikasi = is_string($switch->spesifikasi) ? 
                                            json_decode($switch->spesifikasi, true) : 
                                            (is_array($switch->spesifikasi) ? $switch->spesifikasi : []);
                                    @endphp
                                    @if(count($spesifikasi) > 0)
                                        @foreach($spesifikasi as $key => $value)
                                        <tr>
                                            <td style="width: 40%; font-size:0.8rem;" class="text-dark bg-light">{{ $key }}</td>
                                            <td style="font-size:0.8rem;">{{ $value }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" style="font-size:0.8rem;"  class="text-center text-muted">Tidak ada spesifikasi</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Lokasi</label>
                            <div class="select-search-container">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="lokasi-search{{ $switch->id_barang }}" 
                                           placeholder="Cari dan pilih lokasi..." autocomplete="off">
                                    <button class="btn btn-secondary" type="button" id="clear-lokasi-search{{ $switch->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="id_lokasi" id="lokasi-value{{ $switch->id_barang }}" required>
                                <div class="select-search-dropdown" id="lokasi-dropdown{{ $switch->id_barang }}">
                                    @foreach($lokasi as $lok)
                                    <div class="select-search-option" data-value="{{ $lok->id_lokasi }}">
                                        {{ $lok->nama_lokasi }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Departemen</label>
                            <div class="select-search-container">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="departemen-search{{ $switch->id_barang }}" 
                                           placeholder="Cari dan pilih departemen..." autocomplete="off">
                                    <button class="btn btn-secondary" type="button" id="clear-departemen-search{{ $switch->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="id_departemen" id="departemen-value{{ $switch->id_barang }}" required>
                                <div class="select-search-dropdown" id="departemen-dropdown{{ $switch->id_barang }}">
                                    @foreach($departemen as $dep)
                                    <div class="select-search-option" data-value="{{ $dep->id_departemen }}">
                                        {{ $dep->nama_departemen }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Lokasi Switch</label>
                            <input type="text" class="form-control form-control-sm" name="lokasi_switch" placeholder="Contoh: R.MIS, R.Server, R.RND, dsb" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label col-form-label-sm">Node Bagus</label>
                            <input type="number" class="form-control form-control-sm" name="node_bagus" value="{{ $switch->node_bagus ?? 0 }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label col-form-label-sm">Node Terpakai</label>
                            <input type="number" class="form-control form-control-sm" name="node_terpakai" value="{{ $switch->node_terpakai ?? 0 }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label col-form-label-sm">Node Rusak</label>
                            <input type="number" class="form-control form-control-sm" name="node_rusak" value="{{ $switch->node_rusak ?? 0 }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label col-form-label-sm">Keterangan</label>
                        <textarea class="form-control form-control-sm" name="keterangan" rows="3" placeholder="Masukkan keterangan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Aktivasi</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Pemusnahan -->
<div class="modal fade" id="pemusnahanModal{{ $switch->id_barang }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pindahkan ke Pemusnahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('switch.musnah', $switch->id_barang) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label col-form-label-sm">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" placeholder="Masukkan keterangan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .select-search-container {
        position: relative;
    }
    .select-search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        display: none;
    }
    .select-search-dropdown.show {
        display: block;
    }
    .select-search-option {
        padding: 0.4rem;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .select-search-option:hover {
        background-color: #f8f9fa;
    }
    .select-search-selected {
        background-color: #e9ecef;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($data as $switch)
            setupSearchSelect(
                'lokasi-search{{ $switch->id_barang }}',
                'lokasi-dropdown{{ $switch->id_barang }}',
                'lokasi-value{{ $switch->id_barang }}',
                'clear-lokasi-search{{ $switch->id_barang }}'
            );
            
            setupSearchSelect(
                'departemen-search{{ $switch->id_barang }}',
                'departemen-dropdown{{ $switch->id_barang }}',
                'departemen-value{{ $switch->id_barang }}',
                'clear-departemen-search{{ $switch->id_barang }}'
            );
            
            setupIpAddressHandler('{{ $switch->id_barang }}');
        @endforeach
        
        // Setup search select functionality
        function setupSearchSelect(searchId, dropdownId, valueId, clearId) {
            const searchInput = document.getElementById(searchId);
            const dropdown = document.getElementById(dropdownId);
            const valueInput = document.getElementById(valueId);
            const clearButton = document.getElementById(clearId);
            const options = dropdown.getElementsByClassName('select-search-option');

            searchInput?.addEventListener('focus', () => dropdown.classList.add('show'));
            
            searchInput?.addEventListener('input', () => {
                const filter = searchInput.value.toLowerCase();
                let visible = 0;
                
                Array.from(options).forEach(option => {
                    const matches = option.textContent.toLowerCase().includes(filter);
                    option.style.display = matches ? '' : 'none';
                    if (matches) visible++;
                });
                
                // Tampilkan pesan jika tidak ada hasil
                if (visible === 0) {
                    // Hapus pesan 'tidak ada data' yang mungkin sudah ada
                    const existingNoData = dropdown.querySelector('.no-data-message');
                    if (existingNoData) existingNoData.remove();
                    
                    // Tambahkan pesan baru
                    const noDataMsg = document.createElement('div');
                    noDataMsg.className = 'select-search-option no-data-message';
                    noDataMsg.textContent = 'Tidak ada data yang cocok';
                    noDataMsg.style.fontStyle = 'italic';
                    noDataMsg.style.color = '#6c757d';
                    noDataMsg.style.textAlign = 'center';
                    dropdown.appendChild(noDataMsg);
                } else {
                    // Hapus pesan 'tidak ada data' jika ada
                    const existingNoData = dropdown.querySelector('.no-data-message');
                    if (existingNoData) existingNoData.remove();
                }
                
                dropdown.classList.add('show');
            });

            // Mencegah input manual dengan bantuan fungsi validasi
            searchInput?.addEventListener('blur', () => {
                setTimeout(() => {
                    if (!valueInput.value) {
                        searchInput.value = ''; // Kosongkan input jika tidak ada nilai yang dipilih
                    } else {
                        // Cari opsi yang nilai ID-nya cocok dengan value yang tersimpan
                        const selectedOption = Array.from(options).find(opt => opt.dataset.value === valueInput.value);
                        if (selectedOption) {
                            // Pastikan teks input sesuai dengan opsi yang dipilih
                            searchInput.value = selectedOption.textContent.trim();
                        }
                    }
                }, 200); // Berikan sedikit delay agar klik pada opsi bisa diproses
            });

            Array.from(options).forEach(option => {
                option.addEventListener('click', () => {
                    valueInput.value = option.dataset.value;
                    searchInput.value = option.textContent.trim();
                    dropdown.classList.remove('show');
                    
                    Array.from(options).forEach(opt => opt.classList.remove('select-search-selected'));
                    option.classList.add('select-search-selected');
                    
                    // Hapus pesan 'tidak ada data' jika ada
                    const existingNoData = dropdown.querySelector('.no-data-message');
                    if (existingNoData) existingNoData.remove();
                    
                    valueInput.dispatchEvent(new Event('change', {bubbles: true}));
                });
            });

            clearButton?.addEventListener('click', () => {
                searchInput.value = '';
                valueInput.value = '';
                Array.from(options).forEach(option => {
                    option.style.display = '';
                    option.classList.remove('select-search-selected');
                });
                
                // Hapus pesan 'tidak ada data' jika ada
                const existingNoData = dropdown.querySelector('.no-data-message');
                if (existingNoData) existingNoData.remove();
                
                dropdown.classList.remove('show');
                valueInput.dispatchEvent(new Event('change', {bubbles: true}));
            });

            document.addEventListener('click', ({target}) => {
                if (!searchInput?.contains(target) && 
                    !dropdown?.contains(target) && 
                    !clearButton?.contains(target)) {
                    dropdown?.classList.remove('show');
                    
                    // Validasi setelah dropdown ditutup
                    if (!valueInput.value) {
                        searchInput.value = ''; // Kosongkan input jika tidak ada nilai yang dipilih
                    }
                }
            });

            // Tambahkan validasi pada form submit
            const form = searchInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Pastikan lokasi dan departemen dipilih sebelum form di-submit
                    const lokasiValue = form.querySelector(`input[name="id_lokasi"]`);
                    const depValue = form.querySelector(`input[name="id_departemen"]`);
                    
                    if (!lokasiValue.value || !depValue.value) {
                        e.preventDefault();
                        
                        if (!lokasiValue.value) {
                            // Highlight input lokasi
                            const lokasiInput = form.querySelector(`input[id^="lokasi-search"]`);
                            if (lokasiInput) {
                                lokasiInput.classList.add('is-invalid');
                                // Tambahkan pesan error jika belum ada
                                if (!lokasiInput.nextElementSibling?.classList.contains('invalid-feedback')) {
                                    const errorMsg = document.createElement('div');
                                    errorMsg.className = 'invalid-feedback';
                                    errorMsg.textContent = 'Silakan pilih lokasi dari daftar';
                                    lokasiInput.insertAdjacentElement('afterend', errorMsg);
                                }
                            }
                        }
                        
                        if (!depValue.value) {
                            // Highlight input departemen
                            const depInput = form.querySelector(`input[id^="departemen-search"]`);
                            if (depInput) {
                                depInput.classList.add('is-invalid');
                                // Tambahkan pesan error jika belum ada
                                if (!depInput.nextElementSibling?.classList.contains('invalid-feedback')) {
                                    const errorMsg = document.createElement('div');
                                    errorMsg.className = 'invalid-feedback';
                                    errorMsg.textContent = 'Silakan pilih departemen dari daftar';
                                    depInput.insertAdjacentElement('afterend', errorMsg);
                                }
                            }
                        }
                        
                        // Tampilkan pesan alert
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Silakan pilih lokasi dan departemen dari daftar yang tersedia',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

            // Hapus class is-invalid saat input difokuskan
            searchInput?.addEventListener('focus', function() {
                this.classList.remove('is-invalid');
                const errorMsg = this.nextElementSibling?.classList.contains('invalid-feedback') ? 
                                this.nextElementSibling : null;
                if (errorMsg) errorMsg.remove();
            });
        }
    });
</script>
@endforeach