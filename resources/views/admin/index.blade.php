@extends('admin.layouts.master')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-3">
    <!-- Header Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl">
            <div class="card border-start border-4 border-primary shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="small fw-bold text-primary text-uppercase mb-1">Total Barang</div>
                            <div class="h5 mb-0 fw-bold">{{ $totalBarang }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fs-1 text-primary opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card border-start border-4 border-info shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="small fw-bold text-info text-uppercase mb-1">Barang Baru</div>
                            <div class="h5 mb-0 fw-bold">{{ $barangBaru }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-plus-circle fs-1 text-info opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card border-start border-4 border-success shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="small fw-bold text-success text-uppercase mb-1">Barang Aktif</div>
                            <div class="h5 mb-0 fw-bold">{{ $barangAktif }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check2-circle fs-1 text-success opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card border-start border-4 border-warning shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="small fw-bold text-warning text-uppercase mb-1">Barang Backup</div>
                            <div class="h5 mb-0 fw-bold">{{ $barangBackup }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-archive fs-1 text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card border-start border-4 border-danger shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="small fw-bold text-danger text-uppercase mb-1">Barang Pemusnahan</div>
                            <div class="h5 mb-0 fw-bold">{{ $barangPemusnahan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-trash3 fs-1 text-danger opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row 1 -->
    <div class="row g-3 mb-4">
        <!-- Distribusi Barang Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-pie-chart me-1"></i> Distribusi Jenis Barang
                    </h6>
                </div>
                <div class="card-body">
                    <div id="distribusiBarangChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>

        <!-- Status Barang Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-clipboard-data me-1"></i> Status Barang
                    </h6>
                </div>
                <div class="card-body">
                    <div id="statusBarangChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-3 mb-4">
        <!-- Kelayakan Komputer Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-laptop me-1"></i> Kelayakan Komputer
                    </h6>
                </div>
                <div class="card-body">
                    <div id="kelayakanKomputerChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>

        <!-- Tahun Perolehan Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-calendar-date me-1"></i> Tahun Perolehan Barang
                    </h6>
                </div>
                <div class="card-body">
                    <div id="tahunPerolehanChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row 3 -->
    <div class="row g-3 mb-4">
        <!-- Barang per Departemen Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-diagram-3 me-1"></i> Barang per Departemen
                    </h6>
                </div>
                <div class="card-body">
                    <div id="barangPerDepartemenChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>

        <!-- Barang per Lokasi Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-geo-alt me-1"></i> Barang per Lokasi
                    </h6>
                </div>
                <div class="card-body">
                    <div id="barangPerLokasiChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- IP Address Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-ethernet me-1"></i> Statistik IP Address
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <div class="card bg-light border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <i class="bi bi-globe2 fs-1 text-secondary mb-2"></i>
                                    <h6 class="card-title small text-muted mb-1">Total IP</h6>
                                    <p class="card-text h3 fw-bold">{{ $ipStats['total'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card bg-success bg-opacity-10 border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <i class="bi bi-check-circle fs-1 text-success mb-2"></i>
                                    <h6 class="card-title small text-muted mb-1">IP Terpakai</h6>
                                    <p class="card-text h3 fw-bold text-success">{{ $ipStats['used'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card bg-info bg-opacity-10 border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <i class="bi bi-plus-circle fs-1 text-info mb-2"></i>
                                    <h6 class="card-title small text-muted mb-1">IP Tersedia</h6>
                                    <p class="card-text h3 fw-bold text-info">{{ $ipStats['available'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card bg-danger bg-opacity-10 border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <i class="bi bi-x-circle fs-1 text-danger mb-2"></i>
                                    <h6 class="card-title small text-muted mb-1">IP Diblokir</h6>
                                    <p class="card-text h3 fw-bold text-danger">{{ $ipStats['blocked'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row 1 -->
    <div class="row g-3 mb-4">
        <!-- Barang Baru Table -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-clock-history me-1"></i> Barang Terakhir Ditambahkan
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Jenis</th>
                                    <th>Model</th>
                                    <th>Tipe/Merk</th>
                                    <th>Status</th>
                                    <th class="pe-3">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangTerbaru as $barang)
                                <tr>
                                    <td class="ps-3">{{ $barang->jenis_barang }}</td>
                                    <td>{{ $barang->model }}</td>
                                    <td>{{ $barang->tipe_merk }}</td>
                                    <td>
                                        @if($barang->status == 'Baru')
                                            <span class="badge bg-info">Baru</span>
                                        @elseif($barang->status == 'Aktif')
                                            <span class="badge bg-primary">Aktif</span>
                                        @elseif($barang->status == 'Backup')
                                            <span class="badge bg-success">Backup</span>
                                        @elseif($barang->status == 'Pemusnahan')
                                            <span class="badge bg-danger">Pemusnahan</span>
                                        @endif
                                    </td>
                                    <td class="pe-3">{{ $barang->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Komputer Kelayakan Rendah Table -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-exclamation-triangle me-1"></i> Komputer dengan Kelayakan Rendah
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Model</th>
                                    <th>Tipe/Merk</th>
                                    <th>Kepemilikan</th>
                                    <th>Kelayakan</th>
                                    <th class="pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($komputerKelayakanRendah as $komputer)
                                <tr>
                                    <td class="ps-3">{{ $komputer->model }}</td>
                                    <td>{{ $komputer->tipe_merk }}</td>
                                    <td>{{ $komputer->kepemilikan?? 'N/A' }}</td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $komputer->kelayakan }}%;" aria-valuenow="{{ $komputer->kelayakan }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="mt-1 d-inline-block">{{ $komputer->kelayakan }}%</small>
                                    </td>
                                    <td>
                                        @if($barang->status == 'Baru')
                                            <span class="badge bg-info">Baru</span>
                                        @elseif($barang->status == 'Aktif')
                                            <span class="badge bg-primary">Aktif</span>
                                        @elseif($barang->status == 'Backup')
                                            <span class="badge bg-success">Backup</span>
                                        @elseif($barang->status == 'Pemusnahan')
                                            <span class="badge bg-danger">Pemusnahan</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @if($komputerKelayakanRendah->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                                        <p class="mb-0">Tidak ada komputer dengan kelayakan rendah</p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent History Row -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-clock me-1"></i> Riwayat Penggunaan Terbaru
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Barang</th>
                                    <th>User</th>
                                    <th>Departemen</th>
                                    <th>Lokasi</th>
                                    <th>Waktu Mulai</th>
                                    <th class="pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRiwayat as $riwayat)
                                <tr>
                                    <td class="ps-3">{{ $riwayat->model }} - {{ $riwayat->tipe_merk }}</td>
                                    <td>{{ $riwayat->user }}</td>
                                    <td>{{ $riwayat->nama_departemen }}</td>
                                    <td>{{ $riwayat->nama_lokasi }}</td>
                                    <td>{{ \Carbon\Carbon::parse($riwayat->waktu_awal)->format('d M Y') }}</td>
                                    <td class="pe-3">
                                        @if($riwayat->status == 'Aktif')
                                            <span class="badge bg-primary">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Non-Aktif</span>
                                        @endif
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

    <!-- Maintenance Activity Table -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-tools me-1"></i> Aktivitas Maintenance Switch Terbaru
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Hari/Tanggal</th>
                                    <th>Barang</th>
                                    <th>Lokasi</th>
                                    <th>Status Net</th>
                                    <th>Node Terpakai</th>
                                    <th>Node Bagus</th>
                                    <th class="pe-3">Node Rusak</th>
                                    <th>Node Kosong</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($maintenanceTerbaru as $maintenance)
                                <tr>
                                    <td class="ps-3">{{ \Carbon\Carbon::parse($maintenance->tgl_maintenance)->locale('id')->isoFormat('dddd, D MMM Y') }}</td>
                                    <td>{{ $maintenance->barang->model }} - {{ $maintenance->barang->tipe_merk }}</td>
                                    <td>{{ $maintenance->lokasi_switch }}</td>
                                    <td>
                                        @if($maintenance->status_net == 'OK')
                                            <span class="badge bg-success">OK</span>
                                        @else
                                            <span class="badge bg-danger">Rusak</span>
                                        @endif
                                    </td>
                                    <td>{{ $maintenance->node_terpakai }}</td>
                                    <td>{{ $maintenance->node_bagus }}</td>
                                    <td class="pe-3">{{ $maintenance->node_rusak }}</td>
                                    <td>{{ $maintenance->node_bagus - $maintenance->node_terpakai }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Distribusi Jenis Barang Chart
    var distribusiOptions = {
        series: [{{ $totalKomputer }}, {{ $totalTablet }}, {{ $totalSwitch }}],
        chart: {
            type: 'pie',
            height: 300,
            fontFamily: 'inherit',
            toolbar: {
                show: false
            }
        },
        labels: ['Komputer', 'Tablet', 'Switch'],
        colors: ['#4B0082', '#1E90FF', '#FFA500'],
        legend: {
            position: 'bottom',
            fontSize: '14px'
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return Math.round(val) + '%'
            },
            style: {
                fontSize: '12px',
                fontFamily: 'inherit',
                fontWeight: 'normal'
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 260
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    var distribusiBarangChart = new ApexCharts(document.querySelector("#distribusiBarangChart"), distribusiOptions);
    distribusiBarangChart.render();

    // Status Barang Chart
    var statusOptions = {
        series: [{{ $barangAktif }}, {{ $barangBackup }}, {{ $barangPemusnahan }}, {{ $barangBaru }}],
        chart: {
            type: 'pie',
            height: 300,
            fontFamily: 'inherit',
            toolbar: {
                show: false
            }
        },
        labels: ['Aktif', 'Backup', 'Pemusnahan', 'Baru'],
        colors: ['#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc'],
        legend: {
            position: 'bottom',
            fontSize: '14px'
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return Math.round(val) + '%'
            },
            style: {
                fontSize: '12px',
                fontFamily: 'inherit', 
                fontWeight: 'normal'
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 260
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    var statusBarangChart = new ApexCharts(document.querySelector("#statusBarangChart"), statusOptions);
    statusBarangChart.render();

    // Kelayakan Komputer Chart
    var kelayakanOptions = {
        series: [{
            name: 'Jumlah Komputer',
            data: [{{ implode(',', $kelayakanKomputerData) }}]
        }],
        chart: {
            type: 'bar',
            height: 280,
            fontFamily: 'inherit',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '60%',
                distributed: true
            }
        },
        colors: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#e74a3b'],
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '12px',
                fontFamily: 'inherit',
                fontWeight: 'normal'
            }
        },
        xaxis: {
            categories: ['90-100%', '80-89%', '70-79%', '60-69%', '<60%'],
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah Komputer',
                style: {
                    fontSize: '14px',
                    fontFamily: 'inherit'
                }
            },
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " unit"
                }
            }
        },
        legend: {
            show: false
        }
    };
    var kelayakanKomputerChart = new ApexCharts(document.querySelector("#kelayakanKomputerChart"), kelayakanOptions);
    kelayakanKomputerChart.render();

    // Barang per Lokasi Chart
    var lokasiOptions = {
        series: [{
            name: 'Jumlah Barang',
            data: [{{ $barangPerLokasiData }}]
        }],
        chart: {
            type: 'bar',
            height: 300
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        colors: ['#36b9cc'],
        dataLabels: {
            enabled: true
        },
        xaxis: {
            categories: [{!! $barangPerLokasiLabels !!}],
        },
        yaxis: {
            title: {
                text: 'Lokasi'
            }
        }
    };
    var barangPerLokasiChart = new ApexCharts(document.querySelector("#barangPerLokasiChart"), lokasiOptions);
    barangPerLokasiChart.render();

    // Barang per Departemen Chart
    var departemenOptions = {
        series: [{
            name: 'Jumlah Barang',
            data: [{{ $departemenValues }}]
        }],
        chart: {
            type: 'bar',
            height: 300
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        colors: ['#1cc88a'],
        dataLabels: {
            enabled: true
        },
        xaxis: {
            categories: [{!! $departemenLabels !!}],
        },
        yaxis: {
            title: {
                text: 'Departemen'
            }
        }
    };
    var barangPerDepartemenChart = new ApexCharts(document.querySelector("#barangPerDepartemenChart"), departemenOptions);
    barangPerDepartemenChart.render();

    // Tahun Perolehan Chart
    var tahunOptions = {
        series: [{
            name: 'Jumlah Barang',
            data: [{{ $acquisitionYearValues }}]
        }],
        chart: {
            type: 'area',
            height: 300
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
            }
        },
        colors: ['#f6c23e'],
        markers: {
            size: 4
        },
        xaxis: {
            categories: [{!! $acquisitionYearLabels !!}],
        },
        yaxis: {
            title: {
                text: 'Jumlah Barang'
            }
        }
    };
    var tahunPerolehanChart = new ApexCharts(document.querySelector("#tahunPerolehanChart"), tahunOptions);
    tahunPerolehanChart.render();
});
</script>