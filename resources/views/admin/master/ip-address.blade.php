@extends('admin.layouts.master')

@section('title', 'Data IP Address')

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title">Data Range IP Address</h2>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus me-2"></i> Tambah Range IP
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
                                        <th>Base IP</th>
                                        <th>Total IP</th>
                                        <th>Available</th>
                                        <th>In Use</th>
                                        <th>Blocked</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ipRanges as $baseIp => $range)
                                    <tr>
                                        <td>{{ $baseIp }}</td>
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
                                            <a href="{{ route('ip-address.detail', ['baseIp' => $baseIp]) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye text-white"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                    data-bs-target="#editRangeModal{{ str_replace('.', '_', $baseIp) }}">
                                                <i class="bi bi-pencil text-white"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                                    data-bs-target="#deleteRangeModal{{ str_replace('.', '_', $baseIp) }}">
                                                <i class="bi bi-trash"></i>
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
                    <h5 class="modal-title">Tambah IP Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tab navigation -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#rangeTab">Range IP</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#singleTab">Single IP</a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content">
                        <!-- Range IP Tab -->
                        <div class="tab-pane fade show active" id="rangeTab">
                            <form action="{{ route('ip-address.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="base_ip" class="form-label">Base IP Address</label>
                                    <input type="text" class="form-control" id="base_ip" name="base_ip" 
                                        pattern="^(\d{1,3}\.){3}0$" 
                                        placeholder="xxx.xxx.xxx.0"
                                        required>
                                    <small class="text-muted">Format: xxx.xxx.xxx.0</small>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label for="start_range" class="form-label">Start Range</label>
                                        <input type="number" class="form-control" id="start_range" name="start_range" 
                                            min="2" max="255" value="2" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="end_range" class="form-label">End Range</label>
                                        <input type="number" class="form-control" id="end_range" name="end_range" 
                                            min="2" max="255" value="255" required>
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

                        <!-- Single IP Tab -->
                        <div class="tab-pane fade" id="singleTab">
                            <form action="{{ route('ip-address.store-single') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="ip_address" class="form-label">IP Address</label>
                                    <input type="text" class="form-control" id="ip_address" name="ip_address" 
                                        pattern="^(\d{1,3}\.){3}\d{1,3}$" 
                                        placeholder="xxx.xxx.xxx.xxx"
                                        required>
                                    <small class="text-muted">Format: xxx.xxx.xxx.xxx</small>
                                </div>
                                <div class="mb-3">
                                    <label for="single_status" class="form-label">Status</label>
                                    <select class="form-select" id="single_status" name="status" required>
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
        </div>
    </div>

    <!-- Range Modals -->
    @foreach($ipRanges as $baseIp => $range)
        <!-- Edit Range Modal -->
        <div class="modal fade" id="editRangeModal{{ str_replace('.', '_', $baseIp) }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Range IP: {{ $baseIp }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ip-address.update-range') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="base_ip" value="{{ $baseIp }}">
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
        <div class="modal fade" id="deleteRangeModal{{ str_replace('.', '_', $baseIp) }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Range IP: {{ $baseIp }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('ip-address.destroy-range') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="base_ip" value="{{ $baseIp }}">
                        <div class="modal-body">
                            <p>Anda yakin ingin menghapus range IP ini? Semua IP dalam range {{ $baseIp }} akan dihapus.</p>
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
    @endforeach

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const datatable = new DataTable("#table-ip");
        });
    </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.getElementById('table-ip');
            const loadingContainer = document.getElementById('loading-container');
    
            // Simulate data loading (replace with your actual data fetching)
            setTimeout(() => {
                loadingContainer.classList.add('d-none');
                table.classList.remove('d-none');
                const datatable = new DataTable("#table-ip");
            }, 100);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Base IP validation
            document.querySelector('#base_ip').addEventListener('input', function(e) {
                const input = e.target;
                const value = input.value;
                
                const ipPattern = /^(\d{1,3}\.){3}0$/;
                
                if (!ipPattern.test(value)) {
                    input.setCustomValidity('Format IP harus xxx.xxx.xxx.0');
                } else {
                    const octets = value.split('.');
                    const validOctets = octets.every((octet, index) => {
                        const num = parseInt(octet);
                        if (index === 3) return num === 0;
                        return num >= 0 && num <= 255;
                    });
        
                    if (!validOctets) {
                        input.setCustomValidity('Setiap oktet harus bernilai antara 0 dan 255');
                    } else {
                        input.setCustomValidity('');
                    }
                }
            });
        
            // Single IP validation
            document.querySelector('#ip_address').addEventListener('input', function(e) {
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
        });
    </script>
@endsection


