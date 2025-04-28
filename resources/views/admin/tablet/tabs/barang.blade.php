<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <a href="{{ route('tablet.create') }}" class="btn btn-primary btn-sm ms-2">
                <i class="bi bi-plus me-1"></i>Tambah Tablet
            </a>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-2">
            <select id="statusFilter" class="form-select">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Backup">Backup</option>
                <option value="Baru">Baru</option>
                <option value="Pemusnahan">Pemusnahan</option>
            </select>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-sm small table-striped" id="tabletTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Serial</th>
                <th>Spesifikasi</th>
                <th>Tahun Perolehan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $tablet)
            <tr id="{{ $tablet->serial }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $tablet->model }}</td>
                <td>{{ $tablet->tipe_merk }}</td>
                <td>{{ $tablet->serial }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary text-white" 
                            data-bs-toggle="modal" data-bs-target="#spesifikasiModal{{ $tablet->id_barang }}">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </td>
                <td>{{ \Carbon\Carbon::parse($tablet->tahun_perolehan)->format('M Y') }}</td>
                <td>
                    @if ($tablet->status === 'Backup')
                        <span class="badge bg-success">{{ $tablet->status}}</span>
                    @elseif ($tablet->status === 'Aktif')
                        <span class="badge bg-primary">{{ $tablet->status}}</span>
                    @elseif ($tablet->status === 'Baru')
                        <span class="badge bg-info">{{ $tablet->status}}</span>
                    @else
                        <span class="badge bg-danger">{{ $tablet->status}}</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#aktivasiModal{{ $tablet->id_barang }}"
                                {{ $tablet->status !== 'Backup' && $tablet->status !== 'Baru' ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle"></i>
                        </button>   
                        <a href="{{ route('tablet.edit', $tablet->id_barang) }}" 
                           class="btn btn-warning btn-sm text-white {{ $tablet->status === 'Pemusnahan' ? 'disabled' : '' }}"
                           title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#hapusModal{{ $tablet->id_barang }}"
                                title="Hapus">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Serial -->
@foreach($data as $index => $tablet)
    <!-- Modal Spesifikasi -->
    <div class="modal fade" id="spesifikasiModal{{ $tablet->id_barang }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Spesifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled mb-0">
                        @if(is_string($tablet->spesifikasi))
                            @foreach(json_decode($tablet->spesifikasi, true) as $key => $value)
                                <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                            @endforeach
                        @else
                            <li>-</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Spesifikasi -->

    <!-- Modal Hapus -->
    <div class="modal fade" id="hapusModal{{ $tablet->id_barang }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('tablet.destroy', $tablet->id_barang) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var table = new DataTable('#tabletTable', {
            searching: true,
            paging: true,
            info: true,
            ordering: false,
            language: {
            searchPlaceholder: "Cari Serial..."
            }
        });

        var statusFilter = document.getElementById('statusFilter');
        var searchInput = document.getElementById('searchSerial');

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var statusValue = statusFilter.value.toLowerCase();
            var statusText = data[6].toLowerCase(); // Index kolom ke-7 (mulai dari 0)

            return statusValue === '' || statusText.includes(statusValue);
        });

        statusFilter.addEventListener('change', function() {
            table.draw();
        });

        document.querySelectorAll('.clear-search').forEach(function(button) {
            button.addEventListener('click', function() {
                searchInput.value = '';
                table.search('').draw();
            });
        });

        const loadingContainer = document.getElementById('loading-container');

        setTimeout(() => {
            if (loadingContainer) {
                loadingContainer.classList.add('d-none');
            }
            document.getElementById('tabletTable').style.visibility = 'visible';

            const urlParams = new URLSearchParams(window.location.search);
            const searchResult = urlParams.get('search');

            if (searchResult) {
                table.search(searchResult).draw();

                setTimeout(() => {
                    const row = document.querySelector(`#tabletTable tbody tr[id="${searchResult}"]`);
                    if (row) {
                        window.scrollTo({
                            top: row.offsetTop - 100,
                            behavior: 'smooth'
                        });

                        row.classList.add('table-warning');
                        setTimeout(() => row.classList.remove('table-warning'), 3000);
                    }
                }, 500);
            }
        }, 100);
    });
</script>

