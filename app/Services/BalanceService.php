<?php

namespace App\Services;

use App\Enums\BalanceStudentStatusEnum;
use App\Models\BalancePayment;
use App\Models\BalanceStudent;
use App\Models\Payment;
use App\Models\Student;
use Exception;

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

    public function updateStudentBalance(Payment $payment, Student $student, array $balances): void
    {
        $sortedBalances = collect($balances)
            ->sortBy('id')
            ->values();

        $amount = $payment->students()
            ->where('student_id', $student->id)
            ->first()
            ->pivot->amount_in_dolars;

        $remainingAmount = $amount;

        foreach ($sortedBalances as $balanceData) {
            if ($remainingAmount <= 0) {
                break;
            }

            $balance = BalanceStudent::find($balanceData['id']);

            if (! $balance) {
                continue;
            }

            if ($balance->inscription < 0) {
                $inscriptionDebt = abs($balance->inscription);

                if ($remainingAmount < $inscriptionDebt) {
                    throw new Exception(
                        "El pago no cubre la inscripción del estudiante {$student->name} {$student->last_name}. Deuda: {$inscriptionDebt}$, Disponible: {$remainingAmount}$"
                    );
                }

                $balance->inscription += $inscriptionDebt;
                $balance->inscription_status = BalanceStudentStatusEnum::Paid->value;

                BalancePayment::create([
                    'payment_id' => $payment->id,
                    'balance_student_id' => $balance->id,
                    'amount' => $inscriptionDebt,
                    'month' => null,
                    'is_inscription' => true,
                ]);

                $remainingAmount -= $inscriptionDebt;
            }

            foreach (self::MONTH_ORDER as $month) {
                if ($remainingAmount <= 0) {
                    break;
                }

                if ($balance->$month < 0) {
                    $monthDebt = abs($balance->$month);
                    $paymentToMonth = min($remainingAmount, $monthDebt);

                    $balance->$month += $paymentToMonth;

                    $monthValue = $balance->$month;
                    $balance->{$month.'_status'} = match (true) {
                        $monthValue == 0 => BalanceStudentStatusEnum::Paid->value,
                        $monthValue < 0 => BalanceStudentStatusEnum::Debt->value,
                        default => BalanceStudentStatusEnum::PartiallyPaid->value,
                    };

                    BalancePayment::create([
                        'payment_id' => $payment->id,
                        'balance_student_id' => $balance->id,
                        'amount' => $paymentToMonth,
                        'month' => $month,
                        'is_inscription' => false,
                    ]);

                    $remainingAmount -= $paymentToMonth;
                }
            }

            $this->updateGeneralStatus($balance);
            $balance->save();
        }
    }

    public function revertStudentBalance(Payment $payment, Student $student): void
    {
        $balancePayments = BalancePayment::where('payment_id', $payment->id)
            ->whereHas('balanceStudent', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->get();

        $groupedByBalance = $balancePayments->groupBy('balance_student_id');

        foreach ($groupedByBalance as $balanceId => $bps) {
            $balance = BalanceStudent::find($balanceId);
            if (! $balance) {
                continue;
            }

            foreach ($bps as $balancePayment) {
                if ($balancePayment->is_inscription) {
                    $balance->inscription -= $balancePayment->amount;
                } else {
                    $month = $balancePayment->month;
                    if ($month) {
                        $balance->$month -= $balancePayment->amount;
                    }
                }
                $balancePayment->delete();
            }

            $inscriptionValue = $balance->inscription;
            $balance->inscription_status = match (true) {
                $inscriptionValue == 0 => BalanceStudentStatusEnum::Paid->value,
                $inscriptionValue < 0 => BalanceStudentStatusEnum::Debt->value,
                default => BalanceStudentStatusEnum::PartiallyPaid->value,
            };

            foreach (self::MONTH_ORDER as $month) {
                $monthValue = $balance->$month;
                $balance->{$month.'_status'} = match (true) {
                    $monthValue == 0 => BalanceStudentStatusEnum::Paid->value,
                    $monthValue < 0 => BalanceStudentStatusEnum::Debt->value,
                    default => BalanceStudentStatusEnum::PartiallyPaid->value,
                };
            }

            $this->updateGeneralStatus($balance);
            $balance->save();
        }
    }

    private function updateGeneralStatus(BalanceStudent $balance): void
    {
        $statuses = [];

        if ($balance->getAttribute('inscription_status')) {
            $statuses[] = $balance->inscription_status;
        }

        foreach (self::MONTH_ORDER as $month) {
            $statusField = $month.'_status';
            $statuses[] = $balance->$statusField;
        }

        $allPaid = collect($statuses)->every(
            fn ($status) => $status === BalanceStudentStatusEnum::Paid->value
        );

        $balance->status = $allPaid
            ? BalanceStudentStatusEnum::Paid->value
            : BalanceStudentStatusEnum::Debt->value;
    }
}
