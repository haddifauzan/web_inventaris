<button type="button" class="btn btn-danger btn-sm ms-2 my-2" data-bs-toggle="modal" data-bs-target="#deleteComputersModal">
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
            @foreach($data as $index => $computer)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $computer->model }}</td>
                <td>{{ $computer->tipe_merk }}</td>
                <td>
                    @if (json_decode($computer->serial))
                        CPU: {{ json_decode($computer->serial)->cpu }}<br>
                        Monitor: {{ json_decode($computer->serial)->monitor }}
                    @else
                        {{ $computer->serial }}
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($computer->tahun_perolehan)->format('M Y') }}</td>
                <td title="{{ $computer->menuPemusnahan->keterangan ?? '-' }}">
                    {{ Str::limit($computer->menuPemusnahan->keterangan ?? '-', 50) }}
                </td>
                <td>{{ $computer->menuPemusnahan->created_at ? \Carbon\Carbon::parse($computer->menuPemusnahan->created_at)->format('d M Y - H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<!-- Delete Modal -->
<div class="modal fade" id="deleteComputersModal" tabindex="-1" aria-labelledby="deleteComputersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteComputersModalLabel">Hapus Data Komputer</h5>
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
                        <tbody id="computerTableBody">
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
        let selectedComputers = [];
    
        // Load data when year is selected
        yearFilter.addEventListener('change', function() {
            loadComputerData(this.value);
        });
    
        // Select all functionality
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.computer-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                handleCheckboxChange(checkbox);
            });
        });
    
        // Load computer data based on year
        function loadComputerData(year) {
            fetch(`/komputer/get-destroyed/${year}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('computerTableBody');
                    tableBody.innerHTML = '';
                    
                    if (data.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data pada periode tersebut</td>
                            </tr>
                        `;
                    } else {
                        data.forEach((computer, index) => {
                            const row = `
                                <tr>
                                    <td><input type="checkbox" class="form-check-input computer-checkbox" value="${computer.id_barang}"></td>
                                    <td>${index + 1}</td>
                                    <td>${computer.model || '-'}</td>
                                    <td>${computer.tipe_merk || '-'}</td>
                                    <td>${formatSerial(computer.serial)}</td>
                                    <td>${formatDate(computer.tahun_perolehan)}</td>
                                    <td>${computer.menu_pemusnahan?.keterangan || '-'}</td>
                                    <td>${formatDate(computer.menu_pemusnahan?.created_at)}</td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    }
    
                    // Add event listeners to checkboxes
                    document.querySelectorAll('.computer-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', () => handleCheckboxChange(checkbox));
                    });
                });
        }
    
        // Handle checkbox changes
        function handleCheckboxChange(checkbox) {
            const computerId = checkbox.value;
            if (checkbox.checked) {
                if (!selectedComputers.includes(computerId)) {
                    selectedComputers.push(computerId);
                }
            } else {
                selectedComputers = selectedComputers.filter(id => id !== computerId);
            }
            
            deleteSelectedBtn.disabled = selectedComputers.length === 0;
        }
    
        // Delete selected computers
        deleteSelectedBtn.addEventListener('click', function() {
            if (selectedComputers.length > 0) {
                $('#confirmDeleteModal').modal('show');
            }
        });
    
        // Confirm delete
        confirmDeleteBtn.addEventListener('click', function() {
            fetch('/komputer/destroy-multiple', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    computers: selectedComputers
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload table
                    loadComputerData(yearFilter.value);
                    // Reset selection
                    selectedComputers = [];
                    deleteSelectedBtn.disabled = true;
                    selectAll.checked = false;
                    // Close modals
                    $('#confirmDeleteModal').modal('hide');
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        showCloseButton: true,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        });
    
        // Helper function to format serial number
        function formatSerial(serial) {
            try {
                const serialObj = JSON.parse(serial);
                return `CPU: ${serialObj.cpu}<br>Monitor: ${serialObj.monitor}`;
            } catch {
                return serial || '-';
            }
        }
    
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