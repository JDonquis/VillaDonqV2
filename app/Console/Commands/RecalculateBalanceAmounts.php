<?php

namespace App\Console\Commands;

use App\Services\BalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecalculateBalanceAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:recalculate-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula los montos de los balances de los estudiantes según la configuración actual de precios (Inscripción y Mensualidad).';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService)
    {
        Log::info("Iniciando comando balance:recalculate-amounts");
        $this->info('Iniciando recalculación de montos de balances...');

        try {
            $updatedCount = $balanceService->recalculateAmountsFromConfig();
            $this->info("Recalculación completada. Se actualizaron {$updatedCount} balances.");
            Log::info("Comando balance:recalculate-amounts finalizado. Actualizados: {$updatedCount}");
        } catch (\Exception $e) {
            $this->error("Error durante la recalculación: " . $e->getMessage());
            Log::error("Error en comando balance:recalculate-amounts: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
