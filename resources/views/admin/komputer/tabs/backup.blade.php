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
                        @if($komputer->riwayat()->exists())
                            <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $komputer->id_barang }}"
                                    title="Musnahkan">
                                <i class="bi bi-trash-fill"></i> Musnah
                            </button>
                        @endif
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
                            <select class="form-select form-select-sm" name="id_lokasi" id="lokasi-select{{ $komputer->id_barang }}" required>
                                <option value=""><-- Pilih Lokasi --></option>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Departemen</label>
                            <select class="form-select form-select-sm" name="id_departemen" id="departemen-select{{ $komputer->id_barang }}" required>
                                <option value=""><-- Pilih Departemen --></option>
                                @foreach($departemen as $dep)
                                <option value="{{ $dep->id_departemen }}">{{ $dep->nama_departemen }}</option>
                                @endforeach
                            </select>
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lokasiSelect = document.getElementById('lokasi-select{{ $komputer->id_barang }}');
        const ipSearchInput = document.getElementById('ip-search-input{{ $komputer->id_barang }}');
        const ipAddressList = document.getElementById('ip-address-list{{ $komputer->id_barang }}');
        const clearSearchBtn = document.getElementById('clear-search{{ $komputer->id_barang }}');

        async function loadIpAddresses(lokasiId) {
            try {
                const response = await fetch(`/api/lokasi/${lokasiId}/ip-addresses`);
                const data = await response.json();
                ipAddressList.innerHTML = '';

                if (!data.ipHosts || data.ipHosts.length === 0) {
                    ipAddressList.innerHTML = '<li class="no-results">Tidak ada IP Address tersedia</li>';
                    return;
                }

                data.ipHosts.forEach(ipHost => {
                    const groupHeader = document.createElement('li');
                    groupHeader.textContent = ipHost.ip_host;
                    groupHeader.classList.add('ip-host-group');
                    ipAddressList.appendChild(groupHeader);

                    if (ipHost.ip_addresses && ipHost.ip_addresses.length > 0) {
                        ipHost.ip_addresses.forEach(ip => {
                            if (ip.status === 'Available') {
                                const listItem = document.createElement('li');
                                listItem.classList.add('ip-address-option');

                                const radio = document.createElement('input');
                                radio.type = 'radio';
                                radio.name = 'ip_address';
                                radio.value = ip.id_ip;
                                radio.id = `ip-${ip.id_ip}`;

                                const label = document.createElement('label');
                                label.setAttribute('for', `ip-${ip.id_ip}`);
                                label.textContent = ` ${ip.ip_address}`;
                                label.style.marginLeft = '10px';

                                listItem.appendChild(radio);
                                listItem.appendChild(label);
                                ipAddressList.appendChild(listItem);
                            }
                        });
                    } else {
                        const noIpItem = document.createElement('li');
                        noIpItem.textContent = '  Tidak ada IP tersedia';
                        noIpItem.classList.add('ip-address-option');
                        noIpItem.style.fontStyle = 'italic';
                        noIpItem.style.color = '#6c757d';
                        ipAddressList.appendChild(noIpItem);
                    }
                });

                filterIpAddresses('');
            } catch (error) {
                console.error('Error loading IP addresses:', error);
                ipAddressList.innerHTML = '<li class="no-results">Error loading IP addresses</li>';
            }
        }

        function filterIpAddresses(searchTerm) {
            let visibleOptions = 0;

            Array.from(ipAddressList.children).forEach(item => {
                if (item.classList.contains('ip-host-group')) return;

                const matches = item.textContent.toLowerCase().includes(searchTerm.toLowerCase());
                if (matches) {
                    item.style.display = 'flex';
                    visibleOptions++;
                } else {
                    item.style.display = 'none';
                }
            });

            const noResults = document.querySelector('.no-results');
            if (visibleOptions === 0) {
                if (!noResults) {
                    const noResultsItem = document.createElement('li');
                    noResultsItem.textContent = 'Tidak ditemukan IP Address yang sesuai';
                    noResultsItem.classList.add('no-results');
                    ipAddressList.appendChild(noResultsItem);
                }
            } else {
                if (noResults) {
                    noResults.remove();
                }
            }
        }

        lokasiSelect.addEventListener('change', function() {
            const lokasiId = this.value;
            
            // Reset IP search input
            ipSearchInput.value = '';
            
            if (lokasiId) {
                loadIpAddresses(lokasiId);
            } else {
                ipAddressList.innerHTML = '<li class="no-results">Pilih lokasi terlebih dahulu</li>';
            }
        });

        ipSearchInput.addEventListener('input', function() {
            filterIpAddresses(this.value.trim());
        });

        clearSearchBtn.addEventListener('click', function() {
            ipSearchInput.value = '';
            filterIpAddresses('');
            ipSearchInput.focus();
        });

        ipSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    });
</script>
@endforeach


