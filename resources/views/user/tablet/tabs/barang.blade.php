<div>
    <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchSerial" class="form-control" placeholder="Cari Serial Tablet...">
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
        <tbody>
            @foreach($data as $index => $tablet)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tablet->model }}</td>
                <td>{{ $tablet->tipe_merk }}</td>
                <td>{{ $tablet->serial }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary text-white" 
                            data-bs-toggle="modal" data-bs-target="#spesifikasiModal{{ $tablet->id_barang }}">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </td>
                <td>{{ \Carbon\Carbon::parse($tablet->tahun_perolehan)->format('M Y') }}</td>
                <td>
                    @if ($tablet->status === 'Backup')
                        <span class="badge bg-success">{{ $tablet->status}}</span>
                    @elseif ($tablet->status === 'Aktif')
                        <span class="badge bg-primary">{{ $tablet->status}}</span>
                    @else
                        <span class="badge bg-danger">{{ $tablet->status}}</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('tablet.edit', $tablet->id_barang) }}" 
                           class="btn btn-warning btn-sm text-white"
                           title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                            Edit
                        </a>
                        <button type="button" class="btn btn-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#hapusModal{{ $tablet->id_barang }}"
                                title="Hapus">
                            <i class="bi bi-trash-fill"></i>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Serial -->
@foreach($data as $index => $tablet)
    <!-- Modal Spesifikasi -->
    <div class="modal fade" id="spesifikasiModal{{ $tablet->id_barang }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Spesifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled mb-0">
                        @if(is_string($tablet->spesifikasi))
                            @foreach(json_decode($tablet->spesifikasi, true) as $key => $value)
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
    <div class="modal fade" id="hapusModal{{ $tablet->id_barang }}" tabindex="-1">
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
                    <form action="{{ route('tablet.destroy', $tablet->id_barang) }}" method="POST">
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
            searching: false,  // Nonaktifkan fitur pencarian bawaan DataTable
            paging: true,      // Mengaktifkan pagination
            info: true,        // Menampilkan informasi jumlah data
            ordering: false    // Menonaktifkan fitur pengurutan kolom
        });

        var searchInput = document.getElementById('searchSerial');
        var noDataMessage = document.createElement('tr');
        noDataMessage.innerHTML = '<td colspan="8" class="text-center">Data tidak ditemukan</td>';
        noDataMessage.style.display = 'none';
        document.querySelector('#barangTable tbody').appendChild(noDataMessage);

        // Fungsi pencarian berdasarkan serial CPU/Monitor
        searchInput.addEventListener('keyup', function() {
            var searchTerm = this.value.trim().toLowerCase();
            var rows = document.querySelectorAll('#barangTable tbody tr');
            var foundAny = false;

            rows.forEach(function(row) {
                var serialCell = row.querySelector('td:nth-child(4)'); // Mengambil data di kolom "Serial"
                if (serialCell) {
                    var serialContent = serialCell.textContent.trim().toLowerCase();
                    var found = serialContent.includes(searchTerm);
                    row.style.display = (found || searchTerm === '') ? '' : 'none';
                    if (found) foundAny = true;
                }
            });

            noDataMessage.style.display = foundAny ? 'none' : '';
            updateRowNumbers();
        });

        // Fungsi untuk memperbarui nomor urut setelah pencarian
        function updateRowNumbers() {
            var visibleIndex = 1;
            document.querySelectorAll('#barangTable tbody tr').forEach(function(row) {
                if (row.style.display !== 'none' && !row.contains(noDataMessage)) {
                    row.querySelector('td:first-child').textContent = visibleIndex++;
                }
            });
        }

        // Tombol clear search
        document.querySelectorAll('.clear-search').forEach(function(button) {
            button.addEventListener('click', function() {
                var targetId = this.getAttribute('data-target');
                var input = document.getElementById(targetId);
                input.value = '';
                input.dispatchEvent(new Event('keyup'));
            });
        });
    });

</script>