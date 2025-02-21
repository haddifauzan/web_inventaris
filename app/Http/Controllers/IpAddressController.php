<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\IpAddress;
use App\Models\IpHost;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class IpAddressController extends Controller
{
    private function forgetIpAddressCache() {
        Cache::forget('ip_addresses');
        Cache::forget('ip_ranges');
        Cache::forget('ip_hosts');
    }

    private function validateIpFormat($ip) {
        $octets = explode('.', $ip);
        return count($octets) === 4 && array_reduce($octets, function($carry, $item) {
            return $carry && is_numeric($item) && $item >= 0 && $item <= 255;
        }, true);
    }

    private function getBaseIp($ip) {
        $octets = explode('.', $ip);
        return $octets[0] . '.' . $octets[1] . '.' . $octets[2] . '.0';
    }

    private function generateIpRange($ipHost, $startRange, $endRange, $status = 'Available') {
        $octets = explode('.', $ipHost->ip_host);
        $baseNetwork = $octets[0] . '.' . $octets[1] . '.' . $octets[2];
        $createdIps = [];

        // Validate range
        $startRange = max(1, min(255, intval($startRange)));
        $endRange = max(1, min(255, intval($endRange)));

        // Ensure startRange is less than endRange
        if ($startRange > $endRange) {
            list($startRange, $endRange) = array($endRange, $startRange);
        }

        DB::beginTransaction();
        try {
            for ($i = $startRange; $i <= $endRange; $i++) {
                $newIp = "{$baseNetwork}.$i";
                IpAddress::create([
                    'ip_address' => $newIp,
                    'status' => $status,
                    'id_barang' => null,
                    'id_ip_host' => $ipHost->id_ip_host
                ]);
                $createdIps[] = $newIp;
            }
            DB::commit();
            return $createdIps;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function index()
    {
        $title = 'Data IP Address';
        $breadcrumbs = [
            ['url' => '#', 'text' => 'Data Master'],
            ['url' => route('ip-address.index'), 'text' => 'Data IP Address']
        ];

        // Get locations with their IP hosts and IP addresses
        $locations = Lokasi::with(['ipHosts.ipAddresses'])->orderBy('nama_lokasi', 'asc')->get();
        
        // Transform data for view
        $ipRanges = collect();
        foreach ($locations as $location) {
            foreach ($location->ipHosts as $ipHost) {
                $addresses = $ipHost->ipAddresses;
                $ipRanges->put($ipHost->ip_host, [
                    'location' => $location->nama_lokasi,
                    'base_ip' => $this->getBaseIp($ipHost->ip_host),
                    'count' => $addresses->count(),
                    'available' => $addresses->where('status', 'Available')->count(),
                    'in_use' => $addresses->where('status', 'In Use')->count(),
                    'blocked' => $addresses->where('status', 'Blocked')->count(),
                    'id_ip_host' => $ipHost->id_ip_host
                ]);
            }
        }

        $locations = Lokasi::all();
        return view('admin.master.ip-address', compact('title', 'breadcrumbs', 'ipRanges', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_host' => ['required', 'string', 'regex:/^(\d{1,3}\.){3}\d{1,3}$/', 'unique:tbl_ip_host,ip_host'],
            'id_lokasi' => ['required', 'exists:tbl_lokasi,id_lokasi'],
            'status' => ['required', Rule::in(['Available', 'In Use', 'Blocked'])],
            'start_range' => ['required', 'integer', 'min:1', 'max:255'],
            'end_range' => ['required', 'integer', 'min:1', 'max:255']
        ]);

        if (!$this->validateIpFormat($request->ip_host)) {
            return redirect()->back()->with('error', 'Format IP Host tidak valid');
        }

        DB::beginTransaction();
        // Create IP Host
        $ipHost = IpHost::create([
            'ip_host' => $request->ip_host,
            'id_lokasi' => $request->id_lokasi
        ]);

        // Generate IP range for the host
        $this->generateIpRange(
            $ipHost,
            $request->start_range,
            $request->end_range,
            $request->status
        );

        DB::commit();
        $this->forgetIpAddressCache();
        return redirect()->route('ip-address.index')
            ->with('success', 'IP Host dan Range IP Address berhasil ditambahkan');
    }

    public function updateRange(Request $request)
    {
        $request->validate([
            'id_ip_host' => ['required', 'exists:tbl_ip_host,id_ip_host'],
            'status' => ['required', Rule::in(['Available', 'In Use', 'Blocked'])]
        ]);

        DB::beginTransaction();
        try {
            IpAddress::where('id_ip_host', $request->id_ip_host)
                ->where('status', '!=', 'In Use')
                ->update(['status' => $request->status]);
            
            DB::commit();
            $this->forgetIpAddressCache();
            return redirect()->route('ip-address.index')
                ->with('success', 'Status range IP berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ip-address.index')
                ->with('error', 'Gagal memperbarui status range IP');
        }
    }

    public function destroyRange(Request $request)
    {
        $request->validate([
            'id_ip_host' => ['required', 'exists:tbl_ip_host,id_ip_host']
        ]);

        DB::beginTransaction();
        try {
            // Delete IP addresses
            IpAddress::where('id_ip_host', $request->id_ip_host)->delete();
            
            // Delete IP host
            IpHost::where('id_ip_host', $request->id_ip_host)->delete();
            
            DB::commit();
            $this->forgetIpAddressCache();
            return redirect()->route('ip-address.index')
                ->with('success', 'Range IP berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ip-address.index')
                ->with('error', 'Gagal menghapus range IP');
        }
    }

    public function showDetail($idIpHost)
    {
        $title = 'Detail IP Address';
        $breadcrumbs = [
            ['url' => '#', 'text' => 'Data Master'],
            ['url' => route('ip-address.index'), 'text' => 'Data IP Address'],
            ['url' => '#', 'text' => 'Detail IP Address']
        ];

        $ipHost = IpHost::with([
            'lokasi', 
            'ipAddresses' => function($query) {
                $query->orderByRaw('CAST(SUBSTRING_INDEX(ip_address, ".", -1) AS UNSIGNED)');
            },
            'ipAddresses.menuAktif'
        ])->findOrFail($idIpHost);

        return view('admin.master.show-ip', compact('title', 'breadcrumbs', 'ipHost'));
    }

    public function updateIpStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['Available', 'In Use', 'Blocked'])],
        ]);

        try {
            $ip = IpAddress::findOrFail($id);
            $ip->status = $request->status;
            $ip->save();

            $this->forgetIpAddressCache();

            return redirect()->route('ip-address.detail', ['idIpHost' => $ip->id_ip_host])
                ->with('success', 'Status IP Address berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status IP');
        }
    }

    public function updateHost(Request $request, $id)
    {
        $request->validate([
            'ip_host' => [
                'required', 
                'string', 
                'regex:/^(\d{1,3}\.){3}\d{1,3}$/',
                Rule::unique('tbl_ip_host', 'ip_host')->ignore($id, 'id_ip_host')
            ],
            'id_lokasi' => ['required', 'exists:tbl_lokasi,id_lokasi']
        ]);

        if (!$this->validateIpFormat($request->ip_host)) {
            return redirect()->back()->with('error', 'Format IP Host baru tidak valid');
        }

        DB::beginTransaction();
        try {
            // Ambil IP host dan IP addresses terkait
            $ipHost = IpHost::with('ipAddresses')->findOrFail($id);
            $oldIpBase = substr($ipHost->ip_host, 0, strrpos($ipHost->ip_host, '.'));
            $newIpBase = substr($request->ip_host, 0, strrpos($request->ip_host, '.'));

            // Update IP host
            $ipHost->ip_host = $request->ip_host;
            $ipHost->id_lokasi = $request->id_lokasi;
            $ipHost->save();

            // Update semua IP address terkait
            foreach ($ipHost->ipAddresses as $ipAddress) {
                $lastOctet = substr($ipAddress->ip_address, strrpos($ipAddress->ip_address, '.'));
                $newIpAddress = $newIpBase . $lastOctet;
                
                // Periksa apakah IP baru sudah ada di sistem
                $existingIp = IpAddress::where('ip_address', $newIpAddress)
                    ->where('id_ip', '!=', $ipAddress->id_ip)
                    ->exists();
                    
                if ($existingIp) {
                    throw new \Exception("IP Address $newIpAddress sudah ada dalam sistem");
                }
                
                $ipAddress->ip_address = $newIpAddress;
                $ipAddress->save();
            }

            DB::commit();
            $this->forgetIpAddressCache();
            
            return redirect()->route('ip-address.index')
                ->with('success', 'IP Host dan semua IP Address terkait berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ip-address.index')
                ->with('error', 'Gagal memperbarui IP Host: ' . $e->getMessage());
        }
    }

    public function getAvailableIpAddresses($lokasiId)
    {
        $ipHosts = IpHost::where('id_lokasi', $lokasiId)
            ->with(['ipAddresses' => function ($query) {
                $query->where('status', 'Available')
                      ->orderByRaw('CAST(SUBSTRING_INDEX(ip_address, ".", -1) AS UNSIGNED)');
            }])
            ->get();

        return response()->json([
            'ipHosts' => $ipHosts->map(function ($ipHost) {
                return [
                    'ip_host' => $ipHost->ip_host,
                    'ip_addresses' => $ipHost->ipAddresses
                ];
            })
        ]);
    }
}