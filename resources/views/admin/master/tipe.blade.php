@extends('admin.layouts.master')

@section('title', 'Data Merk Tipe Barang')

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title">Table Data Merk Tipe Barang</h2>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus me-2"></i> Tambah Merk Tipe Barang
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="my-3 ms-3 w-25">
                            <label for="filterJenisBarang" class="form-label">Filter Jenis Barang</label>
                            <select class="form-select" id="filterJenisBarang">
                                <option value="">Semua Jenis Barang</option>
                                <option value="Komputer">Komputer</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Switch">Switch</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <div id="loading-container" class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <table class="table table-sm small table-striped table-hover d-none" id="table-tipe-barang">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Jenis Barang</th>
                                        <th>Tipe/Merk</th>
                                        <th>Spesifikasi</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tipeBarang as $index => $item)
                                    <tr id="{{ $item->tipe_merk }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @switch($item->jenis_barang)
                                                @case('Komputer')
                                                    <span class="badge bg-primary">{{ $item->jenis_barang }}</span>
                                                    @break
                                                @case('Tablet')
                                                    <span class="badge bg-success">{{ $item->jenis_barang }}</span>
                                                    @break
                                                @case('Switch')
                                                    <span class="badge bg-warning">{{ $item->jenis_barang }}</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $item->tipe_merk }}</td>
                                        <td>
                                            <ul class="mb-0">
                                                @php
                                                    $spesifikasi = json_decode($item->spesifikasi, true);
                                                @endphp
                                                @if(!empty($spesifikasi))
                                                    @foreach($spesifikasi as $key => $value)
                                                        <li>{{ $key }}: {{ $value }}</li>
                                                    @endforeach
                                                @else
                                                    <small class="text-sm text-danger">Tidak Ada Spesifikasi</small>
                                                @endif
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id_tipe_barang }}">
                                                <i class="bi bi-pencil-fill text-white"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id_tipe_barang }}">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tipe Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tipe-barang.store') }}" method="POST" id="addForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="jenis_barang" class="form-label small">Jenis Barang<span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="jenis_barang" name="jenis_barang" required onchange="updateSpesifikasiFields(this.value, 'add')">
                                <option value="">Pilih Jenis Barang</option>
                                <option value="Komputer">Komputer</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Switch">Switch</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="tipe_merk" class="form-label small">Tipe/Merk Barang<span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="tipe_merk" name="tipe_merk" required>
                        </div>
                        <label for="spesifikasi" class="form-label small">Spesifikasi Barang</label>
                        <div id="spesifikasi-container-add">
                            <!-- Spesifikasi fields will be dynamically added here -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm mt-2" onclick="addSpesifikasiField('add')">
                            <i class="bi bi-plus"></i> Tambah Spesifikasi
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach($tipeBarang as $item)
    <div class="modal fade" id="editModal{{ $item->id_tipe_barang }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tipe Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tipe-barang.update', $item->id_tipe_barang) }}" method="POST" id="editForm{{ $item->id_tipe_barang }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jenis_barang" class="form-label">Jenis Barang<span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_barang" required onchange="updateSpesifikasiFields(this.value, 'edit{{ $item->id_tipe_barang }}')">
                                <option value="">Pilih Jenis Barang</option>
                                <option value="Komputer" {{ $item->jenis_barang == 'Komputer' ? 'selected' : '' }}>Komputer</option>
                                <option value="Tablet" {{ $item->jenis_barang == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="Switch" {{ $item->jenis_barang == 'Switch' ? 'selected' : '' }}>Switch</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tipe_merk" class="form-label">Tipe/Merk<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tipe_merk" value="{{ $item->tipe_merk }}" required>
                        </div>
                        <div id="spesifikasi-container-edit{{ $item->id_tipe_barang }}">
                            @php
                                $spesifikasi = json_decode($item->spesifikasi, true);
                            @endphp
                            @foreach($spesifikasi as $key => $value)
                            <div class="mb-3 spesifikasi-field">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="spesifikasi[key][]" value="{{ $key }}" placeholder="Nama Spesifikasi">
                                    <input type="text" class="form-control" name="spesifikasi[value][]" value="{{ $value }}" placeholder="Nilai Spesifikasi">
                                    <button type="button" class="btn btn-danger" onclick="removeSpesifikasiField(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-success btn-sm" onclick="addSpesifikasiField('edit{{ $item->id_tipe_barang }}')">
                            <i class="bi bi-plus"></i> Tambah Spesifikasi
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal{{ $item->id_tipe_barang }}" tabindex="-1">
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
                    <form action="{{ route('tipe-barang.destroy', $item->id_tipe_barang) }}" method="POST">
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
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.getElementById('table-tipe-barang');
            const loadingContainer = document.getElementById('loading-container');

            setTimeout(() => {
                loadingContainer.classList.add('d-none');
                table.classList.remove('d-none');
                const datatable = new DataTable("#table-tipe-barang");
            }, 100);
        });

        function getDefaultSpesifikasi(jenisBarang) {
            switch(jenisBarang) {
                case 'Komputer':
                    return [
                        { key: 'Processor', value: '' },
                        { key: 'RAM', value: '' },
                        { key: 'Storage', value: '' },
                        { key: 'GPU', value: '' }
                    ];
                case 'Tablet':
                    return [
                        { key: 'Layar', value: '' },
                        { key: 'Processor', value: '' },
                        { key: 'RAM', value: '' },
                        { key: 'Storage', value: '' }
                    ];
                case 'Switch':
                    return [
                        { key: 'Port Count', value: '' },
                        { key: 'Speed', value: '' },
                    ];
                default:
                    return [];
            }
        }

        function updateSpesifikasiFields(jenisBarang, formType) {
            const container = document.getElementById(`spesifikasi-container-${formType}`);
            container.innerHTML = '';
            
            const defaultSpesifikasi = getDefaultSpesifikasi(jenisBarang);
            defaultSpesifikasi.forEach(spec => {
                addSpesifikasiField(formType, spec.key, spec.value);
            });
        }

        function addSpesifikasiField(formType, key = '', value = '') {
            const container = document.getElementById(`spesifikasi-container-${formType}`);
            const div = document.createElement('div');
            div.className = 'mb-2 spesifikasi-field';
            div.innerHTML = `<div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="spesifikasi[key][]" value="${key}" placeholder="Nama Spesifikasi">
                    <input type="text" class="form-control form-control-sm" name="spesifikasi[value][]" value="${value}" placeholder="Nilai Spesifikasi">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSpesifikasiField(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        function removeSpesifikasiField(button) {
            button.closest('.spesifikasi-field').remove();
        }

        // Form submission handling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get all specification fields
                const spesifikasiFields = this.querySelectorAll('.spesifikasi-field');
                const spesifikasiData = {};

                // Collect specification data
                spesifikasiFields.forEach(field => {
                    const keyInput = field.querySelector('input[name="spesifikasi[key][]"]');
                    const valueInput = field.querySelector('input[name="spesifikasi[value][]"]');
                    
                    if (keyInput && valueInput && keyInput.value && valueInput.value) {
                        spesifikasiData[keyInput.value] = valueInput.value;
                    }
                });

                // Create hidden input for spesifikasi
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'spesifikasi';
                hiddenInput.value = JSON.stringify(spesifikasiData);

                // Remove existing spesifikasi input fields
                spesifikasiFields.forEach(field => field.remove());
                
                // Add the hidden input
                this.appendChild(hiddenInput);
                
                // Submit the form
                this.submit();
            });
        });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterSelect = document.getElementById('filterJenisBarang');
        let datatable;
    
        // Initialize DataTable with filter functionality
        setTimeout(() => {
            const loadingContainer = document.getElementById('loading-container');
            const table = document.getElementById('table-tipe-barang');
            
            loadingContainer.classList.add('d-none');
            table.classList.remove('d-none');
            
            // Initialize DataTable
            datatable = new DataTable("#table-tipe-barang");
            
            // Add filter event listener
            filterSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                
                // Custom filtering function
                DataTable.ext.search.push(function(settings, data, dataIndex) {
                    // If no filter is selected, show all rows
                    if (!selectedValue) return true;
                    
                    // Get the jenis_barang value from the row (assuming it's in the second column)
                    const jenisBarang = data[1].toLowerCase();
                    
                    // Check if the row contains the badge with selected value
                    return jenisBarang.includes(selectedValue.toLowerCase());
                });
                
                // Reapply the filter
                datatable.draw();
                
                // Remove the custom filtering function
                DataTable.ext.search.pop();
            });

            const urlParams = new URLSearchParams(window.location.search);
            const searchResult = urlParams.get('search'); // Ambil ID dari URL

            if (searchResult) {
                // Filter DataTables untuk menampilkan hanya data yang sesuai
                datatable.search(searchResult).draw();

                // Tunggu sebentar agar filtering selesai, lalu cari row yang sesuai
                setTimeout(() => {
                    const row = document.querySelector(`#table-tipe-barang tbody tr[id="${searchResult}"]`);
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
@endsection