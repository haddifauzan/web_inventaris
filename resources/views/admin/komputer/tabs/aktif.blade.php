<div>
    <div class="row">
        <div class="card-body">        
            <div class="card-body border-bottom col-md-12 mb-2">
                <form method="GET" action="{{ route('komputer.index', ['tab' => 'aktif']) }}" id="filterForm">
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
                        
                        <div class="col-md-3">
                            <label for="os" class="form-label">Operating System</label>
                            <select name="os" id="os" class="form-select form-select-sm">
                                <option value="">Semua OS</option>
                                @foreach(['Windows 7', 'Windows 8', 'Windows 10', 'Windows 11', 'Linux', 'MacOS'] as $os)
                                    <option value="{{ $os }}" {{ request('os') == $os ? 'selected' : '' }}>{{ $os }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="model" class="form-label">Model</label>
                            <select name="model" id="model" class="form-select form-select-sm">
                                <option value="">Semua Model</option>
                                <option value="PC" {{ request('model') == 'PC' ? 'selected' : '' }}>PC</option>
                                <option value="Laptop" {{ request('model') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                            </select>
                        </div>
                        
                        <!-- Second Row -->
                        <div class="col-md-3">
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
                        
                        <div class="col-md-3">
                            <label for="kepemilikan" class="form-label">Kepemilikan</label>
                            <select name="kepemilikan" id="kepemilikan" class="form-select form-select-sm">
                                <option value="">Semua Kepemilikan</option>
                                <option value="Inventaris" {{ request('kepemilikan') == 'Inventaris' ? 'selected' : '' }}>Inventaris</option>
                                <option value="MIS" {{ request('kepemilikan') == 'MIS' ? 'selected' : '' }}>MIS</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
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
                        
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('komputer.index', ['tab' => 'aktif']) }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Export Button -->
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
                <th>Komputer Name</th>
                <th>IP Address</th>
                <th>Operating System</th>
                <th>User</th>
                <th>Model</th>
                <th>Type/Merk</th>
                <th>Serial</th>
                <th>Kepemilikan</th>
                <th>Tahun Perolehan</th>
                <th>Kelayakan</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $computer)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $computer->menuAktif->first()->lokasi->nama_lokasi ?? '-' }}</td>
                <td>{{ $computer->menuAktif->first()->departemen->nama_departemen ?? '-' }}</td>
                <td>{{ $computer->menuAktif->first()->komputer_name ?? '-' }}</td>
                <td>{{ $computer->menuAktif->first()->ipAddress->ip_address ?? '-' }}</td>
                <td>{{ $computer->operating_system ?? '-' }}</td>
                <td>{{ $computer->menuAktif->first()->user ?? '-' }}</td>
                <td>{{ $computer->model }}</td>
                <td>{{ $computer->tipe_merk ?? '-' }}</td>
                <td>
                    @if (json_decode($computer->serial))
                        <b><i>CPU:</i></b> {{ json_decode($computer->serial)->cpu }}<br>
                        <b><i>Monitor:</i></b> {{ json_decode($computer->serial)->monitor }}
                    @else
                        {{ $computer->serial ?? '-' }}
                    @endif
                </td>
                <td>
                    @if ($computer->kepemilikan === 'Inventaris')
                        <span class="badge bg-info">{{ $computer->kepemilikan }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $computer->kepemilikan }}</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($computer->tahun_perolehan)->format('M Y') ?? '-' }}</td>
                <td>
                    <div class="progress" style="height: 12px; width: 100px;">
                        <div 
                            class="progress-bar 
                                {{ 
                                    $computer->kelayakan >= 75 ? 'bg-success' :
                                    ($computer->kelayakan >= 50 ? 'bg-warning' : 'bg-danger')
                                }}"
                            role="progressbar" 
                            aria-valuenow="{{ $computer->kelayakan ?? 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100"
                            style="width: {{ $computer->kelayakan ?? 0 }}%">
                            {{ $computer->kelayakan ?? '-' }}%
                        </div>
                    </div>
                </td>
                <td>
                    @if ($computer->status === 'Aktif')
                        Dipakai
                    @else
                        {{ $computer->menuAktif->first()->status ?? '-' }}
                    @endif
                </td>
                <td title="{{ $computer->menuAktif->first()->keterangan ?? '-' }}">
                    {{ Str::limit($computer->menuAktif->first()->keterangan ?? '-', 50) }}
                </td><td>
                    <div class="btn-group">
                        <button type="button" title="Backup" class="btn btn-success btn-sm text-white d-flex" data-bs-toggle="modal" data-bs-target="#backupModal{{ $computer->id_barang }}">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                        </button>
                        <button type="button" title="Musnahkan" class="btn btn-danger btn-sm d-flex" data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $computer->id_barang }}">
                            <i class="bi bi-trash-fill text-white me-1"></i>
                        </button>
                    </div>

                    <!-- Modal Backup -->
                    <div class="modal fade" id="backupModal{{ $computer->id_barang }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Kembalikan ke Backup</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('komputer.tobackup', $computer->id_barang) }}" method="POST">
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
                    <div class="modal fade" id="pemusnahanModal{{ $computer->id_barang }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Pindahkan ke Pemusnahan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('komputer.topemusnahan', $computer->id_barang) }}" method="POST">
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
            <form action="{{ route('laporan.export-computer-active') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Data Komputer</h5>
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

    