@foreach ( $data as $index => $tablet )
{{-- Modal Aktivasi --}}
<div class="modal fade" id="aktivasiModal{{ $tablet->id_barang }}" tabindex="-1" aria-labelledby="aktivasiModalLabel{{ $tablet->id_barang }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aktivasiModalLabel{{ $tablet->id_barang }}">Aktivasi Tablet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tablet.aktivasi', $tablet->id_barang) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Model</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $tablet->model }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Tipe/Merk</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $tablet->tipe_merk }}" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Lokasi</label>
                            <div class="select-search-container">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="lokasi-search{{ $tablet->id_barang }}" 
                                           placeholder="Cari dan pilih lokasi..." autocomplete="off">
                                    <button class="btn btn-secondary" type="button" id="clear-lokasi-search{{ $tablet->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="id_lokasi" id="lokasi-value{{ $tablet->id_barang }}" required>
                                <div class="select-search-dropdown" id="lokasi-dropdown{{ $tablet->id_barang }}">
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
                                    <input type="text" class="form-control" id="departemen-search{{ $tablet->id_barang }}" 
                                           placeholder="Cari dan pilih departemen..." autocomplete="off">
                                    <button class="btn btn-secondary" type="button" id="clear-departemen-search{{ $tablet->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="id_departemen" id="departemen-value{{ $tablet->id_barang }}" required>
                                <div class="select-search-dropdown" id="departemen-dropdown{{ $tablet->id_barang }}">
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
                            <label class="form-label col-form-label-sm">IP Address</label>
                            <div class="ip-selection-container">
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" id="ip-search-input{{ $tablet->id_barang }}" class="form-control" placeholder="Cari IP Address...">
                                    <button class="btn btn-secondary" type="button" id="clear-search{{ $tablet->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <div class="ip-list-container">
                                    <ul id="ip-address-list{{ $tablet->id_barang }}" class="ip-list">
                                        <li class="no-results">Pilih lokasi terlebih dahulu</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">User</label>
                            <input type="text" class="form-control form-control-sm" name="user" placeholder="Contoh: John Doe" required>
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

<style>
    .ip-selection-container {
        position: relative;
    }

    .ip-list-container {
        border: 1px solid #ced4da;
        border-radius: 5px;
        max-height: 150px; /* Batasi tinggi agar tidak memanjang ke bawah */
        overflow-y: auto; /* Aktifkan scroll hanya dalam batas ini */
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
        @foreach($data as $tablet)
        setupSearchSelect(
            'lokasi-search{{ $tablet->id_barang }}',
            'lokasi-dropdown{{ $tablet->id_barang }}',
            'lokasi-value{{ $tablet->id_barang }}',
            'clear-lokasi-search{{ $tablet->id_barang }}'
        );
        
        setupSearchSelect(
            'departemen-search{{ $tablet->id_barang }}',
            'departemen-dropdown{{ $tablet->id_barang }}',
            'departemen-value{{ $tablet->id_barang }}',
            'clear-departemen-search{{ $tablet->id_barang }}'
        );
        
        setupIpAddressHandler('{{ $tablet->id_barang }}');
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

        // Setup IP address handling
        function setupIpAddressHandler(id) {
            const lokasiValue = document.getElementById(`lokasi-value${id}`);
            const ipSearch = document.getElementById(`ip-search-input${id}`);
            const ipList = document.getElementById(`ip-address-list${id}`);
            const clearSearch = document.getElementById(`clear-search${id}`);

            if (!lokasiValue || !ipSearch || !ipList || !clearSearch) return;

            new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.attributeName === 'value') {
                        const lokasiId = lokasiValue.value;
                        lokasiId ? loadIpAddresses(lokasiId) : 
                                 showNoResults('Pilih lokasi terlebih dahulu');
                    }
                });
            }).observe(lokasiValue, {attributes: true});

            lokasiValue.addEventListener('change', () => {
                const lokasiId = lokasiValue.value;
                lokasiId ? loadIpAddresses(lokasiId) : 
                         showNoResults('Pilih lokasi terlebih dahulu');
            });

            async function loadIpAddresses(lokasiId) {
                try {
                    showNoResults('Memuat data IP...');
                    const response = await fetch(`/api/lokasi/${lokasiId}/ip-addresses`);
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

        function createIpOption(ip, tabletId) {
            const item = document.createElement('li');
            item.classList.add('ip-address-option');
            
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'ip_address';
            radio.value = ip.id_ip;
            radio.id = `ip-${ip.id_ip}-${tabletId}`;

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
@endforeach