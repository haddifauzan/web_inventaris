<?php
namespace App\Console\Commands;

use App\Models\Barang;
use App\Models\BarangKelayakanTracker;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateBarangKelayakan extends Command
{
    protected $signature = 'barang:update-kelayakan';
    protected $description = 'Update kelayakan barang based on active status and time passed';

    public function handle()
    {
        $barangs = Barang::where('status', 'Aktif')->get();
        
        foreach ($barangs as $barang) {
            $tracker = BarangKelayakanTracker::firstOrCreate(
                ['id_barang' => $barang->id_barang],
                ['last_update' => now(), 'accumulated_days' => 0]
            );
            
            $daysPassed = Carbon::parse($tracker->last_update)->diffInDays(now());
            $totalDays = $tracker->accumulated_days + $daysPassed;
            
            // If 30 or more days have passed
            if ($totalDays >= 30) {
                // Calculate how many 30-day periods have passed
                $periodsToDecrease = floor($totalDays / 30);
                
                // Store old kelayakan value
                $oldKelayakan = $barang->kelayakan;
                
                // Decrease kelayakan by 1% for each period
                $newKelayakan = max(0, $barang->kelayakan - $periodsToDecrease);
                $barang->update(['kelayakan' => $newKelayakan]);
                
                // Reset accumulated days, keeping remainder
                $remainingDays = $totalDays % 30;
                $tracker->update([
                    'last_update' => now(),
                    'accumulated_days' => $remainingDays
                ]);
                
                $this->info("Updated kelayakan for Barang ID {$barang->id_barang} from {$oldKelayakan} to {$barang->kelayakan}");
            } else {
                // Just update the accumulated days
                $tracker->update([
                    'last_update' => now(),
                    'accumulated_days' => $totalDays
                ]);
            }
        }
        
        $this->info('Kelayakan update process completed');
    }
}
