<div class="mb-3">
    <a href="{{ route('komputer.create') }}" class="btn btn-primary btn-sm ms-2">
        <i class="bi bi-plus me-1"></i>Tambah Komputer
    </a>
</div>
<div class="table-responsive">
    <table class="table table-sm small table-striped" id="backupTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Operating Sistem</th>
                <th>Serial</th>
                <th>Kepemilikan</th>
                <th>Tahun</th>
                <th class="text-start">Kelayakan</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $komputer)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $komputer->model }}</td>
                <td>{{ $komputer->tipe_merk }}</td>
                <td>{{ $komputer->operating_system }}</td>
                <td>
                    @if (json_decode($komputer->serial))
                        <b><i>CPU:</i></b> {{ json_decode($komputer->serial)->cpu }}<br>
                        <b><i>Monitor:</i></b> {{ json_decode($komputer->serial)->monitor }}
                    @else
                        {{ $komputer->serial ?? '-' }}
                    @endif
                </td>
                <td>
                    @if ($komputer->kepemilikan === 'Inventaris')
                        <span class="badge bg-info">{{ $komputer->kepemilikan }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $komputer->kepemilikan }}</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($komputer->tahun_perolehan)->format('M Y') }}</td>
                <td>
                    <div class="progress" style="height: 12px; width: 100px;">
                        <div 
                            class="progress-bar 
                                {{ 
                                    $komputer->kelayakan >= 75 ? 'bg-success' :
                                    ($komputer->kelayakan >= 50 ? 'bg-warning' : 'bg-danger')
                                }}"
                            role="progressbar" 
                            aria-valuenow="{{ $komputer->kelayakan ?? 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100"
                            style="width: {{ $komputer->kelayakan ?? 0 }}%">
                            {{ $komputer->kelayakan ?? '-' }}%
                        </div>
                    </div>
                </td>
                <td data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ $komputer->menuBackup->keterangan ?? "-" }}">
                    {{ Str::limit($komputer->menuBackup->keterangan ?? "-", 50) }}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#aktivasiModal{{ $komputer->id_barang }}">
                            <i class="bi bi-check-circle"></i> Aktif
                        </button>                        
                        <button type="button" class="btn btn-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $komputer->id_barang }}"
                                {{ !$komputer->riwayat()->exists() ? 'disabled' : '' }}
                                title="{{ !$komputer->riwayat()->exists() ? 'Belum ada riwayat penggunaan barang ini' : 'Musnahkan' }}">
                            <i class="bi bi-trash-fill"></i> Musnah
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@foreach ( $data as $index => $komputer )
{{-- Modal Aktivasi --}}
<div class="modal fade" id="aktivasiModal{{ $komputer->id_barang }}" tabindex="-1" aria-labelledby="aktivasiModalLabel{{ $komputer->id_barang }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aktivasiModalLabel{{ $komputer->id_barang }}">Aktivasi Komputer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('komputer.aktivasi', $komputer->id_barang) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Model</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $komputer->model }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Tipe/Merk</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $komputer->tipe_merk }}" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Lokasi</label>
                            <div class="select-search-container">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="lokasi-search{{ $komputer->id_barang }}" 
                                           placeholder="Cari dan pilih lokasi..." autocomplete="off">
                                    <button class="btn btn-secondary" type="button" id="clear-lokasi-search{{ $komputer->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="id_lokasi" id="lokasi-value{{ $komputer->id_barang }}" required>
                                <div class="select-search-dropdown" id="lokasi-dropdown{{ $komputer->id_barang }}">
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
                                    <input type="text" class="form-control" id="departemen-search{{ $komputer->id_barang }}" 
                                           placeholder="Cari dan pilih departemen..." autocomplete="off">
                                    <button class="btn btn-secondary" type="button" id="clear-departemen-search{{ $komputer->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="id_departemen" id="departemen-value{{ $komputer->id_barang }}" required>
                                <div class="select-search-dropdown" id="departemen-dropdown{{ $komputer->id_barang }}">
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
                            <label class="form-label col-form-label-sm">User</label>
                            <input type="text" class="form-control form-control-sm" name="user" placeholder="Contoh: John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Komputer Name</label>
                            <input type="text" class="form-control form-control-sm" name="komputer_name" placeholder="Contoh: PC-001" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">IP Address</label>
                            <div class="ip-selection-container">
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" id="ip-search-input{{ $komputer->id_barang }}" class="form-control" placeholder="Cari IP Address...">
                                    <button class="btn btn-secondary" type="button" id="clear-search{{ $komputer->id_barang }}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <div class="ip-list-container">
                                    <ul id="ip-address-list{{ $komputer->id_barang }}" class="ip-list">
                                        <li class="no-results">Pilih lokasi terlebih dahulu</li>
                                    </ul>
                                </div>
                            </div>
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
<div class="modal fade" id="pemusnahanModal{{ $komputer->id_barang }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pindahkan ke Pemusnahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('komputer.musnah', $komputer->id_barang) }}" method="POST">
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
        @foreach($data as $komputer)
        setupSearchSelect(
            'lokasi-search{{ $komputer->id_barang }}',
            'lokasi-dropdown{{ $komputer->id_barang }}',
            'lokasi-value{{ $komputer->id_barang }}',
            'clear-lokasi-search{{ $komputer->id_barang }}'
        );
        
        setupSearchSelect(
            'departemen-search{{ $komputer->id_barang }}',
            'departemen-dropdown{{ $komputer->id_barang }}',
            'departemen-value{{ $komputer->id_barang }}',
            'clear-departemen-search{{ $komputer->id_barang }}'
        );
        
        setupIpAddressHandler('{{ $komputer->id_barang }}');
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
@endforeach