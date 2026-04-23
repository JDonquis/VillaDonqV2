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

    public function index()
    {
        $accounts  = $this->mainConfigService->getAccounts();
        return inertia('Dashboard/Pagos', ['data' => ['accounts' => $accounts]]);
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
