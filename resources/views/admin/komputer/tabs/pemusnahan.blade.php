<div class="table-responsive">
    <table class="table table-sm small table-striped" id="destroyedTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Tipe/Merk</th>
                <th>Serial</th>
                <th>Tahun Perolehan</th>
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
                <td title="{{ $computer->menuPemusnahan->keterangan ?? '-' }}">
                    {{ Str::limit($computer->menuPemusnahan->keterangan ?? '-', 50) }}
                </td>
                <td>{{ $computer->menuPemusnahan->created_at ? \Carbon\Carbon::parse($computer->menuPemusnahan->created_at)->format('d M Y - H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>