<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\IpAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class IpAddressController extends Controller
{
    private function forgetIpAddressCache() {
        Cache::forget('ip_addresses');
        Cache::forget('ip_ranges');
    }

    private function getBaseIp($ip) {
        $octets = explode('.', $ip);
        return $octets[0] . '.' . $octets[1] . '.' . $octets[2] . '.0';
    }

    private function generateIpRange($baseIp, $startRange, $endRange, $status = 'Available') {
        $octets = explode('.', $baseIp);
        $createdIps = [];

        // Validate range
        $startRange = max(2, min(255, intval($startRange)));
        $endRange = max(2, min(255, intval($endRange)));

        // Ensure startRange is less than endRange
        if ($startRange > $endRange) {
            list($startRange, $endRange) = array($endRange, $startRange);
        }

        DB::beginTransaction();
        try {
            for ($i = $startRange; $i <= $endRange; $i++) {
                $newIp = "{$octets[0]}.{$octets[1]}.{$octets[2]}.$i";
                IpAddress::create([
                    'ip_address' => $newIp,
                    'status' => $status,
                    'id_barang' => null
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

        // Group IP addresses by their base IP (first three octets)
        $ipRanges = IpAddress::all()->groupBy(function ($ip) {
            return $this->getBaseIp($ip->ip_address);
        })->map(function ($group) {
            return [
                'base_ip' => $this->getBaseIp($group->first()->ip_address),
                'count' => $group->count(),
                'available' => $group->where('status', 'Available')->count(),
                'in_use' => $group->where('status', 'In Use')->count(),
                'blocked' => $group->where('status', 'Blocked')->count(),
                'ips' => $group
            ];
        });

        return view('admin.master.ip-address', compact('title', 'breadcrumbs', 'ipRanges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'base_ip' => ['required', 'string', 'regex:/^(\d{1,3}\.){3}0$/'],
            'status' => ['required', Rule::in(['Available', 'In Use', 'Blocked'])],
            'start_range' => ['required', 'integer', 'min:2', 'max:255'],
            'end_range' => ['required', 'integer', 'min:2', 'max:255']
        ]);

        // Check if base IP already exists
        $existingIps = IpAddress::where('ip_address', 'LIKE', substr($request->base_ip, 0, -1) . '%')->exists();
        if ($existingIps) {
            return redirect()->route('ip-address.index')
                ->with('error', 'Range IP ini sudah ada dalam sistem');
        }

        try {
            $createdIps = $this->generateIpRange(
                $request->base_ip, 
                $request->start_range, 
                $request->end_range, 
                $request->status
            );
            $this->forgetIpAddressCache();
            return redirect()->route('ip-address.index')
                ->with('success', 'Range IP Address berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('ip-address.index')
                ->with('error', 'Gagal menambahkan range IP Address');
        }
    }

    // Add new method for single IP address creation
    public function storeSingle(Request $request)
    {
        $request->validate([
            'ip_address' => [
                'required',
                'string',
                'regex:/^(\d{1,3}\.){3}\d{1,3}$/',
                'unique:tbl_ip_address,ip_address'
            ],
            'status' => ['required', Rule::in(['Available', 'In Use', 'Blocked'])]
        ]);

        // Validate IP address format
        $octets = explode('.', $request->ip_address);
        if (count($octets) !== 4 || !array_reduce($octets, function($carry, $item) {
            return $carry && is_numeric($item) && $item >= 0 && $item <= 255;
        }, true)) {
            return redirect()->back()
                ->with('error', 'Format IP Address tidak valid');
        }

        try {
            IpAddress::create([
                'ip_address' => $request->ip_address,
                'status' => $request->status,
                'id_barang' => null
            ]);
            
            $this->forgetIpAddressCache();
            return redirect()->route('ip-address.index')
                ->with('success', 'IP Address berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('ip-address.index')
                ->with('error', 'Gagal menambahkan IP Address');
        }
    }

    public function updateRange(Request $request)
    {
        $request->validate([
            'base_ip' => ['required', 'string', 'regex:/^(\d{1,3}\.){3}0$/'],
            'status' => ['required', Rule::in(['Available', 'In Use', 'Blocked'])]
        ]);

        DB::beginTransaction();
        try {
            IpAddress::where('ip_address', 'LIKE', substr($request->base_ip, 0, -1) . '%')
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
            'base_ip' => ['required', 'string', 'regex:/^(\d{1,3}\.){3}0$/']
        ]);

        DB::beginTransaction();
        try {
            IpAddress::where('ip_address', 'LIKE', substr($request->base_ip, 0, -1) . '%')
                ->delete();
            
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

    // Single IP update for specific cases
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['Available', 'In Use', 'Blocked'])],
            'id_barang' => 'nullable|exists:tbl_barang,id_barang'
        ]);

        $ipAddress = IpAddress::findOrFail($id);
        $ipAddress->update($request->all());

        $this->forgetIpAddressCache();
        return redirect()->route('ip-address.index')
            ->with('success', 'Status IP Address berhasil diperbarui');
    }

    public function showDetail($baseIp)
    {
        $title = 'Detail IP Address';
        $breadcrumbs = [
            ['url' => '#', 'text' => 'Data Master'],
            ['url' => route('ip-address.index'), 'text' => 'Data IP Address'],
            ['url' => '#', 'text' => 'Detail IP Address']
        ];

        $baseIp = urldecode($baseIp);
        $ips = IpAddress::where('ip_address', 'LIKE', substr($baseIp, 0, -1) . '%')
            ->orderByRaw('SUBSTRING_INDEX(ip_address, ".", -1) + 0')
            ->get();

        return view('admin.master.show-ip', compact('title', 'breadcrumbs', 'ips', 'baseIp'));
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

            $baseIp = $this->getBaseIp($ip->ip_address);
            $this->forgetIpAddressCache();

            return redirect()->route('ip-address.detail', ['baseIp' => $baseIp])
                ->with('success', 'Status IP Address berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status IP');
        }
    }
}