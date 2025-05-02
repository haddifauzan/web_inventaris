@extends('admin.layouts.master')
@section('title', 'Tentang Aplikasi')
@section('content')
<div class="container-fluid">
    <!-- Judul Aplikasi -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <div class="card border-0 shadow">
                <div class="card-body py-5">
                    <h1 class="display-5 fw-bold text-dark">RHGIS</h1>
                    <h3 class="text-secondary">Rajawali Hiyoto Gadget Inventory System</h3>
                    <p class="lead mt-3">Solusi terpadu untuk manajemen dan pelacakan aset IT perusahaan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Deskripsi Singkat -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <h4 class="card-title border-bottom pb-2"><i class="bi bi-info-circle me-2"></i>Tentang RHGIS</h4>
                    <p class="card-text">
                        RHGIS (Rajawali Hiyoto Gadget Inventory System) adalah sistem informasi manajemen inventaris yang dirancang khusus untuk memudahkan pengelolaan, pelacakan, dan pelaporan seluruh aset IT perusahaan. Sistem ini mengelola siklus hidup lengkap perangkat elektronik, mulai dari pendaftaran awal, aktivasi, backup, hingga pemusnahan dengan antarmuka yang intuitif dan terorganisir.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fitur Utama -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header bg-light py-3">
                    <h4 class="mb-0"><i class="bi bi-gear-fill me-2"></i>Fitur Utama Aplikasi</h4>
                </div>
                <div class="card-body p-0">
                    <div class="accordion" id="fiturAccordion">
                        <!-- Dashboard -->
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="headingDashboard">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDashboard" aria-expanded="true" aria-controls="collapseDashboard">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </button>
                            </h2>
                            <div id="collapseDashboard" class="accordion-collapse collapse show" aria-labelledby="headingDashboard" data-bs-parent="#fiturAccordion">
                                <div class="accordion-body">
                                    <p>Dashboard komprehensif yang menampilkan:</p>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item bg-transparent">Ringkasan status seluruh barang inventaris (aktif, backup, dan dimusnahkan)</li>
                                        <li class="list-group-item bg-transparent">Informasi data master seperti lokasi, departemen, IP address, dan kategori barang</li>
                                        <li class="list-group-item bg-transparent">Statistik dan visualisasi untuk pemantauan inventaris secara real-time</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Manajemen Komputer -->
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="headingKomputer">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKomputer" aria-expanded="false" aria-controls="collapseKomputer">
                                    <i class="bi bi-pc-display me-2"></i>Manajemen Komputer
                                </button>
                            </h2>
                            <div id="collapseKomputer" class="accordion-collapse collapse" aria-labelledby="headingKomputer" data-bs-parent="#fiturAccordion">
                                <div class="accordion-body">
                                    <p>Sistem lengkap untuk pelacakan dan pengelolaan seluruh komputer:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item bg-transparent">Pendaftaran komputer baru ke inventaris</li>
                                                <li class="list-group-item bg-transparent">Aktivasi komputer untuk penggunaan operasional</li>
                                                <li class="list-group-item bg-transparent">Pengelolaan status backup untuk komputer cadangan</li>
                                                <li class="list-group-item bg-transparent">Proses pemusnahan untuk aset yang tidak layak</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item bg-transparent">Filter data berdasarkan berbagai parameter</li>
                                                <li class="list-group-item bg-transparent">Ekspor data ke format Excel untuk pelaporan</li>
                                                <li class="list-group-item bg-transparent">Penelusuran riwayat penggunaan setiap aset</li>
                                                <li class="list-group-item bg-transparent">Pencatatan perubahan status secara otomatis</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manajemen Tablet -->
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="headingTablet">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTablet" aria-expanded="false" aria-controls="collapseTablet">
                                    <i class="bi bi-tablet me-2"></i>Manajemen Tablet
                                </button>
                            </h2>
                            <div id="collapseTablet" class="accordion-collapse collapse" aria-labelledby="headingTablet" data-bs-parent="#fiturAccordion">
                                <div class="accordion-body">
                                    <p>Pengelolaan lengkap untuk seluruh perangkat tablet:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item bg-transparent">Penambahan tablet baru ke sistem inventaris</li>
                                                <li class="list-group-item bg-transparent">Pengaktifan tablet untuk penggunaan operasional</li>
                                                <li class="list-group-item bg-transparent">Manajemen status backup untuk tablet cadangan</li>
                                                <li class="list-group-item bg-transparent">Dokumentasi pemusnahan untuk aset tidak layak</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item bg-transparent">Filter dan pencarian data yang fleksibel</li>
                                                <li class="list-group-item bg-transparent">Pembuatan laporan dalam format Excel</li>
                                                <li class="list-group-item bg-transparent">Pelacakan riwayat penggunaan setiap tablet</li>
                                                <li class="list-group-item bg-transparent">Rekaman perubahan status secara kronologis</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manajemen Switch -->
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="headingSwitch">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSwitch" aria-expanded="false" aria-controls="collapseSwitch">
                                    <i class="bi bi-hdd-network me-2"></i>Manajemen Switch
                                </button>
                            </h2>
                            <div id="collapseSwitch" class="accordion-collapse collapse" aria-labelledby="headingSwitch" data-bs-parent="#fiturAccordion">
                                <div class="accordion-body">
                                    <p>Sistem pelacakan dan pengelolaan perangkat switch jaringan:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item bg-transparent">Pendaftaran switch baru ke dalam inventaris</li>
                                                <li class="list-group-item bg-transparent">Pengaktifan switch untuk operasional jaringan</li>
                                                <li class="list-group-item bg-transparent">Pengelolaan status backup untuk switch cadangan</li>
                                                <li class="list-group-item bg-transparent">Proses pemusnahan untuk switch yang tidak berfungsi</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item bg-transparent">Pencatatan maintenance untuk perawatan rutin</li>
                                                <li class="list-group-item bg-transparent">Filter dan ekspor data untuk pelaporan</li>
                                                <li class="list-group-item bg-transparent">Dokumentasi riwayat penggunaan setiap switch</li>
                                                <li class="list-group-item bg-transparent">Pelacakan kronologis perubahan status</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alur Kerja -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header bg-light py-3">
                    <h4 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Alur Kerja Sistem</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="timeline">
                                <div class="row g-0">
                                    <div class="col-sm-3 text-center border-end">
                                        <div class="rounded-circle bg-primary p-3 text-white d-inline-block mb-3">
                                            <i class="bi bi-plus-circle-fill fs-4"></i>
                                        </div>
                                        <h5>Penambahan Barang</h5>
                                    </div>
                                    <div class="col-sm-3 text-center border-end">
                                        <div class="rounded-circle bg-success p-3 text-white d-inline-block mb-3">
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                        </div>
                                        <h5>Aktivasi Barang</h5>
                                    </div>
                                    <div class="col-sm-3 text-center border-end">
                                        <div class="rounded-circle bg-warning p-3 text-white d-inline-block mb-3">
                                            <i class="bi bi-arrow-repeat fs-4"></i>
                                        </div>
                                        <h5>Backup Barang</h5>
                                    </div>
                                    <div class="col-sm-3 text-center">
                                        <div class="rounded-circle bg-danger p-3 text-white d-inline-block mb-3">
                                            <i class="bi bi-x-circle-fill fs-4"></i>
                                        </div>
                                        <h5>Pemusnahan Barang</h5>
                                    </div>
                                </div>
                                <div class="progress mt-4" style="height: 4px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="row g-0 mt-4">
                                    <div class="col-sm-3">
                                        <p class="text-muted small">Pendaftaran barang baru masuk ke dalam sistem inventaris untuk pelacakan awal.</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <p class="text-muted small">Pengaktifan barang untuk digunakan dalam operasional perusahaan.</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <p class="text-muted small">Pengalihan status ke backup untuk barang yang sedang tidak digunakan.</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <p class="text-muted small">Pemusnahan dan dokumentasi barang yang sudah tidak layak digunakan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manfaat Sistem -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header bg-light py-3">
                    <h4 class="mb-0"><i class="bi bi-patch-check me-2"></i>Manfaat Sistem RHGIS</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Meningkatkan Efisiensi Operasional</h5>
                                    <p class="text-muted">Memudahkan pelacakan dan manajemen aset IT secara real-time, mengurangi waktu yang dibutuhkan untuk mencari dan mengidentifikasi perangkat.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                                        <i class="bi bi-cash-coin"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Mengoptimalkan Penggunaan Aset</h5>
                                    <p class="text-muted">Membantu memaksimalkan investasi IT dengan memastikan perangkat digunakan secara optimal dan mendokumentasikan siklus hidup lengkap tiap aset.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                                        <i class="bi bi-files"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Mempermudah Pelaporan dan Audit</h5>
                                    <p class="text-muted">Menghasilkan laporan terperinci dengan mudah untuk kebutuhan audit, perencanaan anggaran, dan pengambilan keputusan strategis.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Meningkatkan Keamanan dan Kepatuhan</h5>
                                    <p class="text-muted">Mengelola dan melacak semua aset IT dengan aman, memastikan kepatuhan terhadap kebijakan perusahaan dan regulasi yang berlaku.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection