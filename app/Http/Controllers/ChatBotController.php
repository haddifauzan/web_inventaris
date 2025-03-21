<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\MenuAktif;
use App\Models\Riwayat;
use App\Models\Lokasi;
use App\Models\Departemen;
use App\Models\IpAddress;
use App\Models\Maintenance;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    protected $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation' => 'nullable|array'
        ]);

        $message = strtolower($request->input('message'));
        $conversation = $request->input('conversation', []);
        $conversation[] = ['role' => 'user', 'content' => $message];

        // Cek jika pertanyaan berkaitan dengan inventaris
        if (strpos($message, 'barang inventaris') !== false || strpos($message, 'pengelolaan barang') !== false) {
            $aiMessage = "Untuk pengelolaan barang inventaris, Anda dapat melihat data barang di menu Komputer, Switch, atau Tablet. Anda juga dapat menambah, mengedit, dan menghapus barang sesuai kebutuhan.";
        } 
        // Menampilkan data barang berdasarkan jenis (Komputer, Tablet, Switch)
        elseif (strpos($message, 'komputer') !== false || strpos($message, 'tablet') !== false || strpos($message, 'switch') !== false) {
            $jenis = null;
            if (strpos($message, 'komputer') !== false) $jenis = 'Komputer';
            elseif (strpos($message, 'tablet') !== false) $jenis = 'Tablet';
            elseif (strpos($message, 'switch') !== false) $jenis = 'Switch';
            
            $barang = Barang::where('jenis_barang', $jenis)->count();
            $aiMessage = "Terdapat $barang unit $jenis dalam sistem. Anda dapat melihat detailnya di menu Komputer, Switch, atau Tablet.";
        }
        // Menampilkan data barang berdasarkan status (Baru, Backup, Aktif, Pemusnahan)
        elseif (strpos($message, 'baru') !== false || strpos($message, 'backup') !== false || 
                strpos($message, 'aktif') !== false || strpos($message, 'pemusnahan') !== false) {
            $status = null;
            if (strpos($message, 'baru') !== false) $status = 'Baru';
            elseif (strpos($message, 'backup') !== false) $status = 'Backup';
            elseif (strpos($message, 'aktif') !== false) $status = 'Aktif';
            elseif (strpos($message, 'pemusnahan') !== false) $status = 'Pemusnahan';
            
            $barang = Barang::where('status', $status)->count();
            $aiMessage = "Terdapat $barang unit barang dengan status $status dalam sistem. Anda dapat melihat detailnya di menu Komputer, Switch, atau Tablet dengan filter status '$status'.";
        }
        // Menampilkan data barang berdasarkan kelayakan (khusus komputer)
        elseif (strpos($message, 'kelayakan') !== false) {
            if (strpos($message, 'rendah') !== false || strpos($message, 'buruk') !== false) {
                $komputer = Barang::where('jenis_barang', 'Komputer')
                                  ->where('kelayakan', '<', 50)
                                  ->count();
                $aiMessage = "Terdapat $komputer unit komputer dengan kelayakan di bawah 50%. Anda sebaiknya mempertimbangkan untuk memperbaiki, mengganti, atau memusnahkan pada unit-unit tersebut.";
            } elseif (strpos($message, 'baik') !== false || strpos($message, 'tinggi') !== false) {
                $komputer = Barang::where('jenis_barang', 'Komputer')
                                  ->where('kelayakan', '>=', 80)
                                  ->count();
                $aiMessage = "Terdapat $komputer unit komputer dengan kelayakan di atas 80%. Unit-unit ini dalam kondisi baik dan optimal.";
            } else {
                $rata = Barang::where('jenis_barang', 'Komputer')
                              ->avg('kelayakan');
                $aiMessage = "Rata-rata kelayakan komputer dalam sistem adalah ".round($rata, 2)."%. Anda dapat melihat detail kelayakan di menu barang Komputer.";
            }
        }
        // Menampilkan informasi tentang barang aktif
        elseif (strpos($message, 'barang aktif') !== false) {
            $aktif = MenuAktif::count();
            $aiMessage = "Terdapat $aktif unit barang yang sedang aktif digunakan. Anda dapat melihat detailnya di menu 'Barang pada Tab Aktif'.";
        }
        // Menampilkan informasi tentang barang backup
        elseif (strpos($message, 'barang backup') !== false || strpos($message, 'backup') !== false) {
            $backup = Barang::where('status', 'Backup')->count();
            $aiMessage = "Terdapat $backup unit barang yang berstatus backup. Anda dapat melihat detailnya di menu 'Barang pada Tab Backup'.";
        }
        // Menampilkan informasi tentang barang pemusnahan
        elseif (strpos($message, 'pemusnahan') !== false) {
            $pemusnahan = Barang::where('status', 'Pemusnahan')->count();
            $aiMessage = "Terdapat $pemusnahan unit barang yang telah direkomendasikan untuk pemusnahan. Anda dapat melihat detailnya di menu 'Barang pada Tab Pemusnahan'.";
        }
        // Menampilkan informasi tentang lokasi barang
        elseif (strpos($message, 'lokasi barang') !== false || strpos($message, 'lokasi') !== false) {
            $lokasi = Lokasi::withCount(['menuAktif as jumlah_barang'])->get();
            $aiMessage = "Berikut informasi lokasi barang:\n";
            foreach ($lokasi as $lok) {
                if ($lok->jumlah_barang > 0) {
                    $aiMessage .= "- {$lok->nama_lokasi}: {$lok->jumlah_barang} unit barang\n";
                }
            }
            $aiMessage .= "Anda dapat melihat detail lokasi barang di menu 'Data Master -> Lokasi'.";
        }
        // Menampilkan informasi tentang departemen
        elseif (strpos($message, 'departemen') !== false) {
            $departemen = Departemen::withCount(['menuAktif as jumlah_barang'])->get();
            $aiMessage = "Berikut informasi departemen dan jumlah barang:\n";
            foreach ($departemen as $dept) {
                if ($dept->jumlah_barang > 0) {
                    $aiMessage .= "- {$dept->nama_departemen}: {$dept->jumlah_barang} unit barang\n";
                }
            }
            $aiMessage .= "Anda dapat melihat detail departemen di menu 'Data Master -> Departemen'.";
        }
        // Menampilkan informasi tentang IP Address
        elseif (strpos($message, 'ip') !== false || strpos($message, 'ip address') !== false) {
            $available = IpAddress::where('status', 'Available')->count();
            $inUse = IpAddress::where('status', 'In Use')->count();
            $blocked = IpAddress::where('status', 'Blocked')->count();
            $total = $available + $inUse + $blocked;
            
            $aiMessage = "Informasi IP Address:\n";
            $aiMessage .= "- Total: $total alamat IP\n";
            $aiMessage .= "- Tersedia: $available alamat IP\n";
            $aiMessage .= "- Digunakan: $inUse alamat IP\n";
            $aiMessage .= "- Diblokir: $blocked alamat IP\n";
            $aiMessage .= "Anda dapat mengelola IP Address di menu 'Data Master -> IP Address'.";
        }
        // Menampilkan informasi tentang maintenance switch
        elseif (strpos($message, 'maintenance') !== false || strpos($message, 'perawatan') !== false) {
            if (strpos($message, 'switch') !== false) {
                $maintenance = Maintenance::count();
                $switchRusak = Maintenance::where('status_net', 'Rusak')->count();
                
                $aiMessage = "Informasi maintenance switch:\n";
                $aiMessage .= "- Total maintenance: $maintenance kali\n";
                $aiMessage .= "- Switch dengan status jaringan rusak: $switchRusak unit\n";
                $aiMessage .= "Anda dapat melihat detail maintenance di menu Switch pada Tab Barang Aktif.";
            } else {
                $aiMessage = "Sistem mencatat maintenance untuk perangkat switch. Anda dapat melihat dan mengelola data maintenance di menu 'Switch pada Tab Aktif'.";
            }
        }
        // Menampilkan informasi tentang riwayat
        elseif (strpos($message, 'riwayat') !== false || strpos($message, 'history') !== false) {
            $riwayat = Riwayat::count();
            $aktif = Riwayat::where('status', 'Aktif')->count();
            $nonAktif = Riwayat::where('status', 'Non-Aktif')->count();
            
            $aiMessage = "Informasi riwayat barang:\n";
            $aiMessage .= "- Total catatan riwayat: $riwayat entri\n";
            $aiMessage .= "- Riwayat dengan status aktif: $aktif entri\n";
            $aiMessage .= "- Riwayat dengan status non-aktif: $nonAktif entri\n";
            $aiMessage .= "Anda dapat melihat detail riwayat di menu barang Komputer, Switch, atau Tablet pada tab 'Riwayat Penggunaan'.";
        }
        // Menampilkan informasi tentang OS (untuk komputer)
        elseif (strpos($message, 'os') !== false || strpos($message, 'operating system') !== false || strpos($message, 'sistem operasi') !== false) {
            $osData = Barang::where('jenis_barang', 'Komputer')
                            ->select('operating_system', DB::raw('count(*) as total'))
                            ->groupBy('operating_system')
                            ->get();
            
            $aiMessage = "Informasi sistem operasi komputer:\n";
            foreach ($osData as $os) {
                $aiMessage .= "- {$os->operating_system}: {$os->total} unit\n";
            }
            $aiMessage .= "Anda dapat melihat detail sistem operasi di menu Komputer.";
        }
        // Menampilkan informasi tentang kepemilikan
        elseif (strpos($message, 'kepemilikan') !== false) {
            $inventaris = Barang::where('kepemilikan', 'Inventaris')->count();
            $nop = Barang::where('kepemilikan', 'NOP')->count();
            
            $aiMessage = "Informasi kepemilikan barang komputer:\n";
            $aiMessage .= "- Inventaris: $inventaris unit\n";
            $aiMessage .= "- NOP: $nop unit\n";
            $aiMessage .= "Anda dapat melihat detail kepemilikan di menu komputer.";
        }
        // Menampilkan informasi tentang tahun perolehan
        elseif (strpos($message, 'tahun perolehan') !== false || strpos($message, 'tahun') !== false) {
            $tahunData = Barang::select(DB::raw('YEAR(tahun_perolehan) as tahun'), DB::raw('count(*) as total'))
                               ->groupBy('tahun')
                               ->orderBy('tahun', 'desc')
                               ->get();
            
            $aiMessage = "Informasi barang berdasarkan tahun perolehan:\n";
            foreach ($tahunData as $data) {
                $aiMessage .= "- Tahun {$data->tahun}: {$data->total} unit\n";
            }
            $aiMessage .= "Anda dapat melihat detail tahun perolehan sesuai dengan barang yang diinginkan pada menu.";
        }
        // Menampilkan informasi tentang total barang
        elseif (strpos($message, 'total barang') !== false || strpos($message, 'jumlah barang') !== false) {
            $total = Barang::count();
            $komputer = Barang::where('jenis_barang', 'Komputer')->count();
            $tablet = Barang::where('jenis_barang', 'Tablet')->count();
            $switch = Barang::where('jenis_barang', 'Switch')->count();
            
            $aiMessage = "Informasi total barang dalam sistem:\n";
            $aiMessage .= "- Total semua barang: $total unit\n";
            $aiMessage .= "- Komputer: $komputer unit\n";
            $aiMessage .= "- Tablet: $tablet unit\n";
            $aiMessage .= "- Switch: $switch unit\n";
            $aiMessage .= "Anda dapat melihat detail barang di menu Komputer, Switch, atau Tablet.";
        }
        // Gunakan layanan Groq untuk pertanyaan yang tidak terkait dengan inventaris
        else {
            $response = $this->groqService->generateResponse($conversation);
            
            if (isset($response['error'])) {
                return response()->json([
                    'success' => false,
                    'error' => $response['message']
                ], 500);
            }

            $aiMessage = $response['choices'][0]['message']['content'] ?? 'Maaf, saya tidak dapat memproses pesan Anda saat ini.';
        }

        $conversation[] = ['role' => 'assistant', 'content' => $aiMessage];

        return response()->json([
            'success' => true,
            'message' => $aiMessage,
            'conversation' => $conversation
        ]);
    }
}