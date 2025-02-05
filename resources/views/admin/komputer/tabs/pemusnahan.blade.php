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
            @foreach($data as $index => $computer)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $computer->model }}</td>
                <td>{{ $computer->tipe_merk }}</td>
                <td>
                    @if (json_decode($computer->serial))
                        CPU: {{ json_decode($computer->serial)->cpu }}<br>
                        Monitor: {{ json_decode($computer->serial)->monitor }}
                    @else
                        {{ $computer->serial }}
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($computer->tahun_perolehan)->format('M Y') }}</td>
                <td>
                    <div class="progress" style="height: 12px; width: 100px;">
                        <div 
                            class="progress-bar 
                                {{ 
                                    $computer->kelayakan >= 75 ? 'bg-success' :
                                    ($computer->kelayakan >= 50 ? 'bg-warning' : 'bg-danger')
                                }}"
                            role="progressbar" 
                            aria-valuenow="{{ $computer->kelayakan ?? 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100"
                            style="width: {{ $computer->kelayakan ?? 0 }}%">
                            {{ $computer->kelayakan ?? '-' }}%
                        </div>
                    </div>
                </td>
                <td title="{{ $computer->menuPemusnahan->keterangan ?? '-' }}">
                    {{ Str::limit($computer->menuPemusnahan->keterangan ?? '-', 50) }}
                </td>
                <td>{{ $computer->menuPemusnahan->created_at ? \Carbon\Carbon::parse($computer->menuPemusnahan->created_at)->format('d M Y - H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>