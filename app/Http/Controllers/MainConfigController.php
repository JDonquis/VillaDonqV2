<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountPayment;
use App\Services\MainConfigService;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\PaymentConfigRequest;
use App\Http\Resources\AccountPaymentResource;

class MainConfigController extends Controller
{
    public function __construct()
    {
        $this->mainConfigService = new MainConfigService;
    }

    public function index()
    {

        $methods = $this->mainConfigService->getMethods();
        $accounts  = $this->mainConfigService->getAccounts();
        return inertia('Dashboard/Configuracion',
        [
            'data' =>
            [
                'accounts' => $accounts,
                'methods' => $methods,
            ]
        
        ]
        
        );
        
    }

    public function showCreateAccount($methodID)
    {
        $fields = $this->mainConfigService->getFieldsFromMethod($methodID);
        return inertia('Dashboard/MetodosDePago/Crear', ['data' => ['fields' => $fields]]);
    }

    public function showEditAccount($id)
    {
        $account = AccountPayment::where('id',$id)->with('method')->first();
        $accountResource = new AccountPaymentResource($account);

        return inertia('Dashboard/MetodosDePago/Editar', ['data' => ['account' => $accountResource]]);
    }


    public function createAccount(AccountRequest $request)
    {
       
        $this->mainConfigService->createAccount($request);
        return redirect('/dashboard/configuracion');
    }

    public function editAccount(AccountRequest $request, $id)
    {
        $this->mainConfigService->UpdateAccount($id, $request);
        return redirect('/dashboard/configuracion');

    }

    public function updatePaymentConfig(PaymentConfigRequest $request)
    {   
        $this->mainConfigService->updatePaymentConfig($request);

        return redirect('/dashboard/configuracion');

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
