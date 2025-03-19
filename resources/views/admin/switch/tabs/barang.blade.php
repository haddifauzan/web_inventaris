<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <a href="{{ route('switch.create') }}" class="btn btn-primary btn-sm ms-2">
                <i class="bi bi-plus me-1"></i>Tambah Switch
            </a>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-2">
            <select id="statusFilter" class="form-select">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Backup">Backup</option>
                <option value="Baru">Baru</option>
                <option value="Pemusnahan">Pemusnahan</option>
            </select>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-sm small table-striped" id="switchTable">
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
            @foreach($data as $index => $switch)
            <tr id="{{ $switch->serial }}">
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
                    @elseif ($switch->status === 'Baru')
                        <span class="badge bg-info">{{ $switch->status}}</span>
                    @elseif ($switch->status === 'Pemusnahan')
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
        var table = new DataTable('#switchTable', {
            searching: true,
            paging: true,
            info: true,
            ordering: false,
            language: {
            searchPlaceholder: "Cari Serial..."
            }
        });

        var statusFilter = document.getElementById('statusFilter');

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var statusValue = statusFilter.value.toLowerCase();
            var statusText = data[6].toLowerCase(); // Index kolom ke-7 (mulai dari 0)

            return statusValue === '' || statusText.includes(statusValue);
        });

        statusFilter.addEventListener('change', function() {
            table.draw();
        });

        document.querySelectorAll('.clear-search').forEach(function(button) {
            button.addEventListener('click', function() {
                searchInput.value = '';
                table.search('').draw();
            });
        });

        const loadingContainer = document.getElementById('loading-container');

        setTimeout(() => {
            if (loadingContainer) {
                loadingContainer.classList.add('d-none');
            }
            document.getElementById('switchTable').style.visibility = 'visible';

            const urlParams = new URLSearchParams(window.location.search);
            const searchResult = urlParams.get('search');

            if (searchResult) {
                table.search(searchResult).draw();

                setTimeout(() => {
                    const row = document.querySelector(`#switchTable tbody tr[id="${searchResult}"]`);
                    if (row) {
                        window.scrollTo({
                            top: row.offsetTop - 100,
                            behavior: 'smooth'
                        });

                        row.classList.add('table-warning');
                        setTimeout(() => row.classList.remove('table-warning'), 3000);
                    }
                }, 500);
            }
        }, 100);
    });
</script>