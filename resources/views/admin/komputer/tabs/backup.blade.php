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
                    title="{{ $komputer->keterangan ?? "-" }}">
                    {{ Str::limit($komputer->keterangan ?? "-", 50) }}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#aktivasiModal{{ $komputer->id_barang }}"
                                title="Aktivasi">
                            <i class="bi bi-check-circle"></i>
                            Aktif
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
<div class="modal fade" id="aktivasiModal{{ $komputer->id_barang }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aktivasi Komputer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('komputer.aktivasi', $komputer->id_barang) }}" method="POST">
                @csrf
                <input type="hidden" name="id_barang" id="aktivasi_id_barang">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label col-form-label-sm">Lokasi</label>
                            <select class="form-select form-select-sm" name="id_lokasi" required>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Departemen</label>
                            <select class="form-select form-select-sm" name="id_departemen" required>
                                @foreach($departemen as $dept)
                                    <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">IP Address</label>
                            <input type="text" id="ip-search-input" class="form-control form-control-sm form-control form-control-sm-sm mb-1" placeholder="Ketik untuk mencari IP Address">
                            <select class="form-select form-select-sm" name="ip_address">
                                @foreach($ipAddresses as $ip)
                                    <option value="{{ $ip->id_ip }}">{{ $ip->ip_address }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Komputer Name</label>
                            <input type="text" class="form-control form-control-sm" name="komputer_name" placeholder="Contoh: PC-001" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">User</label>
                            <input type="text" class="form-control form-control-sm" name="user" placeholder="Contoh: John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Kelayakan (%)</label>
                            <div class="d-flex align-items-center">
                                <input type="range" class="form-range form-range-sm me-2 mt-2" name="kelayakan" min="0" max="100" id="kelayakanRange" value="{{ old('kelayakan', $komputer->kelayakan) }}" required style="direction: ltr;">
                                <span id="kelayakanValue" class="fw-bold">{{ old('kelayakan', $komputer->kelayakan) }}</span>%
                            </div>
                        </div>
                        <script>
                            document.getElementById('kelayakanRange').addEventListener('input', function() {
                                document.getElementById('kelayakanValue').textContent = this.value;
                            });
                        </script>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Get the search input and select element
    const searchInput = document.getElementById('ip-search-input');
    const selectElement = document.querySelector('select[name="ip_address"]');

    // Add event listener for search input
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();

        // Loop through all options
        Array.from(selectElement.options).forEach(option => {
            const optionText = option.text.toLowerCase();
            
            // Show or hide options based on search term
            if (optionText.includes(searchTerm) || searchTerm === '') {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });

        // Open the select dropdown when typing
        if (selectElement.multiple) {
            selectElement.size = Math.min(selectElement.options.length, 10);
        } else {
            selectElement.size = Math.min(selectElement.options.length, 10);
            selectElement.click();
        }
    });

    // Close dropdown when an option is selected
    selectElement.addEventListener('change', function() {
        if (!selectElement.multiple) {
            selectElement.size = 1;
        }
    });

    // Prevent form submission when pressing enter in search input
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});
</script>
@endforeach