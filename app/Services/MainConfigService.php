<?php  

namespace App\Services;

use DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Activity;
use App\Models\MainConfig;
use App\Models\CourseSection;
use App\Models\PaymentMethod;
use App\Events\StudentCreated;
use App\Events\StudentUpdated;
use App\Models\AccountPayment;
use App\Models\Representative;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudentCollection;
use App\Http\Resources\AccountPaymentCollection;

class MainConfigService
{	
	private MainConfig $mainConfigModel;


    public function __construct()
    {
        $this->mainConfigModel = MainConfig::first();
    }

    public function getAccounts()
    {
        $accounts = AccountPayment::where('status',1)->with('method')->get();

        return new AccountPaymentCollection($accounts);
    }

    public function getAccountsWhereId($id)
    {
        $accounts = AccountPayment::where('status',1)->where('payment_method_id',$id)->with('method')->get();

        return new AccountPaymentCollection($accounts);
    }

    public function getMethods()
    {
        $methods = PaymentMethod::whereNot('id',1)->get();

        return $methods;
    }

    public function getConfigData()
    {
        return $this->mainConfigModel->first();
    }

    public function updatePaymentConfig($request)
    {
        $this->mainConfigModel->update(
        [ 
        'regular_inscription_price' => $request->regular_inscription_price,
        'new_inscription_price' => $request->new_inscription_price,
        'monthly_payment' => $request->monthly_payment
        ]);
        
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
        AccountPayment::where('id',$id)->update(['status' => 2]);
        return 0;   
    }

    public function getFieldsFromMethod($methodID)
    {
        switch ($methodID) 
        {
            case 2:
                return ['ci','phone_number','bank'];
            break;
            
            case 3:
                return ['account_number','person_name','ci','phone_number','bank'];
            break;

            case 4:
                return ['username','email'];
            break;

            case 5:
                return ['email'];
            break;
            
            default:
                return null;
                break;
        }
    }

    

}
