<div class="mb-3 p-0 m-0">
    <div class="row align-items-center">
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
            <form method="GET" action="{{ route('komputer.index', ['tab' => 'aktif']) }}" class="d-flex" id="filterForm">
            <select name="lokasi_id" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasi as $lok)
                <option value="{{ $lok->id_lokasi }}" {{ request('lokasi_id') == $lok->id_lokasi ? 'selected' : '' }}>
                    {{ $lok->nama_lokasi }}
                </option>
                @endforeach
            </select>
            <a href="{{route('komputer.index', ['tab' => 'aktif'])}}" class="btn btn-danger btn-sm me-1 d-flex justify-content-center align-items-center">
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
                <th>Komputer Name</th>
                <th>IP Address</th>
                <th>Operating System</th>
                <th>User</th>
                <th>Model</th>
                <th>Type/Merk</th>
                <th>Serial</th>
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
                            Backup
                        </button>
                        <button type="button" title="Musnahkan" class="btn btn-danger btn-sm d-flex" data-bs-toggle="modal" data-bs-target="#pemusnahanModal{{ $computer->id_barang }}">
                            <i class="bi bi-trash-fill text-white me-1"></i>
                            Musnah
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

    