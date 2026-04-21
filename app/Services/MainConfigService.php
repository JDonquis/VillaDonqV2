<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Events\StudentCreated;
use App\Events\StudentUpdated;
use App\Http\Resources\AccountPaymentCollection;
use App\Http\Resources\StudentCollection;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Models\AccountPayment;
use App\Models\Activity;
use App\Models\CourseSection;
use App\Models\MainConfig;
use App\Models\PaymentMethod;
use App\Models\Representative;
use App\Models\Student;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MainConfigService
{
    private MainConfig $mainConfigModel;


    public function __construct()
    {
        $this->mainConfigModel = MainConfig::first();
    }

    public function getAccounts()
    {
        $accounts = AccountPayment::where('status', 1)->with('method')->get();

        return new AccountPaymentCollection($accounts);
    }

    public function getAccountsWhereId($id)
    {
        $accounts = AccountPayment::where('status', 1)->where('payment_method_id', $id)->with('method')->get();

        return new AccountPaymentCollection($accounts);
    }

    public function getMethods()
    {
        $methods = PaymentMethod::whereNot('id', 1)->get();

        return $methods;
    }

    public function getConfigData()
    {
        return $this->mainConfigModel->first();
    }

    public function updatePaymentConfig($data)
    {
        $this->mainConfigModel->update($data);
    }

    public function createAccount($request)
    {
        return AccountPayment::create($request->all());
    }

    public function updateAccount($id, $request)
    {
        $account = AccountPayment::find($id);

        $account->update($request->all());

        $account->touch();

        return 0;
    }

    public function deleteAccount($id)
    {
        AccountPayment::where('id', $id)->update(['status' => 2]);
        return 0;
    }

    public function getFieldsFromMethod($methodID)
    {
        $method = PaymentMethodEnum::from($methodID);

        return match ($method) {
            PaymentMethodEnum::PagoMovil => ['ci', 'phone_number', 'bank'],
            PaymentMethodEnum::Transferencia => ['account_number', 'person_name', 'ci', 'phone_number', 'bank'],
            PaymentMethodEnum::Zelle => ['username', 'email'],
            PaymentMethodEnum::Binance => ['email'],
            PaymentMethodEnum::PuntoDeVenta => ['bank', 'comision'],
            default => null,
            //a
        };
    }
}