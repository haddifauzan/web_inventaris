<div class="table-responsive">
    <table class="table table-sm small table-striped" id="historyTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Serial</th>
                <th>Status Riwayat</th>
                <th>Status Barang</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Keterangan Terakhir</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $barang)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $barang->model }}</td>
                <td>{{ $barang->tipe_merk }}</td>
                <td>
                    {{ $barang->serial }}
                </td>
                <td>
                    @if ($barang->riwayat->first()->status === 'Aktif')
                        <span class="badge bg-primary">{{ $barang->riwayat->first()->status }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $barang->riwayat->first()->status }}</span>
                    @endif
                </td>
                <td>
                    @if ($barang->status === 'Backup')
                        <span class="badge bg-success">{{ $barang->status}}</span>
                    @elseif ($barang->status === 'Aktif')
                        <span class="badge bg-primary">{{ $barang->status}}</span>
                    @else
                        <span class="badge bg-danger">{{ $barang->status}}</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($barang->riwayat->first()->waktu_awal)->format('d M Y - H:i') }}</td>
                <td>{{ $barang->riwayat->first()->waktu_akhir ? \Carbon\Carbon::parse($barang->riwayat->first()->waktu_akhir)->format('d M Y - H:i') : '-' }}</td>
                <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $history->keterangan ?? '-' }}">
                    {{ Str::limit($history->keterangan ?? '-', 30) }}
                </td>
                <td>
                    <button type="button" class="btn btn-info btn-sm text-white d-flex" title="Lihat Riwayat" data-bs-toggle="modal" data-bs-target="#historyModal{{ $barang->id_barang }}">
                        <i class="bi bi-eye-fill me-1"></i> Lihat
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal for each item -->
@foreach($data as $barang)
<div class="modal fade" id="historyModal{{ $barang->id_barang }}" tabindex="-1" aria-labelledby="historyModalLabel{{ $barang->id_barang }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel{{ $barang->id_barang }}">
                    Riwayat {{ $barang->model }} ({{ $barang->tipe_merk }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm small table-striped" id="historyDetailTable{{ $barang->id_barang }}">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Lokasi</th>
                                <th>Departemen</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barang->riwayat as $index => $history)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $history->lokasi->nama_lokasi }}</td>
                                <td>{{ $history->departemen->nama_departemen }}</td>
                                <td>{{ $history->waktu_awal }}</td>
                                <td>{{ $history->waktu_akhir ?? '-' }}</td>
                                <td>
                                    @if ($history->status === 'Aktif')
                                        <span class="badge bg-primary">{{ $history->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $history->status }}</span>
                                    @endif
                                </td>
                                <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $history->keterangan ?? '-' }}">
                                    {{ Str::limit($history->keterangan ?? '-', 20) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach