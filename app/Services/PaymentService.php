<?php

namespace App\Services;

use App\Models\AccountPayment;
use App\Models\Payment;
use App\Models\Student;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function getAll()
    {
        return Payment::query()->with('students', 'accountPayment.method', 'user')->paginate(25)->withQueryString();
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

        $balanceService = new BalanceService();

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

        $balanceService = new BalanceService();

        foreach ($payment->students as $student) {
            $balanceService->revertStudentBalance($payment, $student);
        }

        $payment->delete();
    }
}
