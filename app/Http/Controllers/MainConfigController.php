<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MainConfigService;
use App\Http\Requests\PaymentConfigRequest;

class MainConfigController extends Controller
{
    public function __construct()
    {
        $this->mainConfigService = new MainConfigService;
    }

    public function index()
    {
        return inertia('Dashboard/Configuracion');
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function updatePaymentConfig(PaymentConfigRequest $request)
    {   
        $this->mainConfigService->updatePaymentConfig($request);

        return redirect('/dashboard/matricula?course_id='.$request->course_id.'&section_id='.$request->section_id);

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
