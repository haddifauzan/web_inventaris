<div class="card-body border-bottom col-md-12 mb-2">
    <form method="GET" action="{{ route('tablet.index', ['tab' => 'aktif']) }}" id="filterForm">
        <div class="row g-2">
            <!-- First Row -->
            <div class="col-md-3">
                <label for="lokasi_id" class="form-label">Lokasi</label>
                <select name="lokasi_id" id="lokasi_id" class="form-select form-select-sm">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasi as $lok)
                        <option value="{{ $lok->id_lokasi }}" {{ request('lokasi_id') == $lok->id_lokasi ? 'selected' : '' }}>
                            {{ $lok->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="departemen_id" class="form-label">Departemen</label>
                <select name="departemen_id" id="departemen_id" class="form-select form-select-sm">
                    <option value="">Semua Departemen</option>
                    @foreach($departemen as $dept)
                        <option value="{{ $dept->id_departemen }}" {{ request('departemen_id') == $dept->id_departemen ? 'selected' : '' }}>
                            {{ $dept->nama_departemen }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="tipe_merk" class="form-label">Tipe/Merk</label>
                <select name="tipe_merk" id="tipe_merk" class="form-select form-select-sm">
                    <option value="">Semua Tipe Merk</option>
                    @foreach($tipeMerk as $merk)
                        <option value="{{ $merk->tipe_merk }}" {{ request('tipe_merk') == $merk->tipe_merk ? 'selected' : '' }}>
                            {{ $merk->tipe_merk }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="tahun_perolehan" class="form-label">Tahun Perolehan</label>
                <select name="tahun_perolehan" id="tahun_perolehan" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @for($year = date('Y'); $year >= 2000; $year--)
                        <option value="{{ $year }}" {{ request('tahun_perolehan') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <!-- Second Row -->
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('tablet.index', ['tab' => 'aktif']) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="col-md-2 mb-3 ms-3">
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
        <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
    </button>
</div>

<div class="table-responsive">
    <table class="table table-sm small table-striped" id="activeTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Lokasi</th>
                <th>Departemen</th>
                <th>IP Address</th>
                <th>User</th>
                <th>Model</th>
                <th>Type/Merk</th>
                <th>Serial</th>
                <th>Tahun Perolehan</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $tablet)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tablet->menuAktif->first()->lokasi->nama_lokasi ?? '-' }}</td>
                <td>{{ $tablet->menuAktif->first()->departemen->nama_departemen ?? '-' }}</td>
                <td>{{ $tablet->menuAktif->first()->ipAddress->ip_address ?? '-' }}</td>
                <td>{{ $tablet->menuAktif->first()->user ?? '-' }}</td>
                <td>{{ $tablet->model }}</td>
                <td>{{ $tablet->tipe_merk ?? '-' }}</td>
                <td>{{ $tablet->serial }}</td>
                <td>{{ \Carbon\Carbon::parse($tablet->tahun_perolehan)->format('M Y') ?? '-' }}</td>
                <td>
                    @if ($tablet->status === 'Aktif')
                        Dipakai
                    @else
                        {{ $tablet->menuAktif->first()->status ?? '-' }}
                    @endif
                </td>
                <td title="{{ $tablet->menuAktif->first()->keterangan ?? '-' }}">
                    {{ Str::limit($tablet->menuAktif->first()->keterangan ?? '-', 50) }}
                </td><td>
                    <div class="btn-group">
                        <button type="button" title="Backup" class="btn btn-success btn-sm text-white d-flex" data-bs-toggle="modal" data-bs-target="#backupModal{{ $tablet->id_barang }}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                        </button>
                        <button type="button" title="Edit" class="btn btn-warning btn-sm d-flex" data-bs-toggle="modal" data-bs-target="#editAktivasiModal{{ $tablet->id_barang }}">
                            <i class="bi bi-pencil-fill text-white me-1"></i>
                        </button>
                        <button type="button" title="Musnahkan" class="btn btn-danger btn-sm d-flex" data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $tablet->id_barang }}">
                            <i class="bi bi-trash-fill text-white me-1"></i>
                        </button>
                    </div>

                    <!-- Modal Backup -->
                    <div class="modal fade" id="backupModal{{ $tablet->id_barang }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Kembalikan ke Backup</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('tablet.tobackup', $tablet->id_barang) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="keterangan" class="form-control" placeholder="Masukkan keterangan jika diperlukan"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Pemusnahan -->
                    <div class="modal fade" id="pemusnahanModal{{ $tablet->id_barang }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Pindahkan ke Pemusnahan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('tablet.topemusnahan', $tablet->id_barang) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="keterangan" class="form-control" placeholder="Masukkan keterangan jika diperlukan"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="{{ route('laporan.export-tablet-active') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Data Tablet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="periode" class="form-label">Periode</label>
                            <input type="month" class="form-control" id="periode" name="periode" value="{{ date('Y-m') }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="lokasi" class="form-label">Lokasi</label>
                            <select name="lokasi" id="lokasi" class="form-select">
                                <option value="">Semua Lokasi</option>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="departemen" class="form-label">Departemen</label>
                            <select name="departemen" id="departemen" class="form-select">
                                <option value="">Semua Departemen</option>
                                @foreach($departemen as $dept)
                                    <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($data as $index => $tablet)
{{-- Modal Edit Aktivasi --}}
<div class="modal fade" id="editAktivasiModal{{ $tablet->id_barang }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Aktivasi Tablet</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3" id="editTab{{ $tablet->id_barang }}" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="teknis-tab{{ $tablet->id_barang }}" data-bs-toggle="tab"
                data-bs-target="#teknis{{ $tablet->id_barang }}" type="button" role="tab">Edit Data Teknis</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pengguna-tab{{ $tablet->id_barang }}" data-bs-toggle="tab"
                data-bs-target="#pengguna{{ $tablet->id_barang }}" type="button" role="tab">Edit Aktivasi Baru</button>
            </li>
          </ul>
  
          <div class="tab-content" id="tabContent{{ $tablet->id_barang }}">
            {{-- Tab: Edit Data Teknis --}}
            <div class="tab-pane fade show active" id="teknis{{ $tablet->id_barang }}" role="tabpanel">
              <form action="{{ route('tablet.update.teknis', $tablet->id_barang) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.tablet.partials.edit_teknis_fields', ['tablet' => $tablet, 'lokasi' => $lokasi, 'departemen' => $departemen])
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  <button type="submit" class="btn btn-primary">Update Teknis</button>
                </div>
              </form>
            </div>
  
            {{-- Tab: Edit Pengguna --}}
            <div class="tab-pane fade" id="pengguna{{ $tablet->id_barang }}" role="tabpanel">
              <form action="{{ route('tablet.update.aktivasi', $tablet->id_barang) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.tablet.partials.edit_full_fields', ['tablet' => $tablet, 'lokasi' => $lokasi, 'departemen' => $departemen])
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  <button type="submit" class="btn btn-primary">Simpan Aktivasi Baru</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach

  <style>
    .ip-selection-container {
        position: relative;
    }

    .ip-list-container {
        border: 1px solid #ced4da;
        border-radius: 5px;
        max-height: 150px;
        overflow-y: auto;
        background: white;
        width: 100%;
    }

    .ip-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ip-list li {
        padding: 5px 10px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #ddd;
    }

    .ip-list li:last-child {
        border-bottom: none;
    }

    .ip-host-group {
        font-weight: bold;
        background-color: #f8f9fa;
        padding: 5px;
        color: #495057;
        cursor: default;
    }

    .ip-address-option {
        padding-left: 15px;
    }

    .no-results {
        padding: 10px;
        color: #6c757d;
        text-align: center;
        font-style: italic;
    }

    .select-search-container {
        position: relative;
        width: 100%;
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: none;
        width: 100%;
    }

    .select-search-dropdown.show {
        display: block !important;
    }

    .select-search-option {
        padding: 0.4rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background-color 0.2s;
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
        @foreach($data as $tablet)
            // Tab teknis
            setupSearchSelect(
                'teknis-lokasi-search{{ $tablet->id_barang }}',
                'teknis-lokasi-dropdown{{ $tablet->id_barang }}',
                'teknis-lokasi-value{{ $tablet->id_barang }}',
                'teknis-clear-lokasi-search{{ $tablet->id_barang }}'
            );
            
            setupSearchSelect(
                'teknis-departemen-search{{ $tablet->id_barang }}',
                'teknis-departemen-dropdown{{ $tablet->id_barang }}',
                'teknis-departemen-value{{ $tablet->id_barang }}',
                'teknis-clear-departemen-search{{ $tablet->id_barang }}'
            );
            
            setupIpAddressHandler(
                'teknis', 
                '{{ $tablet->id_barang }}', 
                'teknis-lokasi-value{{ $tablet->id_barang }}',
                'teknis-ip-search-input{{ $tablet->id_barang }}',
                'teknis-ip-address-list{{ $tablet->id_barang }}',
                'teknis-clear-ip-search{{ $tablet->id_barang }}'
            );
            
            // Tab aktivasi
            setupSearchSelect(
                'aktivasi-lokasi-search{{ $tablet->id_barang }}',
                'aktivasi-lokasi-dropdown{{ $tablet->id_barang }}',
                'aktivasi-lokasi-value{{ $tablet->id_barang }}',
                'aktivasi-clear-lokasi-search{{ $tablet->id_barang }}'
            );
            
            setupSearchSelect(
                'aktivasi-departemen-search{{ $tablet->id_barang }}',
                'aktivasi-departemen-dropdown{{ $tablet->id_barang }}',
                'aktivasi-departemen-value{{ $tablet->id_barang }}',
                'aktivasi-clear-departemen-search{{ $tablet->id_barang }}'
            );
            
            setupIpAddressHandler(
                'aktivasi', 
                '{{ $tablet->id_barang }}', 
                'aktivasi-lokasi-value{{ $tablet->id_barang }}',
                'aktivasi-ip-search-input{{ $tablet->id_barang }}',
                'aktivasi-ip-address-list{{ $tablet->id_barang }}',
                'aktivasi-clear-ip-search{{ $tablet->id_barang }}'
            );
            
            // Setup IP Address current handler
            setupIpAddressHandler(
                'aktivasi', 
                '{{ $tablet->id_barang }}', 
                'aktivasi-lokasi-value{{ $tablet->id_barang }}',
                'aktivasi-ip-search-input{{ $tablet->id_barang }}',
                'aktivasi-ip-address-list{{ $tablet->id_barang }}',
                'aktivasi-clear-ip-search{{ $tablet->id_barang }}'
            );
        @endforeach
        
        // Setup search select functionality
        function setupSearchSelect(searchId, dropdownId, valueId, clearId) {
            const searchInput = document.getElementById(searchId);
            const dropdown = document.getElementById(dropdownId);
            const valueInput = document.getElementById(valueId);
            const clearButton = document.getElementById(clearId);
            
            if (!searchInput || !dropdown || !valueInput || !clearButton) return;
            
            const options = dropdown.getElementsByClassName('select-search-option');

            searchInput.addEventListener('focus', () => dropdown.classList.add('show'));
            
            searchInput.addEventListener('input', () => {
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
            searchInput.addEventListener('blur', () => {
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

            clearButton.addEventListener('click', () => {
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

            document.addEventListener('click', (event) => {
                const target = event.target;
                if (!searchInput.contains(target) && 
                    !dropdown.contains(target) && 
                    !clearButton.contains(target)) {
                    dropdown.classList.remove('show');
                    
                    // Validasi setelah dropdown ditutup
                    if (!valueInput.value) {
                        searchInput.value = ''; // Kosongkan input jika tidak ada nilai yang dipilih
                    }
                }
            });

            // Tambahkan validasi pada form submit
            const form = searchInput.closest('form');
            if (form && !form.querySelector('[id^="teknis"]')) {  // Only for non-teknis forms
                form.addEventListener('submit', function(e) {
                    // Pastikan lokasi dan departemen dipilih sebelum form di-submit
                    const lokasiValue = form.querySelector(`input[name="id_lokasi"]`);
                    const depValue = form.querySelector(`input[name="id_departemen"]`);
                    
                    if (!lokasiValue.value || !depValue.value) {
                        e.preventDefault();
                        
                        if (!lokasiValue.value) {
                            const lokasiInput = document.getElementById(
                                `aktivasi-lokasi-search${lokasiValue.id.replace('aktivasi-lokasi-value', '')}`
                            );
                            if (lokasiInput) {
                                lokasiInput.classList.add('is-invalid');
                                if (!lokasiInput.nextElementSibling?.classList.contains('invalid-feedback')) {
                                    const errorMsg = document.createElement('div');
                                    errorMsg.className = 'invalid-feedback';
                                    errorMsg.textContent = 'Silakan pilih lokasi dari daftar';
                                    lokasiInput.insertAdjacentElement('afterend', errorMsg);
                                }
                            }
                        }
                        
                        if (!depValue.value) {
                            const depInput = document.getElementById(
                                `aktivasi-departemen-search${depValue.id.replace('aktivasi-departemen-value', '')}`
                            );
                            if (depInput) {
                                depInput.classList.add('is-invalid');
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
            searchInput.addEventListener('focus', function() {
                this.classList.remove('is-invalid');
                const errorMsg = this.nextElementSibling?.classList.contains('invalid-feedback') ? 
                                this.nextElementSibling : null;
                if (errorMsg) errorMsg.remove();
            });
        }

        // Setup IP address handling
        function setupIpAddressHandler(prefix, id, lokasiValueId, ipSearchId, ipListId, clearSearchId) {
            const lokasiValue = document.getElementById(lokasiValueId);
            const ipSearch = document.getElementById(ipSearchId);
            const ipList = document.getElementById(ipListId);
            const clearSearch = document.getElementById(clearSearchId);

            if (!lokasiValue || !ipSearch || !ipList || !clearSearch) return;

            // For teknis tab, we need to initialize with current IP
            if (prefix === 'teknis' && lokasiValue.value) {
                loadIpAddresses(lokasiValue.value);
            }

            // Add MutationObserver to lokasi value field to detect attribute changes
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.attributeName === 'value') {
                        const lokasiId = lokasiValue.value;
                        lokasiId ? loadIpAddresses(lokasiId) : 
                                showNoResults('Pilih lokasi terlebih dahulu');
                    }
                });
            });
            
            try {
                observer.observe(lokasiValue, {attributes: true});
            } catch (e) {
                console.error('Failed to observe value attribute:', e);
            }

            // Also listen for change events on the lokasi value field
            lokasiValue.addEventListener('change', () => {
                const lokasiId = lokasiValue.value;
                lokasiId ? loadIpAddresses(lokasiId) : 
                        showNoResults('Pilih lokasi terlebih dahulu');
            });

            // Setup search filtering for IP addresses
            ipSearch.addEventListener('input', function() {
                filterIpAddresses(this.value.toLowerCase());
            });

            // Clear search button
            clearSearch.addEventListener('click', function() {
                ipSearch.value = '';
                filterIpAddresses('');
            });

            async function loadIpAddresses(lokasiId) {
                try {
                    showNoResults('Memuat data IP...');
                    const response = await fetch(`/api/lokasi/${lokasiId}/ip-addresses`);
                    if (!response.ok) {
                        throw new Error(`Network response error: ${response.status}`);
                    }
                    const data = await response.json();
                    
                    if (!data.ipHosts?.length) {
                        showNoResults('Tidak ada IP Address tersedia');
                        return;
                    }

                    renderIpAddresses(data.ipHosts);
                    filterIpAddresses('');
                } catch (error) {
                    console.error('Error loading IP addresses:', error);
                    showNoResults('Error loading IP addresses');
                }
            }

            function showNoResults(message) {
                ipList.innerHTML = `<li class="no-results">${message}</li>`;
            }

            function renderIpAddresses(ipHosts) {
                ipList.innerHTML = '';
                
                ipHosts.forEach(host => {
                    const groupHeader = createGroupHeader(host.ip_host);
                    ipList.appendChild(groupHeader);

                    const availableIps = host.ip_addresses?.filter(ip => ip.status === 'Available') || [];
                    
                    if (availableIps.length) {
                        availableIps.forEach(ip => {
                            ipList.appendChild(createIpOption(ip, id));
                        });
                    } else {
                        ipList.appendChild(createNoIpMessage());
                    }
                });
            }

            function filterIpAddresses(searchTerm) {
                const items = Array.from(ipList.children);
                let visibleCount = 0;

                items.forEach(item => {
                    if (item.classList.contains('ip-address-option')) {
                        const matches = item.textContent.toLowerCase().includes(searchTerm.toLowerCase());
                        item.style.display = matches ? '' : 'none';
                        if (matches) visibleCount++;
                    } else if (item.classList.contains('ip-host-group')) {
                        const hasVisibleChild = hasVisibleChildren(item);
                        item.style.display = hasVisibleChild ? '' : 'none';
                    }
                });

                if (!visibleCount) {
                    showNoResults(searchTerm ? 
                        'Tidak ditemukan IP Address yang sesuai' : 
                        'Tidak ada IP Address tersedia'
                    );
                }
            }

            ipSearch.addEventListener('input', e => filterIpAddresses(e.target.value.trim()));
            clearSearch.addEventListener('click', () => {
                ipSearch.value = '';
                filterIpAddresses('');
                ipSearch.focus();
            });
            ipSearch.addEventListener('keydown', e => {
                if (e.key === 'Enter') e.preventDefault();
            });
        }
        function createGroupHeader(text) {
            const header = document.createElement('li');
            header.textContent = text;
            header.classList.add('ip-host-group');
            return header;
        }

        function createIpOption(ip, computerId) {
            const item = document.createElement('li');
            item.classList.add('ip-address-option');
            
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'ip_address';
            radio.value = ip.id_ip;
            radio.id = `ip-${ip.id_ip}-${computerId}`;

            const label = document.createElement('label');
            label.htmlFor = radio.id;
            label.textContent = ` ${ip.ip_address}`;
            label.style.marginLeft = '10px';

            item.append(radio, label);
            return item;
        }

        function createNoIpMessage() {
            const item = document.createElement('li');
            item.textContent = '  Tidak ada IP tersedia';
            item.classList.add('ip-address-option');
            item.style.fontStyle = 'italic';
            item.style.color = '#6c757d';
            return item;
        }

        function hasVisibleChildren(element) {
            let next = element.nextElementSibling;
            while (next && next.classList.contains('ip-address-option')) {
                if (next.style.display !== 'none') return true;
                next = next.nextElementSibling;
            }
            return false;
        }
    });
</script>   

    