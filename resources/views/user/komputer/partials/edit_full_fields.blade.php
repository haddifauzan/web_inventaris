{{-- edit_full_fields.blade.php --}}
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Model</label>
        <input type="text" class="form-control form-control-sm" value="{{ $computer->model }}" disabled>
    </div>
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Tipe/Merk</label>
        <input type="text" class="form-control form-control-sm" value="{{ $computer->tipe_merk }}" disabled>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Lokasi</label>
        <div class="select-search-container">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" id="aktivasi-lokasi-search{{ $computer->id_barang }}" 
                    placeholder="Cari dan pilih lokasi..." autocomplete="off">
                <button class="btn btn-secondary" type="button" id="aktivasi-clear-lokasi-search{{ $computer->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <input type="hidden" name="id_lokasi" id="aktivasi-lokasi-value{{ $computer->id_barang }}" required>
            <div class="select-search-dropdown" id="aktivasi-lokasi-dropdown{{ $computer->id_barang }}">
                @foreach($lokasi as $lok)
                <div class="select-search-option" data-value="{{ $lok->id_lokasi }}"
                    @if($computer->id_lokasi == $lok->id_lokasi) class="select-search-selected" @endif>
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
                <input type="text" class="form-control" id="aktivasi-departemen-search{{ $computer->id_barang }}" 
                    placeholder="Cari dan pilih departemen..." autocomplete="off">
                <button class="btn btn-secondary" type="button" id="aktivasi-clear-departemen-search{{ $computer->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <input type="hidden" name="id_departemen" id="aktivasi-departemen-value{{ $computer->id_barang }}" required>
            <div class="select-search-dropdown" id="aktivasi-departemen-dropdown{{ $computer->id_barang }}">
                @foreach($departemen as $dep)
                <div class="select-search-option" data-value="{{ $dep->id_departemen }}"
                    @if($computer->id_departemen == $dep->id_departemen) class="select-search-selected" @endif>
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
        <label class="form-label col-form-label-sm">Komputer Name</label>
        <input type="text" class="form-control form-control-sm" name="komputer_name" placeholder="Contoh: PC-001" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label col-form-label-sm">Pilih IP Address</label>
        <div class="ip-selection-container mt-2">
            <div class="input-group input-group-sm mb-2">
                <input type="text" id="aktivasi-ip-search-input{{ $computer->id_barang }}" class="form-control" 
                        placeholder="Cari IP Address baru...">
                <button class="btn btn-secondary" type="button" id="aktivasi-clear-ip-search{{ $computer->id_barang }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="ip-list-container">
                <ul id="aktivasi-ip-address-list{{ $computer->id_barang }}" class="ip-list">
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