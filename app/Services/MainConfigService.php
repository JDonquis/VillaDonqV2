<?php  

namespace App\Services;

use DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Activity;
use App\Models\MainConfig;
use App\Models\CourseSection;
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

    public function updatePaymentConfig($request)
    {
        $this->mainConfigModel->update(
        [ 
        'regular_inscription_price' => $request->regular_inscription_price,
        'new_inscription_price' => $request->new_inscription_price,
        'monthly_payment' => $request->monthly_payment
        ]);
        
    }

    public function getAccounts()
    {
        $accounts = AccountPayment::with('method')->get();

        return new AccountPaymentCollection($accounts);
    }

}
