<?php

namespace App\Services;

use App\Enums\BalanceStudentStatusEnum;
use App\Models\BalancePayment;
use App\Models\BalanceStudent;
use App\Models\MainConfig;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
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
                    $balance->{$month.'_status'} = $this->determineMonthStatus($monthValue, $month);

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
                $balance->{$month.'_status'} = $this->determineMonthStatus($monthValue, $month);
            }

            $this->updateGeneralStatus($balance);
            $balance->save();
        }
    }

    public function recalculateBalanceForExemption(Student $student, float $exemptionPercentage, bool $applyToPastDebts): void
    {
        $multiplier = 1 - ($exemptionPercentage / 100);

        $currentMonthName = strtolower(Carbon::now()->englishMonth);
        $monthOrder = array_flip(self::MONTH_ORDER);
        $currentMonthIndex = $monthOrder[$currentMonthName] ?? -1;

        $balances = BalanceStudent::where('student_id', $student->id)->get();

        foreach ($balances as $balance) {
            $balancePayments = BalancePayment::where('balance_student_id', $balance->id)->get();
            $paymentsByMonth = $balancePayments->groupBy('month');
            $totalPaidInscription = $balancePayments->where('is_inscription', true)->sum('amount');

            foreach (self::MONTH_ORDER as $month) {
                $totalPaid = (float) (($paymentsByMonth->get($month, collect()))->sum('amount'));
                $currentValue = (float) $balance->$month;

                $originalCharge = $currentValue - $totalPaid;

                if ($originalCharge >= 0) {
                    continue;
                }

                if (! $applyToPastDebts) {
                    $monthIndex = $monthOrder[$month] ?? -1;
                    if ($monthIndex < $currentMonthIndex) {
                        continue;
                    }
                }

                $newCharge = $originalCharge * $multiplier;
                $balance->$month = $newCharge + $totalPaid;
                $balance->{$month.'_status'} = $this->determineMonthStatus((float) $balance->$month, $month);
            }

            $currentInscription = (float) $balance->inscription;
            $originalInscriptionCharge = $currentInscription - $totalPaidInscription;

            if ($originalInscriptionCharge < 0) {
                $newInscriptionCharge = $originalInscriptionCharge * $multiplier;
                $balance->inscription = $newInscriptionCharge + $totalPaidInscription;
                $inscriptionValue = (float) $balance->inscription;
                $balance->inscription_status = match (true) {
                    $inscriptionValue == 0 => BalanceStudentStatusEnum::Paid->value,
                    $inscriptionValue < 0 => BalanceStudentStatusEnum::Debt->value,
                    default => BalanceStudentStatusEnum::PartiallyPaid->value,
                };
            }

            $this->updateGeneralStatus($balance);
            $balance->save();
        }
    }

    private function determineMonthStatus(float $monthValue, string $monthName): string
    {
        if ($monthValue == 0) {
            return BalanceStudentStatusEnum::Paid->value;
        }

        if ($monthValue > 0) {
            return BalanceStudentStatusEnum::PartiallyPaid->value;
        }

        $currentMonthName = strtolower(Carbon::now()->englishMonth);
        $monthOrder = array_flip(self::MONTH_ORDER);
        $currentMonthIndex = $monthOrder[$currentMonthName] ?? -1;
        $monthIndex = $monthOrder[$monthName] ?? -1;

        if ($monthIndex > $currentMonthIndex) {
            return BalanceStudentStatusEnum::Pending->value;
        }

        if ($monthIndex < $currentMonthIndex) {
            return BalanceStudentStatusEnum::Debt->value;
        }

        $config = MainConfig::select('day_of_monthly_payment')->first();
        $dayOfMonthlyPayment = $config->day_of_monthly_payment ?? 1;

        return Carbon::now()->day >= $dayOfMonthlyPayment
            ? BalanceStudentStatusEnum::Debt->value
            : BalanceStudentStatusEnum::Pending->value;
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

        if ($allPaid) {
            $balance->status = BalanceStudentStatusEnum::Paid->value;

            return;
        }

        $hasDebt = collect($statuses)->contains(
            fn ($status) => $status === BalanceStudentStatusEnum::Debt->value
        );

        if ($hasDebt) {
            $balance->status = BalanceStudentStatusEnum::Debt->value;

            return;
        }

        $hasPartial = collect($statuses)->contains(
            fn ($status) => $status === BalanceStudentStatusEnum::PartiallyPaid->value
        );

        $balance->status = $hasPartial
            ? BalanceStudentStatusEnum::PartiallyPaid->value
            : BalanceStudentStatusEnum::Pending->value;
    }
}
