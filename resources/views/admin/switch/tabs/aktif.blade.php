<div class="mb-3 p-0 m-0">
    <div class="row align-items-center ms-1 me-1">
        <!-- Export Button on the Left -->
        <div class="col-md-3 mb-3">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
            </button>
        </div>
        
        <!-- Spacer Column -->
        <div class="col-md-5"></div>
        
        <!-- Filter on the Right -->
        <div class="col-md-4 mb-3">
            <form method="GET" action="{{ route('switch.index', ['tab' => 'aktif']) }}" class="d-flex" id="filterForm">
            <select name="lokasi_id" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasi as $lok)
                <option value="{{ $lok->id_lokasi }}" {{ request('lokasi_id') == $lok->id_lokasi ? 'selected' : '' }}>
                    {{ $lok->nama_lokasi }}
                </option>
                @endforeach
            </select>
            <a href="{{route('switch.index', ['tab' => 'aktif'])}}" class="btn btn-danger btn-sm me-1 d-flex justify-content-center align-items-center">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            </form>
        </div>
    </div>
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
                            <input type="number" name="node_terpakai" class="form-control form-control-sm" 
                                   value="{{ $maintenance->node_terpakai }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Bagus</label>
                            <input type="number" name="node_bagus" class="form-control form-control-sm" 
                                   value="{{ $maintenance->node_bagus }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Rusak</label>
                            <input type="number" name="node_rusak" class="form-control form-control-sm" 
                                   value="{{ $maintenance->node_rusak }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
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



    