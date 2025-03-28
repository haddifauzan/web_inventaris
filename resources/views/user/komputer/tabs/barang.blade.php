<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <a href="{{ route('komputer.create') }}" class="btn btn-primary btn-sm ms-2">
                <i class="bi bi-plus me-1"></i>Tambah Komputer
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
    <table class="table table-sm small table-striped" id="komputerTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Operating Sistem</th>
                <th>Serial</th>
                <th>Spesifikasi</th>
                <th>Kepemilikan</th>
                <th>Tahun Perolehan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <div id="noDataMessage" class="alert alert-secondary py-2 mx-2 mt-2" style="display: none;">
            Data tidak ditemukan.
        </div>
        <tbody>
            @foreach($data as $index => $komputer)
            <tr id="{{ json_decode($komputer->serial, true)['cpu'] ?? '' }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $komputer->model }}</td>
                <td>{{ $komputer->tipe_merk }}</td>
                <td>{{ $komputer->operating_system }}</td>
                <td>
                    <ul class="list-unstyled mb-0">
                        @if(json_decode($komputer->serial, true))
                            @foreach(json_decode($komputer->serial, true) as $key => $value)
                                <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                            @endforeach
                        @else
                            <li>-</li>
                        @endif
                    </ul>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary text-white" 
                            data-bs-toggle="modal" data-bs-target="#spesifikasiModal{{ $komputer->id_barang }}">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </td>
                <td>
                    @if ($komputer->kepemilikan === 'Inventaris')
                        <span class="badge bg-info">{{ $komputer->kepemilikan }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $komputer->kepemilikan }}</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($komputer->tahun_perolehan)->format('M Y') }}</td>
                <td>
                    @if ($komputer->status === 'Backup')
                        <span class="badge bg-warning">{{ $komputer->status}}</span>
                    @elseif ($komputer->status === 'Aktif')
                        <span class="badge bg-primary">{{ $komputer->status}}</span>
                    @elseif ($komputer->status === 'Baru')
                        <span class="badge bg-success">{{ $komputer->status}}</span>
                    @else
                        <span class="badge bg-danger">{{ $komputer->status}}</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        @if($komputer->status != 'Pemusnahan')
                        <a href="{{ route('komputer.edit', $komputer->id_barang) }}" 
                           class="btn btn-warning btn-sm text-white"
                           title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        @endif
                        <button type="button" class="btn btn-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#hapusModal{{ $komputer->id_barang }}"
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

@foreach($data as $index => $komputer)
    <!-- Modal Spesifikasi -->
    <div class="modal fade" id="spesifikasiModal{{ $komputer->id_barang }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Spesifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled mb-0">
                        @if(is_string($komputer->spesifikasi))
                            @foreach(json_decode($komputer->spesifikasi, true) as $key => $value)
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
    <div class="modal fade" id="hapusModal{{ $komputer->id_barang }}" tabindex="-1">
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
                    <form action="{{ route('komputer.destroy', $komputer->id_barang) }}" method="POST">
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
        var table = new DataTable('#komputerTable', {
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
            var statusText = data[9].toLowerCase(); // Index kolom ke-9 (mulai dari 0)

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
            document.getElementById('komputerTable').style.visibility = 'visible';

            const urlParams = new URLSearchParams(window.location.search);
            const searchResult = urlParams.get('search');

            if (searchResult) {
                table.search(searchResult).draw();

                setTimeout(() => {
                    const row = document.querySelector(`#komputerTable tbody tr[id="${searchResult}"]`);
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