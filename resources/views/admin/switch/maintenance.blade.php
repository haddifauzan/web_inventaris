@extends('admin.layouts.master')

@section('title', 'Data Maintenance')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mx-3">
                        <div>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
                            </button>
                        </div>
                        <a href="{{ route('switch.index', 'aktif') }}" class="btn btn-secondary btn-sm">
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
                        <table class="table table-striped table-sm small table-hover d-none" id="table-maintenance">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Merk/Tipe</th>
                                    <th>Serial</th>
                                    <th>Tanggal Maintenance</th>
                                    <th>Status Maintenance</th>
                                    <th>Node Terpakai</th>
                                    <th>Node Bagus</th>
                                    <th>Node Rusak</th>
                                    <th>Node Kosong</th>
                                    <th>Status Net</th>
                                    <th>Petugas</th>
                                    <th>Lokasi Switch</th>
                                    <th>Keterangan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($maintenances as $maintenance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $maintenance->barang->tipe_merk}}</td>
                                    <td>{{ $maintenance->barang->serial}}</td>
                                    <td>{{ $maintenance->tgl_maintenance ?? "-" }}</td>
                                    <td>
                                        @switch($maintenance->status_maintenance)
                                            @case('Sudah')
                                                <span class="badge bg-success">Sudah</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Belum</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $maintenance->node_terpakai }}</td>
                                    <td>{{ $maintenance->node_bagus }}</td>
                                    <td>{{ $maintenance->node_rusak }}</td>
                                    <td>{{ ($maintenance->node_bagus) - ($maintenance->node_terpakai) }}</td>
                                    <td>
                                        @switch($maintenance->status_net)
                                            @case('OK')
                                                <span class="badge bg-success">OK</span>
                                                @break
                                            @case('Rusak')
                                                <span class="badge bg-danger">Rusak</span>
                                                @break
                                            @default
                                                {{ $maintenance->status_net }}
                                        @endswitch
                                    </td>
                                    <td>{{ $maintenance->petugas ?? "-" }}</td>
                                    <td>{{ $maintenance->lokasi_switch }}</td>
                                    <td>
                                        @if(strlen($maintenance->keterangan) > 20)
                                            <span title="{{ $maintenance->keterangan }}">{{ substr($maintenance->keterangan, 0, 20) }}...</span>
                                        @else
                                            {{ $maintenance->keterangan ?? "-" }}
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" title="Maintenance" 
                                            class="btn btn-warning btn-sm text-white d-flex" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#maintenanceModal{{ $maintenance->id_maintenance }}"
                                            {{ $maintenance->status_maintenance === 'Sudah' ? 'disabled' : '' }}>
                                            <i class="bi bi-tools me-2"></i> Maintenance
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

