@extends('admin.layouts.master')

@section('title', 'Data Lokasi')

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title">Data Lokasi</h2>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus me-2"></i> Tambah Lokasi
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="loading-container" class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <table class="table table-sm small table-striped table-hover d-none" id="table-lokasi">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Lokasi</th>
                                        <th>Deskripsi</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lokasi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->nama_lokasi }}</td>
                                        <td>{{ $item->deskripsi ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id_lokasi }}">
                                                <i class="bi bi-pencil-fill text-white"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id_lokasi }}">
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
                    <h5 class="modal-title">Tambah Lokasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('lokasi.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
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

    <!-- Edit Modals -->
    @foreach($lokasi as $item)
    <div class="modal fade" id="editModal{{ $item->id_lokasi }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Lokasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('lokasi.update', $item->id_lokasi) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" value="{{ $item->nama_lokasi }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ $item->deskripsi }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal{{ $item->id_lokasi }}" tabindex="-1">
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
                    <form action="{{ route('lokasi.destroy', $item->id_lokasi) }}" method="POST">
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
            const datatable = new DataTable("#table-lokasi");
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.getElementById('table-lokasi');
            const loadingContainer = document.getElementById('loading-container');

            // Simulate data loading (replace with your actual data fetching)
            setTimeout(() => {
                loadingContainer.classList.add('d-none');
                table.classList.remove('d-none');
                const datatable = new DataTable("#table-lokasi");
            }, 100);
        });
    </script>
@endsection


