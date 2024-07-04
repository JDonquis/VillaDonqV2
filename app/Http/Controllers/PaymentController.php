<?php

namespace App\Http\Controllers;

use inertia;
use Illuminate\Http\Request;
use App\Services\MainConfigService;

class PaymentController extends Controller
{
    
    public function __construct()
    {
        $this->mainConfigService = new MainConfigService;
    }

    public function index()
    {
        return inertia('Dashboard/Pagos');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function showCreatePayment(Request $request)
    {   
        $methodPayment = 2;        
        if(isset($request->method_payment))
            $methodPayment = $request->method_payment;
        $methods = $this->mainConfigService->getMethods();
        $accounts  = $this->mainConfigService->getAccountsWhereId($methodPayment);
        

        return inertia('Dashboard/RegistrarPago',['data' => ['methods' => $methods, 'accounts' => $accounts] ]);
    }

  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
