<?php

namespace App\Services;

use App\Enums\BalanceStudentStatusEnum;
use App\Models\BalancePayment;
use App\Models\BalanceStudent;
use App\Models\Payment;
use App\Models\Student;

class BalanceService
{
    private const MONTH_ORDER = [
        'september',
        'october',
        'november',
        'december',
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
    ];

    public function updateStudentBalance(Payment $payment, Student $student): void
    {
        $balance = BalanceStudent::where('student_id', $student->id)
            ->where('status', BalanceStudentStatusEnum::Debt->value)
            ->orderBy('id', 'asc')
            ->first();

        if (! $balance) {
            $balance = BalanceStudent::where('student_id', $student->id)
                ->where('status', BalanceStudentStatusEnum::Pending->value)
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
            $balance->update(['status' => BalanceStudentStatusEnum::Paid->value]);

            return;
        }

        $amount = $payment->students()
            ->where('student_id', $student->id)
            ->first()
            ->pivot->amount_in_dolars;

        $balance->$monthToPay += $amount;

        $monthValue = $balance->$monthToPay;

        $newMonthStatus = match (true) {
            $monthValue == 0 => BalanceStudentStatusEnum::Paid->value,
            $monthValue < 0 => BalanceStudentStatusEnum::Debt->value,
            default => BalanceStudentStatusEnum::PartiallyPaid->value,
        };

        $balance->{$monthToPay.'_status'} = $newMonthStatus;

        $this->updateGeneralStatus($balance);

        $balance->save();

        $balancePayment = new BalancePayment;
        $balancePayment->payment_id = $payment->id;
        $balancePayment->balance_student_id = $balance->id;
        $balancePayment->amount = $amount;
        $balancePayment->month = $monthToPay;
        $balancePayment->save();
    }

    public function revertStudentBalance(Payment $payment, Student $student): void
    {
        $balancePayment = BalancePayment::where('payment_id', $payment->id)
            ->whereHas('balanceStudent', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->first();

        if (! $balancePayment) {
            return;
        }

        $balance = $balancePayment->balanceStudent;
        $month = $balancePayment->month;
        $balance->$month -= $balancePayment->amount;

        $monthValue = $balance->$month;

        $balance->{$month.'_status'} = match (true) {
            $monthValue == 0 => BalanceStudentStatusEnum::Paid->value,
            $monthValue < 0 => BalanceStudentStatusEnum::Debt->value,
            default => BalanceStudentStatusEnum::PartiallyPaid->value,
        };

        $this->updateGeneralStatus($balance);

        $balance->save();
        $balancePayment->delete();
    }

    private function updateGeneralStatus(BalanceStudent $balance): void
    {
        $allPaid = true;

        foreach (self::MONTH_ORDER as $month) {
            $statusField = $month.'_status';
            if (BalanceStudentStatusEnum::Paid->value !== $balance->$statusField) {
                $allPaid = false;
                break;
            }
        }

        $balance->status = $allPaid
            ? BalanceStudentStatusEnum::Paid->value
            : BalanceStudentStatusEnum::Debt->value;
    }
}
