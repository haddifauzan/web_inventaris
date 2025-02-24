<div>
    <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchSerial" class="form-control" placeholder="Cari Serial CPU/Monitor...">
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
                <th>Operating Sistem</th>
                <th>Serial</th>
                <th>Spesifikasi</th>
                <th class="text-start">Kelayakan</th>
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
            <tr>
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
                    <div class="progress" style="height: 12px; width: 100px;">
                        <div 
                            class="progress-bar 
                                {{ 
                                    $komputer->kelayakan >= 75 ? 'bg-success' :
                                    ($komputer->kelayakan >= 50 ? 'bg-warning' : 'bg-danger')
                                }}"
                            role="progressbar" 
                            aria-valuenow="{{ $komputer->kelayakan ?? 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100"
                            style="width: {{ $komputer->kelayakan ?? 0 }}%">
                            {{ $komputer->kelayakan ?? '-' }}%
                        </div>
                    </div>
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
                        <span class="badge bg-success">{{ $komputer->status}}</span>
                    @elseif ($komputer->status === 'Aktif')
                        <span class="badge bg-primary">{{ $komputer->status}}</span>
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
        var table = new DataTable('#barangTable', {
            searching: false,  // Nonaktifkan search box
            paging: true,      // Mengaktifkan pagination
            info: true,        // Menampilkan informasi jumlah data
            ordering: false     // Mengaktifkan fitur pengurutan
        });

        // Fungsi pencarian berdasarkan serial CPU atau Monitor
        document.getElementById('searchSerial').addEventListener('keyup', function() {
            var searchTerm = this.value.toLowerCase();
            var rows = document.querySelectorAll('#barangTable tbody tr');
            var noDataMessage = document.getElementById('noDataMessage');
            var foundAny = false;

            rows.forEach(function(row) {
                // Ambil konten dari kolom serial (kolom ke-5)
                var serialContent = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                // Cek apakah konten serial mengandung searchTerm
                var found = serialContent.includes(searchTerm);

                // Tampilkan atau sembunyikan baris berdasarkan hasil pencarian
                row.style.display = (found || searchTerm === '') ? '' : 'none';
                if (found) {
                    foundAny = true; // Set foundAny to true if at least one row is found
                }
            });

            // Tampilkan atau sembunyikan pesan "Data tidak ditemukan"
            noDataMessage.style.display = foundAny ? 'none' : 'block';

            updateRowNumbers();
        });

        // Fungsi untuk memperbarui nomor urut
        function updateRowNumbers() {
            var visibleIndex = 1;
            document.querySelectorAll('#barangTable tbody tr').forEach(function(row) {
                if (row.style.display !== 'none') {
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