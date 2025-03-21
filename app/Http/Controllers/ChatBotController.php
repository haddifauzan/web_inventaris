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

        // Cek jika pesan dimulai dengan '/cek'
        if (strpos($message, '/cek') === 0) {
            $messageWithoutPrefix = trim(substr($message, 4)); // Menghapus '/cek' dari pesan
            $aiMessage = null; // Initialize aiMessage

            // Cek jika pertanyaan berkaitan dengan inventaris
            if (preg_match('/\b(barang inventaris|pengelolaan barang)\b/', $messageWithoutPrefix)) {
                $aiMessage = "Untuk pengelolaan barang inventaris, Anda dapat melihat data barang di menu Komputer, Switch, atau Tablet. Anda juga dapat menambah, mengedit, dan menghapus barang sesuai kebutuhan.";
            } 
            // Menampilkan data barang berdasarkan jenis (Komputer, Tablet, Switch)
            elseif (preg_match('/\b(komputer|tablet|switch)\b/', $messageWithoutPrefix)) {
                $jenis = null;
                if (strpos($messageWithoutPrefix, 'komputer') !== false) $jenis = 'Komputer';
                elseif (strpos($messageWithoutPrefix, 'tablet') !== false) $jenis = 'Tablet';
                elseif (strpos($messageWithoutPrefix, 'switch') !== false) $jenis = 'Switch';
                
                $barang = Barang::where('jenis_barang', $jenis)->count();
                $aiMessage = "Terdapat $barang unit $jenis dalam sistem. Anda dapat melihat detailnya di menu Komputer, Switch, atau Tablet.";
            }
            // Menampilkan status semua barang
            elseif (preg_match('/\b(status|status barang|semua status)\b/', $messageWithoutPrefix)) {
                $statuses = Barang::select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->get();
                
                $aiMessage = "Informasi status semua barang:\n";
                foreach ($statuses as $status) {
                    $aiMessage .= "- {$status->status}: {$status->total} unit\n";
                }
                $aiMessage .= "Anda dapat melihat detail status barang di menu Komputer, Switch, atau Tablet.";
            }
            // Menampilkan data barang berdasarkan status (Baru, Backup, Aktif, Pemusnahan)
            elseif (preg_match('/\b(baru|backup|aktif|pemusnahan)\b/', $messageWithoutPrefix)) {
                $status = null;
                if (strpos($messageWithoutPrefix, 'baru') !== false) $status = 'Baru';
                elseif (strpos($messageWithoutPrefix, 'backup') !== false) $status = 'Backup';
                elseif (strpos($messageWithoutPrefix, 'aktif') !== false) $status = 'Aktif';
                elseif (strpos($messageWithoutPrefix, 'pemusnahan') !== false) $status = 'Pemusnahan';
                
                if ($status) {
                    $barang = Barang::where('status', $status)->count();
                    $aiMessage = "Terdapat $barang unit barang dengan status $status dalam sistem. Anda dapat melihat detailnya di menu Komputer, Switch, atau Tablet dengan filter status '$status'.";
                }
            }
            // Menampilkan data barang berdasarkan kelayakan (khusus komputer)
            elseif (preg_match('/\b(kelayakan)\b/', $messageWithoutPrefix)) {
                if (preg_match('/\b(rendah|buruk)\b/', $messageWithoutPrefix)) {
                    $komputer = Barang::where('jenis_barang', 'Komputer')
                                    ->where('kelayakan', '<', 50)
                                    ->count();
                    $aiMessage = "Terdapat $komputer unit komputer dengan kelayakan di bawah 50%. Anda sebaiknya mempertimbangkan untuk memperbaiki, mengganti, atau memusnahkan pada unit-unit tersebut.";
                } elseif (preg_match('/\b(baik|tinggi)\b/', $messageWithoutPrefix)) {
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
            elseif (preg_match('/\b(barang aktif)\b/', $messageWithoutPrefix)) {
                $aktif= MenuAktif::count();
                $aiMessage = "Terdapat $aktif unit barang yang sedang aktif digunakan. Anda dapat melihat detailnya di menu 'Barang pada Tab Aktif'.";
            }
            // Menampilkan informasi tentang barang backup
            elseif (preg_match('/\b(barang backup|backup)\b/', $messageWithoutPrefix)) {
                $backup = Barang::where('status', 'Backup')->count();
                $aiMessage = "Terdapat $backup unit barang yang berstatus backup. Anda dapat melihat detailnya di menu 'Barang pada Tab Backup'.";
            }
            // Menampilkan informasi tentang barang pemusnahan
            elseif (preg_match('/\b(pemusnahan)\b/', $messageWithoutPrefix)) {
                $pemusnahan = Barang::where('status', 'Pemusnahan')->count();
                $aiMessage = "Terdapat $pemusnahan unit barang yang telah direkomendasikan untuk pemusnahan. Anda dapat melihat detailnya di menu 'Barang pada Tab Pemusnahan'.";
            }
            // Menampilkan informasi tentang lokasi barang
            elseif (preg_match('/\b(lokasi barang|lokasi)\b/', $messageWithoutPrefix)) {
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
            elseif (preg_match('/\b(departemen)\b/', $messageWithoutPrefix)) {
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
            elseif (preg_match('/\b(ip|ip address)\b/', $messageWithoutPrefix)) {
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
            elseif (preg_match('/\b(maintenance|perawatan)\b/', $messageWithoutPrefix)) {
                if (strpos($messageWithoutPrefix, 'switch') !== false) {
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
            elseif (preg_match('/\b(riwayat|history)\b/', $messageWithoutPrefix)) {
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
            elseif (preg_match('/\b(os|operating system|sistem operasi)\b/', $messageWithoutPrefix)) {
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
            elseif (preg_match('/\b(kepemilikan)\b/', $messageWithoutPrefix)) {
                $inventaris = Barang::where('kepemilikan', 'Inventaris')->count();
                $nop = Barang::where('kepemilikan', 'NOP')->count();
                
                $aiMessage = "Informasi kepemilikan barang komputer:\n";
                $aiMessage .= "- Inventaris: $inventaris unit\n";
                $aiMessage .= "- NOP: $nop unit\n";
                $aiMessage .= "Anda dapat melihat detail kepemilikan di menu komputer.";
            }
            // Menampilkan informasi tentang tahun perolehan
            elseif (preg_match('/\b(tahun perolehan|tahun)\b/', $messageWithoutPrefix)) {
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
            elseif (preg_match('/\b(total barang|jumlah barang)\b/', $messageWithoutPrefix)) {
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
            } else {
                $aiMessage .=  'Perintah tidak dikenali. Silakan gunakan perintah yang valid setelah /cek.';
            }
        } else {
            // Gunakan layanan Groq untuk pertanyaan yang tidak terkait dengan inventaris
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