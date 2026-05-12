<?php

namespace App\Http\Controllers;

use App\Services\AccountStatementService;
use Illuminate\Http\Request;

class AccountStatementController extends Controller
{
    public function index(Request $request)
    {
        $service = new AccountStatementService;
        $result = $service->getAll($request->all());

        return inertia('Dashboard/EstadosDeCuenta', [
            'data' => $result,
        ]);
    }
}
