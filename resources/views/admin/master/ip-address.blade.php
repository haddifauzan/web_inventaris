@extends('admin.layouts.master')

@section('title', 'Data IP Address')

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title">Data IP Address per Lokasi</h2>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus me-2"></i> Tambah IP Host
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="loading-container" class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <table class="table table-striped table-sm small table-hover d-none" id="table-ip">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Lokasi</th>
                                        <th>IP Host</th>
                                        <th>Total IP</th>
                                        <th>Available</th>
                                        <th>In Use</th>
                                        <th>Blocked</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ipRanges as $ipHost => $range)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $range['location'] }}</td>
                                        <td>{{ $ipHost }}</td>
                                        <td>{{ $range['count'] }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $range['available'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $range['in_use'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $range['blocked'] }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('ip-address.detail', ['idIpHost' => $range['id_ip_host']]) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye-fill text-white"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                        data-bs-target="#editRangeModal{{ $range['id_ip_host'] }}">
                                                    <i class="bi bi-pencil-fill text-white"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                                        data-bs-target="#editIpHostModal{{ $range['id_ip_host'] }}">
                                                    <i class="bi bi-gear-fill text-white"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                                        data-bs-target="#deleteRangeModal{{ $range['id_ip_host'] }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
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
                    <h5 class="modal-title">Tambah IP Host dan Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ip-address.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="id_lokasi" class="form-label">Lokasi</label>
                            <select class="form-select" id="id_lokasi" name="id_lokasi" required>
                                <option value="">Pilih Lokasi</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id_lokasi }}">{{ $location->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ip_host" class="form-label">IP Host</label>
                            <input type="text" class="form-control" id="ip_host" name="ip_host" 
                                pattern="^(\d{1,3}\.){3}\d{1,3}$" 
                                placeholder="xxx.xxx.xxx.xxx"
                                required>
                            <small class="text-muted">Format: xxx.xxx.xxx.xxx</small>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="start_range" class="form-label">Start Range</label>
                                <input type="number" class="form-control" id="start_range" name="start_range" 
                                    min="1" max="255" value="1" required>
                            </div>
                            <div class="col-6">
                                <label for="end_range" class="form-label">End Range</label>
                                <input type="number" class="form-control" id="end_range" name="end_range" 
                                    min="1" max="255" value="255" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status Awal</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Range Modals -->
    @foreach($ipRanges as $ipHost => $range)
        <!-- Edit Range Modal -->
        <div class="modal fade" id="editRangeModal{{ $range['id_ip_host'] }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Range IP: {{ $ipHost }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ip-address.update-range') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_ip_host" value="{{ $range['id_ip_host'] }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Baru</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Available">Available</option>
                                    <option value="Blocked">Blocked</option>
                                </select>
                                <small class="text-muted">Perubahan ini akan mempengaruhi semua IP dalam range ini yang tidak sedang digunakan</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Range Modal -->
        <div class="modal fade" id="deleteRangeModal{{ $range['id_ip_host'] }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Range IP: {{ $ipHost }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ip-address.destroy-range') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id_ip_host" value="{{ $range['id_ip_host'] }}">
                        <div class="modal-body">
                            <p>Anda yakin ingin menghapus IP Host ini? Semua IP dalam range {{ $ipHost }} akan dihapus.</p>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Tindakan ini tidak dapat dibatalkan!
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit IP Host Modal -->
        <div class="modal fade" id="editIpHostModal{{ $range['id_ip_host'] }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit IP Host & Range: {{ $ipHost }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ip-address.update-host', $range['id_ip_host']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="id_lokasi" class="form-label">Lokasi</label>
                                <select class="form-select" id="edit_id_lokasi" name="id_lokasi" required>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id_lokasi }}" 
                                            {{ $location->id_lokasi ? 'selected' : '' }}>
                                            {{ $location->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ip_host" class="form-label">IP Host Baru</label>
                                <input type="text" class="form-control" id="edit_ip_host" name="ip_host" 
                                    pattern="^(\d{1,3}\.){3}\d{1,3}$" 
                                    placeholder="xxx.xxx.xxx.xxx"
                                    value="{{ $ipHost }}"
                                    required>
                                <small class="text-muted">Format: xxx.xxx.xxx.xxx</small>
                            </div>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Mengubah IP Host akan mengubah seluruh IP Address dalam range ini yang menggunakan pola IP Host lama.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.getElementById('table-ip');
            const loadingContainer = document.getElementById('loading-container');
    
            // Simulate data loading
            setTimeout(() => {
                loadingContainer.classList.add('d-none');
                table.classList.remove('d-none');
                const datatable = new DataTable("#table-ip");
            }, 100);
        });

        // IP Host validation
        document.querySelector('#ip_host').addEventListener('input', function(e) {
            const input = e.target;
            const value = input.value;
            
            const ipPattern = /^(\d{1,3}\.){3}\d{1,3}$/;
            
            if (!ipPattern.test(value)) {
                input.setCustomValidity('Format IP harus xxx.xxx.xxx.xxx');
            } else {
                const octets = value.split('.');
                const validOctets = octets.every(octet => {
                    const num = parseInt(octet);
                    return num >= 0 && num <= 255;
                });
    
                if (!validOctets) {
                    input.setCustomValidity('Setiap oktet harus bernilai antara 0 dan 255');
                } else {
                    input.setCustomValidity('');
                }
            }
        });

        // Range validation
        const startRange = document.querySelector('#start_range');
        const endRange = document.querySelector('#end_range');
    
        function validateRange() {
            const start = parseInt(startRange.value);
            const end = parseInt(endRange.value);
            
            if (start > end) {
                endRange.setCustomValidity('End range harus lebih besar dari start range');
            } else {
                endRange.setCustomValidity('');
            }
        }
    
        startRange.addEventListener('input', validateRange);
        endRange.addEventListener('input', validateRange);
    </script>
@endsection