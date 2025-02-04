@extends('admin.layouts.master')

@section('title', 'Detail IP Address')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title">Detail IP Range: {{ $baseIp }}</h2>
                    <a href="{{ route('ip-address.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Cari IP Address...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select id="statusFilter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Available">Available</option>
                                <option value="In Use">In Use</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                        </div>
                    </div>

                    <!-- Spinner Loading -->
                    <div id="loadingSpinner" class="text-center my-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <div class="table-responsive" id="tableContainer" style="display: none;">
                        <table class="table table-sm small table-striped" id="ipTable">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                    <th>Jenis Barang</th>
                                    <th>Model</th>
                                    <th>Tipe/Merk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ips as $ip)
                                <tr>
                                    <td>{{ $ip->ip_address }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ip->status === 'Available' ? 'success' : ($ip->status === 'In Use' ? 'primary' : 'danger') }}">
                                            {{ $ip->status }}
                                        </span>
                                    </td>
                                    <td>{{ $ip->barang()->exists() ? $ip->barang->jenis_barang : '-' }}</td>
                                    <td>{{ $ip->barang()->exists() ? $ip->barang->model : '-' }}</td>
                                    <td>{{ $ip->barang()->exists() ? $ip->barang->tipe_merk : '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning edit-ip" 
                                                data-bs-toggle="modal" data-bs-target="#editIpModal{{ $ip->id_ip }}">
                                            <i class="bi bi-pencil text-white"></i>
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

@foreach($ips as $ip)
    <div class="modal fade" id="editIpModal{{ $ip->id_ip }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit IP Address: <span id="modalIpAddress"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editIpForm" action="{{ route('ip-address.update-status', $ip->id_ip) }}" method="POST">
                    @csrf
                    <input type="hidden" id="ipId" name="ip_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="Available" {{ $ip->status == 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="In Use" {{ $ip->status == 'In Use' ? 'selected' : '' }}>In Use</option>
                                <option value="Blocked" {{ $ip->status == 'Blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                            <p class="mt-2">IP Address: <span id="modalIpValue">{{ $ip->ip_address }}</span></p>
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
document.addEventListener('DOMContentLoaded', function() {
    // Tampilkan spinner, lalu muat tabel setelah delay
    setTimeout(function() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('tableContainer').style.display = 'block';
    }, 1500);

    // Initialize DataTable
    const table = new DataTable('#ipTable', {
        pageLength: 25,
        ordering: false,
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        table.search(this.value).draw();
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        table.column(1).search(this.value).draw();
    });
});
</script>
@endsection