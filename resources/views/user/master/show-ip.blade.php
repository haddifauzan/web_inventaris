@extends('admin.layouts.master')

@section('title', 'Detail IP Address')

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title">Detail IP Address - {{ $ipHost->ip_host }}</h2>
                                <p class="text-muted mb-0">Lokasi: {{ $ipHost->lokasi->nama_lokasi }}</p>
                            </div>
                            <a href="{{ route('ip-address.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="loading-container" class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <table class="table table-striped table-sm small table-hover d-none" id="table-ip-detail">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                        <th>Digunakan Oleh</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ipHost->ipAddresses as $ip)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ip->ip_address }}</td>
                                        <td>
                                            @switch($ip->status)
                                                @case('Available')
                                                    <span class="badge bg-success">Available</span>
                                                    @break
                                                @case('In Use')
                                                    <span class="badge bg-primary">In Use</span>
                                                    @break
                                                @case('Blocked')
                                                    <span class="badge bg-danger">Blocked</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($ip->menuAktif)
                                                <strong class="text-bold">{{ $ip->barang->jenis_barang }}: </strong>{{ $ip->menuAktif->komputer_name }}
                                                @if($ip->menuAktif->user)
                                                    <br>
                                                    <strong class="text-bold">User: </strong>{{ $ip->menuAktif->user }}
                                                @endif
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ip->status !== 'In Use')
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                        data-bs-target="#editStatusModal{{ $ip->id_ip }}">
                                                    <i class="bi bi-pencil-fill text-white"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Edit Status Modal -->
                                    <div class="modal fade" id="editStatusModal{{ $ip->id_ip }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Status IP: {{ $ip->ip_address }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('ip-address.update-status', $ip->id_ip) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select class="form-select" id="status" name="status" required>
                                                                <option value="Available" {{ $ip->status === 'Available' ? 'selected' : '' }}>Available</option>
                                                                <option value="Blocked" {{ $ip->status === 'Blocked' ? 'selected' : '' }}>Blocked</option>
                                                            </select>
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.getElementById('table-ip-detail');
            const loadingContainer = document.getElementById('loading-container');
    
            setTimeout(() => {
                loadingContainer.classList.add('d-none');
                table.classList.remove('d-none');
                const datatable = new DataTable("#table-ip-detail");
            }, 100);
        });
    </script>
@endsection