@foreach($maintenances as $maintenance)
<div class="modal fade" id="maintenanceModal{{ $maintenance->id_maintenance }}" tabindex="-1" aria-labelledby="maintenanceModalLabel{{ $maintenance->id_maintenance }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceModalLabel{{ $maintenance->id_maintenance }}">Form Maintenance - {{ $maintenance->tipe_merk . " (" . $maintenance->lokasi_switch . ")"}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('switch.maintenance.action', $maintenance->id_maintenance) }}" method="POST">
                @csrf
                <input type="hidden" name="id_barang" value="{{ $maintenance->id_barang }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Model</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $maintenance->barang->model }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label col-form-label-sm">Tipe/Merk</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $maintenance->barang->tipe_merk }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label col-form-label-sm">Spesifikasi</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    @php
                                        $spesifikasi = is_string($maintenance->barang->spesifikasi) ? 
                                            json_decode($maintenance->barang->spesifikasi, true) : 
                                            (is_array($maintenance->barang->spesifikasi) ? $maintenance->barang->spesifikasi : []);
                                    @endphp
                                    @if(count($spesifikasi) > 0)
                                        @foreach($spesifikasi as $key => $value)
                                        <tr>
                                            <td style="width: 40%; font-size:0.8rem;" class="text-dark bg-light">{{ $key }}</td>
                                            <td style="font-size:0.8rem;">{{ $value }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" style="font-size:0.8rem;"  class="text-center text-muted">Tidak ada spesifikasi</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label small">Tanggal Maintenance</label>
                            <input type="date" name="tgl_maintenance" class="form-control form-control-sm" 
                                   value="{{ date('Y-m-d') }}" 
                                   id="maintenance-date-{{ $maintenance->id_maintenance }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small">Lokasi Switch</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $maintenance->lokasi_switch }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Terpakai</label>
                            <input type="number" name="node_terpakai" class="form-control form-control-sm" 
                                   value="{{ $maintenance->node_terpakai }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Bagus</label>
                            <input type="number" name="node_bagus" class="form-control form-control-sm" 
                                   value="{{ $maintenance->node_bagus }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Node Rusak</label>
                            <input type="number" name="node_rusak" class="form-control form-control-sm" 
                                   value="{{ $maintenance->node_rusak }}" 
                                   min="0" max="{{ $maintenance->node_terpakai + $maintenance->node_bagus + $maintenance->node_rusak }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small">Petugas Maintenance</label>
                                <div id="petugas-container-{{ $maintenance->id_maintenance }}">
                                    <div class="input-group input-group-sm mb-2">
                                        <input type="text" name="petugas[]" class="form-control form-control-sm petugas-input" placeholder="Nama Petugas">
                                        <button type="button" class="btn btn-danger btn-sm remove-petugas" style="display:none;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-petugas-{{ $maintenance->id_maintenance }}">
                                    <i class="bi bi-plus"></i> Tambah Petugas
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small">Status Jaringan</label>
                                <select name="status_net" class="form-select form-select-sm">
                                    <option value="OK" {{ $maintenance->status_net === 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="Rusak" {{ $maintenance->status_net === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Keterangan Maintenance</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="3">{{ $maintenance->keterangan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Maintenance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($maintenances as $maintenance)
    const petugasContainer{{ $maintenance->id_maintenance }} = document.getElementById('petugas-container-{{ $maintenance->id_maintenance }}');
    const addPetugasBtn{{ $maintenance->id_maintenance }} = document.getElementById('add-petugas-{{ $maintenance->id_maintenance }}');

    addPetugasBtn{{ $maintenance->id_maintenance }}.addEventListener('click', function() {
        const newPetugasGroup = document.createElement('div');
        newPetugasGroup.className = 'input-group mb-2';
        newPetugasGroup.innerHTML = `
            <input type="text" name="petugas[]" class="form-control form-control-sm petugas-input" placeholder="Nama Petugas">
            <button type="button" class="btn btn-danger btn-sm remove-petugas">
                <i class="bi bi-trash"></i>
            </button>
        `;

        petugasContainer{{ $maintenance->id_maintenance }}.appendChild(newPetugasGroup);
        updateRemoveButtons(petugasContainer{{ $maintenance->id_maintenance }});
    });

    petugasContainer{{ $maintenance->id_maintenance }}.addEventListener('click', function(event) {
        if (event.target.closest('.remove-petugas')) {
            event.target.closest('.input-group').remove();
            updateRemoveButtons(petugasContainer{{ $maintenance->id_maintenance }});
        }
    });

    function updateRemoveButtons(container) {
        const removeButtons = container.querySelectorAll('.remove-petugas');
        removeButtons.forEach((button, index) => {
            button.style.display = removeButtons.length > 1 ? 'block' : 'none';
        });
    }
    @endforeach
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const table = document.getElementById('table-maintenance');
        const loadingContainer = document.getElementById('loading-container');

        setTimeout(() => {
            loadingContainer.classList.add('d-none');
            table.classList.remove('d-none');
            const datatable = new DataTable("#table-maintenance");
        }, 100);
    });
</script>
@endsection