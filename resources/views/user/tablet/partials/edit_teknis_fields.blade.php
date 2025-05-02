<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Lokasi Saat Ini</label>
        <div class="current-value-container mb-2">
            @if($tablet->id_lokasi)
                <input type="text" class="form-control form-control-sm" value="{{ $tablet->nama_lokasi }}" disabled>
            @else
                <span class="no-value">Belum ada lokasi</span>
            @endif
        </div>

        <label class="form-label col-form-label-sm">Pilih Lokasi Baru</label>
        <div class="select-search-container">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" id="teknis-lokasi-search{{ $tablet->id_barang }}" 
                    placeholder="Cari dan pilih lokasi..." autocomplete="off">
                <button class="btn btn-secondary" type="button" id="teknis-clear-lokasi-search{{ $tablet->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <input type="hidden" name="id_lokasi" id="teknis-lokasi-value{{ $tablet->id_barang }}">
            <div class="select-search-dropdown" id="teknis-lokasi-dropdown{{ $tablet->id_barang }}">
                @foreach($lokasi as $lok)
                <div class="select-search-option" data-value="{{ $lok->id_lokasi }}">
                    {{ $lok->nama_lokasi }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Departemen Saat Ini</label>
        <div class="current-value-container mb-2">
            @if($tablet->id_departemen)
                <input type="text" class="form-control form-control-sm" value="{{ $tablet->nama_departemen }}" disabled>
            @else
                <span class="no-value">Belum ada departemen</span>
            @endif
        </div>

        <label class="form-label col-form-label-sm">Pilih Departemen Baru</label>
        <div class="select-search-container">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" id="teknis-departemen-search{{ $tablet->id_barang }}" 
                    placeholder="Cari dan pilih departemen..." autocomplete="off">
                <button class="btn btn-secondary" type="button" id="teknis-clear-departemen-search{{ $tablet->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <input type="hidden" name="id_departemen" id="teknis-departemen-value{{ $tablet->id_barang }}">
            <div class="select-search-dropdown" id="teknis-departemen-dropdown{{ $tablet->id_barang }}">
                @foreach($departemen as $dep)
                <div class="select-search-option" data-value="{{ $dep->id_departemen }}">
                    {{ $dep->nama_departemen }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row my-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">IP Address Saat Ini</label>
        <div class="ip-list-container">
            <ul class="ip-list">
                @if($tablet->id_ip)
                    <li>
                        <input type="radio" checked disabled>
                        <label style="margin-left: 10px">
                            {{ $tablet->menuAktif->first()->ipAddress->ip_address ?? '-' }}
                        </label>
                    </li>
                @else
                    <li class="no-results">Tidak ada IP Address terpasang</li>
                @endif
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Pilih IP Address Baru</label>
        <div class="ip-selection-container mt-2">
            <div class="input-group input-group-sm mb-2">
                <input type="text" id="teknis-ip-search-input{{ $tablet->id_barang }}" class="form-control" 
                        placeholder="Cari IP Address baru...">
                <button class="btn btn-secondary" type="button" id="teknis-clear-ip-search{{ $tablet->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="ip-list-container">
                <ul id="teknis-ip-address-list{{ $tablet->id_barang }}" class="ip-list">
                    <li class="no-results">Pilih lokasi terlebih dahulu</li>
                </ul>
            </div>
        </div>
    </div>
</div>