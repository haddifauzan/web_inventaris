{{-- edit_full_fields.blade.php --}}
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Model</label>
        <input type="text" class="form-control form-control-sm" value="{{ $tablet->model }}" disabled>
    </div>
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Tipe/Merk</label>
        <input type="text" class="form-control form-control-sm" value="{{ $tablet->tipe_merk }}" disabled>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Lokasi</label>
        <div class="select-search-container">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" id="aktivasi-lokasi-search{{ $tablet->id_barang }}" 
                    placeholder="Cari dan pilih lokasi..." autocomplete="off">
                <button class="btn btn-secondary" type="button" id="aktivasi-clear-lokasi-search{{ $tablet->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <input type="hidden" name="id_lokasi" id="aktivasi-lokasi-value{{ $tablet->id_barang }}" required>
            <div class="select-search-dropdown" id="aktivasi-lokasi-dropdown{{ $tablet->id_barang }}">
                @foreach($lokasi as $lok)
                <div class="select-search-option" data-value="{{ $lok->id_lokasi }}"
                    @if($tablet->id_lokasi == $lok->id_lokasi) class="select-search-selected" @endif>
                    {{ $lok->nama_lokasi }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Departemen</label>
        <div class="select-search-container">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" id="aktivasi-departemen-search{{ $tablet->id_barang }}" 
                    placeholder="Cari dan pilih departemen..." autocomplete="off">
                <button class="btn btn-secondary" type="button" id="aktivasi-clear-departemen-search{{ $tablet->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <input type="hidden" name="id_departemen" id="aktivasi-departemen-value{{ $tablet->id_barang }}" required>
            <div class="select-search-dropdown" id="aktivasi-departemen-dropdown{{ $tablet->id_barang }}">
                @foreach($departemen as $dep)
                <div class="select-search-option" data-value="{{ $dep->id_departemen }}"
                    @if($tablet->id_departemen == $dep->id_departemen) class="select-search-selected" @endif>
                    {{ $dep->nama_departemen }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">User</label>
        <input type="text" class="form-control form-control-sm" name="user" placeholder="Contoh: John Doe" required>
    </div>
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Pilih IP Address</label>
        <div class="ip-selection-container mt-2">
            <div class="input-group input-group-sm mb-2">
                <input type="text" id="aktivasi-ip-search-input{{ $tablet->id_barang }}" class="form-control" 
                        placeholder="Cari IP Address baru...">
                <button class="btn btn-secondary" type="button" id="aktivasi-clear-ip-search{{ $tablet->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="ip-list-container">
                <ul id="aktivasi-ip-address-list{{ $tablet->id_barang }}" class="ip-list">
                    <li class="no-results">Pilih lokasi terlebih dahulu</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="mb-3">
    <label class="form-label col-form-label-sm">Keterangan</label>
    <textarea class="form-control form-control-sm" name="keterangan" rows="3" placeholder="Masukkan keterangan jika diperlukan"></textarea>
</div>