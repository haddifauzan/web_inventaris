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
                            Backup
                        </button>
                        <button type="button" title="Musnahkan" class="btn btn-danger btn-sm d-flex" data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $tablet->id_barang }}">
                            <i class="bi bi-trash-fill text-white me-1"></i>
                            Musnah
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

    