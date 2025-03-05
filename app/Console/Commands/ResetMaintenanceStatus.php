<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Maintenance;
use App\Models\MenuAktif;
use Illuminate\Support\Facades\Log;

class ResetMaintenanceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset maintenance status to "Belum" for all switches at the beginning of each month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Reset status_maintenance to "Belum" for all switch maintenance records
            $updatedCount = Maintenance::where('status_maintenance', 'Sudah')
                ->update([
                    'status_maintenance' => 'Belum',
                    'petugas' => null,
                    'keterangan' => null
                ]);

            // Log the reset operation
            Log::info('Maintenance status reset completed.', [
                'updated_records' => $updatedCount,
                'reset_date' => now()
            ]);

            // Output to console
            $this->info("Maintenance status reset successfully. {$updatedCount} records updated.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Maintenance status reset failed.', [
                'error' => $e->getMessage(),
                'reset_date' => now()
            ]);

            // Output error to console
            $this->error("Failed to reset maintenance status: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}