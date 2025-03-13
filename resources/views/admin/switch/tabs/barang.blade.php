<div>
    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('switch.create') }}" class="btn btn-primary btn-sm ms-2">
                <i class="bi bi-plus me-1"></i>Tambah Switch
            </a>
        </div>
        <div class="col-md-3"></div>
        <div class="col-md-2">
            <select id="statusFilter" class="form-select">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Backup">Backup</option>
                <option value="Baru">Baru</option>
                <option value="Pemusnahan">Pemusnahan</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchSerial" class="form-control" placeholder="Cari Serial Switch...">
                <button class="btn btn-secondary clear-search" type="button" data-target="searchSerial">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-sm small table-striped" id="barangTable">
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
        <div id="noDataMessage" class="alert alert-secondary py-2 mx-2 mt-2" style="display: none;">
            Data tidak ditemukan.
        </div>
        <tbody>
            @foreach($data as $index => $switch)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $switch->model }}</td>
                <td>{{ $switch->tipe_merk }}</td>
                <td>{{ $switch->serial }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary text-white" 
                            data-bs-toggle="modal" data-bs-target="#spesifikasiModal{{ $switch->id_barang }}">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </td>
                <td>{{ \Carbon\Carbon::parse($switch->tahun_perolehan)->format('M Y') }}</td>
                <td>
                    @if ($switch->status === 'Backup')
                        <span class="badge bg-success">{{ $switch->status}}</span>
                    @elseif ($switch->status === 'Aktif')
                        <span class="badge bg-primary">{{ $switch->status}}</span>
                    @else
                        <span class="badge bg-danger">{{ $switch->status}}</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('switch.edit', $switch->id_barang) }}" 
                           class="btn btn-warning btn-sm text-white"
                           title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#hapusModal{{ $switch->id_barang }}"
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
@foreach($data as $index => $switch)
    <!-- Modal Spesifikasi -->
    <div class="modal fade" id="spesifikasiModal{{ $switch->id_barang }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Spesifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled mb-0">
                        @if(is_string($switch->spesifikasi))
                            @foreach(json_decode($switch->spesifikasi, true) as $key => $value)
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
    <div class="modal fade" id="hapusModal{{ $switch->id_barang }}" tabindex="-1">
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
                    <form action="{{ route('switch.destroy', $switch->id_barang) }}" method="POST">
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
        var table = new DataTable('#barangTable', {
            searching: false,  // Nonaktifkan search box
            paging: true,      // Mengaktifkan pagination
            info: true,        // Menampilkan informasi jumlah data
            ordering: false     // Mengaktifkan fitur pengurutan
        });

        var statusFilter = document.getElementById('statusFilter');
        var searchInput = document.getElementById('searchSerial');
        var noDataMessage = document.getElementById('noDataMessage');

        function filterTable() {
            var statusValue = statusFilter.value.toLowerCase();
            var searchTerm = searchInput.value.toLowerCase();
            var rows = document.querySelectorAll('#barangTable tbody tr');
            var foundAny = false;

            rows.forEach(function(row) {
                var statusText = row.querySelector('td:nth-child(7)').textContent.toLowerCase();
                var serialContent = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                var statusMatch = statusValue === '' || statusText.includes(statusValue);
                var searchMatch = searchTerm === '' || serialContent.includes(searchTerm);
                
                if (statusMatch && searchMatch) {
                    row.style.display = '';
                    foundAny = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            noDataMessage.style.display = foundAny ? 'none' : 'block';
        }

        statusFilter.addEventListener('change', filterTable);
        searchInput.addEventListener('keyup', filterTable);

        document.querySelectorAll('.clear-search').forEach(function(button) {
            button.addEventListener('click', function() {
                searchInput.value = '';
                filterTable();
            });
        });
    });
</script>