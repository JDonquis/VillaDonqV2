<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Services\MainConfigService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use inertia;

class PaymentController extends Controller
{
    private MainConfigService $mainConfigService;
    private PaymentService $paymentService;
    public function __construct()
    {

        $this->mainConfigService = new MainConfigService;
        $this->paymentService = new PaymentService;
    }

    public function index(Request $request)
    {
        $prices = $this->mainConfigService->getPrices();
        $accounts  = $this->mainConfigService->getAccounts();
        $payments = $this->paymentService->getAll($request->all());
        return inertia('Dashboard/Pagos', ['data' =>
        [
            'accounts' => $accounts,
            'payments' => $payments,
            'prices' => $prices,
        ]]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        DB::beginTransaction();

        try {

            $this->paymentService->create($request->validated());

            DB::commit();


            return redirect('/dashboard/pagos');
        } catch (Exception $e) {

            DB::rollback();

            Log::error('Error al crear pago: ' . $e->getMessage());

            return redirect('/dashboard/pagos')->withErrors(['message' => 'Ha ocurrido un error al crear el pago. Por favor, intente más tarde.']);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $this->paymentService->delete($id);

            return redirect('/dashboard/pagos');
        } catch (Exception $e) {
            Log::error('Error al eliminar pago ID ' . $id . ': ' . $e->getMessage());

            return redirect('/dashboard/pagos')->withErrors(['message' => 'Ha ocurrido un error al eliminar el pago. Por favor, intente más tarde.']);
        }
    }
}
