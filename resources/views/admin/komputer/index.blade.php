@extends('admin.layouts.master')
@section('title', 'Data Komputer')
@section('content')

<style>
    .nav-tabs .nav-link {
        color: black; /* Warna teks default */
        font-weight: normal; /* Normal jika tidak aktif */
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd !important; /* Warna primary Bootstrap */
        font-weight: bold; /* Tebal saat aktif */
    }

    /* Styling untuk loading */
    #loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100px;
    }
</style>

<section class="section">
    <div class="row">
        <section class="col-12">
            <div class="card p-3">
                <div class="card-body">
                    <!-- Tabs dengan Route -->
                    <ul class="nav nav-tabs" id="computerTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'barang' ? 'active' : '' }}" 
                               href="{{ route('komputer.index', 'barang') }}">Data Barang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'backup' ? 'active' : '' }}" 
                               href="{{ route('komputer.index', 'backup') }}">Menu Backup</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'aktif' ? 'active' : '' }}" 
                               href="{{ route('komputer.index', 'aktif') }}">Menu Aktif</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'pemusnahan' ? 'active' : '' }}" 
                               href="{{ route('komputer.index', 'pemusnahan') }}">Menu Pemusnahan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'riwayat' ? 'active' : '' }}" 
                               href="{{ route('komputer.index', 'riwayat') }}">Riwayat Penggunaan</a>
                        </li>
                    </ul>

                    <!-- Loading Spinner -->
                    <div id="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <!-- Konten Tabs -->
                    <div id="table-container" class="tab-content mt-3" style="display: none;">
                        <div class="tab-pane fade show active">
                            @include("admin.komputer.tabs.$tab", ['data' => $data])
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tables = [
            { id: 'activeTable' },
            { id: 'backupTable' },
            { id: 'historyTable' },
            @foreach($data as $barang)
            { id: 'historyDetailTable{{ $barang->id_barang }}' },
            @endforeach
            { id: 'destroyedTable' }
        ];

        tables.forEach(table => {
            const tableElement = document.getElementById(table.id);
            if (tableElement) {
                const dataTable = $(tableElement).DataTable();
            }
        });
    const totalTables = tables.length;
    let loadedTables = 0;

    // Function to check if all tables are loaded
    function checkAllTablesLoaded() {
        loadedTables++;
        if (loadedTables >= totalTables) {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.style.display = 'none'; // Sembunyikan spinner
            }
            document.getElementById('table-container').style.display = 'block'; // Tampilkan konten tabel
        }
    }

    // Show loading spinner initially
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = 'flex';
    }

    // Initialize each table
    tables.forEach(table => {
        const tableId = table.id;
        if (tableId) {
            try {
                const dataTable = $(`#${tableId}`).DataTable({
                    ...dataTableOptions,
                    drawCallback: function() {
                        checkAllTablesLoaded(); // Panggil fungsi untuk memeriksa apakah semua tabel sudah dimuat
                    }
                });
            } catch (error) {
                checkAllTablesLoaded(); // Pastikan untuk memanggil ini meskipun ada error
            }
        }
    });
});
</script>

@endsection