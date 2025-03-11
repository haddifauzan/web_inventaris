<div class="card-body border-bottom col-md-12 mb-2">
    <form method="GET" action="{{ route('switch.index', ['tab' => 'aktif']) }}" id="filterForm">
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
                <a href="{{ route('switch.index', ['tab' => 'aktif']) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>
<div class="col-md-3 mb-3 ms-3">
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
                <th>Lokasi Switch</th>
                <th>Type/Merk</th>
                <th>Tahun Perolehan</th>
                <th>Node Terpakai</th>
                <th>Node Bagus</th>
                <th>Node Rusak</th>
                <th>Node Kosong</th>
                <th>Status Net</th>
                <th>Tanggal Maintenance</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $switch)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $switch->menuAktif->first()->lokasi->nama_lokasi ?? '-' }}</td>
                <td>{{ $switch->menuAktif->first()->departemen->nama_departemen ?? '-' }}</td>
                <td>{{ $switch->maintenance->first()->lokasi_switch ?? '-' }}</td>
                <td>{{ $switch->tipe_merk ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($switch->tahun_perolehan)->format('M Y') ?? '-' }}</td>
                <td>{{ $switch->menuAktif->first()->node_terpakai ?? 0 }}</td>
                <td>{{ $switch->menuAktif->first()->node_bagus ?? 0 }}</td>
                <td>{{ $switch->menuAktif->first()->node_rusak ?? 0 }}</td>
                <td>{{ ($switch->menuAktif->first()->node_bagus ?? 0) - ($switch->menuAktif->first()->node_terpakai ?? 0) }}</td>
                <td>
                    @if($switch->maintenance->first()->status_net === 'OK')
                        <span class="badge bg-success">OK</span>
                    @elseif($switch->maintenance->first()->status_net === 'Rusak')
                        <span class="badge bg-danger">Rusak</span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $switch->maintenance->first()->tgl_maintenance ? \Carbon\Carbon::parse($switch->maintenance->first()->tgl_maintenance)->locale('id')->isoFormat('dddd, DD-MMMM-Y') : '-' }}</td>
                <td title="{{ $switch->menuAktif->first()->keterangan ?? '-' }}">
                    {{ Str::limit($switch->menuAktif->first()->keterangan ?? '-', 50) }}
                </td><td>
                    <div class="btn-group">
                        <button type="button" title="Backup" class="btn btn-success btn-sm text-white d-flex" data-bs-toggle="modal" data-bs-target="#backupModal{{ $switch->id_barang }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                        <button type="button" title="Maintenance" class="btn btn-warning btn-sm text-white d-flex" data-bs-toggle="modal" data-bs-target="#maintenanceModal{{ $switch->id_maintenance }}">
                            <i class="bi bi-tools"></i>
                        </button>
                        <button type="button" title="Musnah" class="btn btn-danger btn-sm d-flex" data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $switch->id_barang }}">
                            <i class="bi bi-trash-fill text-white"></i>
                        </button>
                    </div>

                    <!-- Modal Backup -->
                    <div class="modal fade" id="backupModal{{ $switch->id_barang }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Kembalikan ke Backup</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('switch.tobackup', $switch->id_barang) }}" method="POST">
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
                    <div class="modal fade" id="pemusnahanModal{{ $switch->id_barang }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Pindahkan ke Pemusnahan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('switch.topemusnahan', $switch->id_barang) }}" method="POST">
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

@foreach($data as $index => $maintenance)
<div class="modal fade" id="maintenanceModal{{ $maintenance->id_maintenance }}" tabindex="-1" aria-labelledby="maintenanceModalLabel{{ $maintenance->id_maintenance }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceModalLabel{{ $maintenance->id_maintenance }}">Form Maintenance - {{ $maintenance->tipe_merk . " (" . $maintenance->lokasi_switch . ")"}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('switch.maintenance.action', $maintenance->id_maintenance) }}" method="POST">
                @csrf
                <input type="hidden" name="id_barang" value="{{ $maintenance->id_barang }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Model</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $maintenance->model }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Tipe/Merk</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $maintenance->tipe_merk }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label col-form-label-sm">Spesifikasi</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    @php
                                        $spesifikasi = is_string($maintenance->spesifikasi) ? 
                                            json_decode($maintenance->spesifikasi, true) : 
                                            (is_array($maintenance->spesifikasi) ? $maintenance->spesifikasi : []);
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
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small">Tanggal Maintenance</label>
                            <input type="date" name="tgl_maintenance" class="form-control form-control-sm" 
                                   value="{{ date('Y-m-d') }}" 
                                   id="maintenance-date-{{ $maintenance->id_maintenance }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small">Lokasi Switch</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $maintenance->lokasi_switch }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label small">Status Jaringan</label>
                                <select name="status_net" class="form-select form-select-sm">
                                    <option value="OK" {{ $maintenance->status_net === 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="Rusak" {{ $maintenance->status_net === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Terpakai</label>
                            <input type="number" name="node_terpakai" class="form-control form-control-sm node-terpakai" 
                                   value="{{ $maintenance->node_terpakai }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                            <div class="invalid-feedback">
                                Node terpakai tidak boleh lebih besar dari node bagus
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Bagus</label>
                            <input type="number" name="node_bagus" class="form-control form-control-sm node-bagus" 
                                   value="{{ $maintenance->node_bagus }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Rusak</label>
                            <input type="number" name="node_rusak" class="form-control form-control-sm" 
                                   value="{{ $maintenance->node_rusak }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
                        <script>
                            document.querySelectorAll('.node-terpakai, .node-bagus').forEach(input => {
                                input.addEventListener('input', function() {
                                    const nodeBagus = parseInt(this.closest('.row').querySelector('.node-bagus').value) || 0;
                                    const nodeTerpakai = parseInt(this.closest('.row').querySelector('.node-terpakai').value) || 0;
                                    const nodeTerpakaiInput = this.closest('.row').querySelector('.node-terpakai');
                                    
                                    if (nodeTerpakai > nodeBagus) {
                                        nodeTerpakaiInput.classList.add('is-invalid');
                                    } else {
                                        nodeTerpakaiInput.classList.remove('is-invalid');
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Maintenance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Filter Report Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('export.switch-maintenance') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="tanggalAwal" class="form-label small">Tanggal Awal</label>
                            <input type="date" class="form-control form-control-sm" id="tanggalAwal" name="tanggal_awal" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="tanggalAkhir" class="form-label small">Tanggal Akhir</label>
                            <input type="date" class="form-control form-control-sm" id="tanggalAkhir" name="tanggal_akhir" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <script>
                        document.getElementById('tanggalAwal').addEventListener('change', function() {
                            document.getElementById('tanggalAkhir').min = this.value;
                        });
                        
                        document.getElementById('tanggalAkhir').addEventListener('change', function() {
                            if (this.value < document.getElementById('tanggalAwal').value) {
                                this.value = document.getElementById('tanggalAwal').value;
                            }
                        });
                    </script>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="lokasi" class="form-label small">Lokasi</label>
                            <select name="lokasi" id="lokasi" class="form-select form-select-sm">
                                <option value="">Semua Lokasi</option>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="departemen" class="form-label small">Departemen</label>
                            <select name="departemen" id="departemen" class="form-select form-select-sm">
                                <option value="">Semua Departemen</option>
                                @foreach($departemen as $dept)
                                    <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-2">
                            <label class="form-label small">Petugas Maintenance <span class="text-danger text-sm">*</span> </label>
                            <div class="petugas-container">
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" name="petugas[]" class="form-control form-control-sm" placeholder="Nama Petugas 1" required>
                                    <button type="button" class="btn btn-danger btn-sm remove-petugas" style="display:none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        Petugas maintenance wajib diisi
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm mt-2 add-petugas">
                                <i class="bi bi-plus"></i> Tambah Petugas
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm">Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-petugas').forEach(button => {
        button.addEventListener('click', function() {
            const container = this.closest('.mb-2').querySelector('.petugas-container');
            const newPetugasGroup = document.createElement('div');
            newPetugasGroup.className = 'input-group input-group-sm mb-2';
            newPetugasGroup.innerHTML = `
                <input type="text" name="petugas[]" class="form-control form-control-sm" placeholder="Nama Petugas ${container.querySelectorAll('.input-group').length + 1}">
                <button type="button" class="btn btn-danger btn-sm remove-petugas">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            container.appendChild(newPetugasGroup);
            updateRemoveButtons(container);
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-petugas')) {
            const button = e.target.closest('.remove-petugas');
            const container = button.closest('.petugas-container');
            button.closest('.input-group').remove();
            updateRemoveButtons(container);
        }
    });

    function updateRemoveButtons(container) {
        const removeButtons = container.querySelectorAll('.remove-petugas');
        removeButtons.forEach(button => {
            button.style.display = removeButtons.length > 1 ? 'block' : 'none';
        });
    }

    // Initialize remove buttons on page load
    document.querySelectorAll('.petugas-container').forEach(container => {
        updateRemoveButtons(container);
    });
});
</script>
    