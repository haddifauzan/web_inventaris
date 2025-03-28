<button type="button" class="btn btn-danger btn-sm ms-2 my-2" data-bs-toggle="modal" data-bs-target="#deleteSwitchsModal">
    <i class="bi bi-trash me-2"></i> Hapus Data Berdasarkan Periode
</button>

<div class="table-responsive">
    <table class="table table-sm small table-striped" id="destroyedTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Serial</th>
                <th>Tahun Perolehan</th>
                <th>Keterangan Pemusnahan</th>
                <th>Waktu Pemusnahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $switch)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $switch->model }}</td>
                <td>{{ $switch->tipe_merk }}</td>
                <td>{{ $switch->serial }}</td>
                <td>{{ \Carbon\Carbon::parse($switch->tahun_perolehan)->format('M Y') }}</td>
                <td title="{{ $switch->menuPemusnahan->keterangan ?? '-' }}">
                    {{ Str::limit($switch->menuPemusnahan->keterangan ?? '-', 50) }}
                </td>
                <td>{{ $switch->menuPemusnahan->created_at ? \Carbon\Carbon::parse($switch->menuPemusnahan->created_at)->format('d M Y - H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<!-- Delete Modal -->
<div class="modal fade" id="deleteSwitchsModal" tabindex="-1" aria-labelledby="deleteSwitchsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSwitchsModalLabel">Hapus Data Switch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Year Filter -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select id="yearFilter" class="form-select">
                            <option value="">Pilih Periode Tahun</option>
                            @php
                                $currentYear = date('Y');
                                for($year = $currentYear; $year >= $currentYear - 5; $year--) {
                                    echo "<option value='$year'>$year</option>";
                                }
                            @endphp
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-sm small table-striped" id="destroyedTablePeriode">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>No</th>
                                <th>Model</th>
                                <th>Tipe/Merk</th>
                                <th>Serial</th>
                                <th>Tahun Perolehan</th>
                                <th>Keterangan Pemusnahan</th>
                                <th>Waktu Pemusnahan</th>
                            </tr>
                        </thead>
                        <tbody id="switchTableBody">
                            <!-- Data will be populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="deleteSelectedBtn" disabled>Hapus Terpilih</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data terpilih secara permanen?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus Permanen</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearFilter = document.getElementById('yearFilter');
        const selectAll = document.getElementById('selectAll');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let selectedSwitchs = [];
    
        // Load data when year is selected
        yearFilter.addEventListener('change', function() {
            loadSwitchData(this.value);
        });
    
        // Select all functionality
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.switch-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                handleCheckboxChange(checkbox);
            });
        });
    
        // Load switch data based on year
        function loadSwitchData(year) {
            console.log('Mengambil data untuk tahun:', year);
            fetch(`/switch/get-destroyed/${year}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Data yang diterima:', data);
                    const tableBody = document.getElementById('switchTableBody');
                    tableBody.innerHTML = '';

                    if (!data || data.length === 0) {
                        console.warn('Tidak ada data ditemukan.');
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data pada periode tersebut</td>
                            </tr>
                        `;
                        return;
                    }

                    data.forEach((switchItem, index) => {
                        console.log('Memproses Switch:', switchItem);
                        const row = `
                            <tr>
                                <td><input type="checkbox" class="form-check-input switch-checkbox" value="${switchItem.id_barang}"></td>
                                <td>${index + 1}</td>
                                <td>${switchItem.model || '-'}</td>
                                <td>${switchItem.tipe_merk || '-'}</td>
                                <td>${switchItem.serial}</td>
                                <td>${formatDate(switchItem.tahun_perolehan)}</td>
                                <td>${switchItem.menu_pemusnahan?.keterangan || '-'}</td>
                                <td>${formatDate(switchItem.menu_pemusnahan?.created_at)}</td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });

                    // Tambahkan event listener ke checkbox baru
                    document.querySelectorAll('.switch-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', () => handleCheckboxChange(checkbox));
                    });
                })
                .catch(error => {
                    console.error('Error mengambil data:', error);
                });
        }
    
        // Handle checkbox changes
        function handleCheckboxChange(checkbox) {
            const switchId = checkbox.value;
            if (checkbox.checked) {
                if (!selectedSwitchs.includes(switchId)) {
                    selectedSwitchs.push(switchId);
                }
            } else {
                selectedSwitchs = selectedSwitchs.filter(id => id !== switchId);
            }
            
            deleteSelectedBtn.disabled = selectedSwitchs.length === 0;
        }
    
        // Delete selected switchs
        deleteSelectedBtn.addEventListener('click', function() {
            if (selectedSwitchs.length > 0) {
                $('#confirmDeleteModal').modal('show');
            }
        });
    
        // Confirm delete
        confirmDeleteBtn.addEventListener('click', function() {
            fetch('/switch/destroy-multiple', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ switchs: selectedSwitchs })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                // Tutup modal konfirmasi terlebih dahulu
                $('#confirmDeleteModal').modal('hide');
                // Tutup juga modal utama
                $('#deleteSwitchsModal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    showCloseButton: true,
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                }).then(() => {
                    // Refresh halaman
                    window.location.reload();
                });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $('#confirmDeleteModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menghapus data',
                    showCloseButton: true,
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
            });
        });
    
        // Helper function to format dates without moment.js
        function formatDate(dateString) {
            if (!dateString) return '-';
            
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';
    
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = date.getDate().toString().padStart(2, '0');
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
    
            if (dateString.includes('tahun_perolehan')) {
                return `${month} ${year}`;
            }
            
            return `${day} ${month} ${year} - ${hours}:${minutes}`;
        }
    });
</script>