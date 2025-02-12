<div class="table-responsive">
    <table class="table table-sm small table-striped" id="destroyedTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Serial</th>
                <th>Tahun Perolehan</th>
                <th>Kelayakan</th>
                <th>Keterangan Pemusnahan</th>
                <th>Waktu Pemusnahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $tablet)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tablet->model }}</td>
                <td>{{ $tablet->tipe_merk }}</td>
                <td>
                    {{ $tablet->serial }}
                </td>
                <td>{{ \Carbon\Carbon::parse($tablet->tahun_perolehan)->format('M Y') }}</td>
                <td>
                    <div class="progress" style="height: 12px; width: 100px;">
                        <div 
                            class="progress-bar 
                                {{ 
                                    $tablet->kelayakan >= 75 ? 'bg-success' :
                                    ($tablet->kelayakan >= 50 ? 'bg-warning' : 'bg-danger')
                                }}"
                            role="progressbar" 
                            aria-valuenow="{{ $tablet->kelayakan ?? 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100"
                            style="width: {{ $tablet->kelayakan ?? 0 }}%">
                            {{ $tablet->kelayakan ?? '-' }}%
                        </div>
                    </div>
                </td>
                <td title="{{ $tablet->menuPemusnahan->keterangan ?? '-' }}">
                    {{ Str::limit($tablet->menuPemusnahan->keterangan ?? '-', 50) }}
                </td>
                <td>{{ $tablet->menuPemusnahan->created_at ? \Carbon\Carbon::parse($tablet->menuPemusnahan->created_at)->format('d M Y - H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>