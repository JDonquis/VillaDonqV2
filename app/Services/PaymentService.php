<?php

namespace App\Services;

use App\Models\AccountPayment;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    public function getAll($params = [])
    {
        $query = Payment::query()
            ->with('students', 'accountPayment.method', 'user', 'deletedBy')
            ->when(isset($params['search']), function ($q) use ($params) {
                $search = $params['search'];
                $q->where(function ($query) use ($search) {
                    $query->where('reference', 'like', '%' . $search . '%')
                        ->orWhere('observations', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                                ->orWhere('name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('accountPayment.method', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('students', function ($q) use ($search) {
                            $q->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                                ->orWhere('name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
                });
            });

        return $query->paginate($params['per_page'] ?? 25)->withQueryString();
    }

    public function create(array $data): Payment
    {
        $accountPayment = AccountPayment::findOrFail($data['account_payment_id']);

        $userId = Auth::id() ?? 1;

        $payment = Payment::create([
            'user_id' => $userId,
            'account_payment_id' => $data['account_payment_id'],
            'date' => $data['date'],
            'total_in_dolars' => $data['total_in_dolars'],
            'total_in_bs' => $data['total_in_bs'],
            'reference' => $data['reference'] ?? null,
            'status' => 1,
            'observations' => $data['observations'] ?? null,
        ]);

        $studentsData = collect($data['students']);

        $balanceService = new BalanceService;

        foreach ($studentsData as $studentData) {
            $student = Student::where('id', $studentData['id'])
                ->where('status', '!=', 0)
                ->firstOrFail();

            $payment->students()->attach($studentData['id'], [
                'amount_in_dolars' => $studentData['amount_in_dolars'],
            ]);

            $balanceService->updateStudentBalance($payment, $student);
        }

        $payment->load('students', 'accountPayment');

        return $payment;
    }

    public function delete($id)
    {
        $payment = Payment::findOrFail($id);

        $balanceService = new BalanceService;

        foreach ($payment->students as $student) {
            $balanceService->revertStudentBalance($payment, $student);
        }

        $payment->deleted_by = Auth::id();
        $payment->save();
        $payment->delete();
    }
}
