<?php

namespace App\Services;

use App\Models\BalanceStudent;
use App\Models\Payment;
use App\Models\Student;

class BalanceService
{
    private const MONTH_ORDER = [
        'september', 'october', 'november', 'december',
        'january', 'february', 'march', 'april', 'may',
        'june', 'july', 'august',
    ];

    public function updateStudentBalance(Payment $payment, Student $student): void
    {
        $balance = BalanceStudent::where('student_id', $student->id)
            ->where('status', 'in_debt')
            ->orderBy('id', 'asc')
            ->first();

        if (! $balance) {
            $balance = BalanceStudent::where('student_id', $student->id)
                ->where('status', 'pending')
                ->orderBy('id', 'asc')
                ->first();
        }

        if (! $balance) {
            return;
        }

        $monthToPay = null;
        foreach (self::MONTH_ORDER as $month) {
            if ($balance->$month > 0) {
                $monthToPay = $month;
                break;
            }
        }

        if ($monthToPay === null) {
            $balance->update(['status' => 'paid']);

            return;
        }

        $amount = $payment->students()
            ->where('student_id', $student->id)
            ->first()
            ->pivot->amount_in_dolars;

        $balance->$monthToPay += $amount;
        $balance->save();
    }
}
