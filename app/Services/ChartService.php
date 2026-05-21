<?php

namespace App\Services;

use App\Models\BalancePayment;
use App\Models\BalanceStudent;
use App\Models\SchoolLapse;
use Carbon\Carbon;

class ChartService
{
    private const MONTHS = [
        'august',
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
        'july'
    ];

    public function annualVsMonthlyFlow($schoolLapse)
    {
        if (!$schoolLapse instanceof SchoolLapse) {
            $lapse = SchoolLapse::find($schoolLapse);
        } else {
            $lapse = $schoolLapse;
        }

        if (!$lapse) {
            $lapse = SchoolLapse::where('status', 1)->first();
        }

        if (!$lapse) {
            return [
                'pagado_mensual' => [],
                'esperado_mensual' => [],
                'real_acumulado' => [],
                'meta_acumulada' => []
            ];
        }

        $lapseId = $lapse->id;

        // Sum of payments assigned to each month
        $paidByMonth = BalancePayment::whereHas('balanceStudent', function ($q) use ($lapseId) {
            $q->where('school_lapse_id', $lapseId);
        })
            ->whereNotNull('month')
            ->selectRaw('month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Sum of remaining debts to calculate the "expected" (total original debt)
        $rawSelect = "";
        foreach (self::MONTHS as $month) {
            $rawSelect .= "SUM($month) as sum_$month, ";
        }
        $rawSelect = rtrim($rawSelect, ", ");

        $balancesSum = BalanceStudent::where('school_lapse_id', $lapseId)
            ->selectRaw($rawSelect)
            ->first();

        $pagado_mensual = [];
        $esperado_mensual = [];
        $real_acumulado = [];
        $meta_acumulada = [];

        $real_sum = 0;
        $meta_sum = 0;

        $now = Carbon::now();
        $startOfMonth = Carbon::parse($lapse->start)->startOfMonth();
        $isLapseActive = $lapse->status == 1;

        foreach (self::MONTHS as $index => $month) {
            $paid = (float) ($paidByMonth[$month] ?? 0);
            $remaining = abs((float) ($balancesSum->{"sum_$month"} ?? 0));
            $expected = $paid + $remaining;

            $targetDate = $startOfMonth->copy()->addMonths($index);
            // Consider a month "future" if we haven't reached its start yet
            $isFuture = $isLapseActive && $targetDate->gt($now->startOfMonth());

            $esperado_mensual[] = $expected;
            $meta_sum += $expected;
            $meta_acumulada[] = $meta_sum;

            if ($isFuture && $paid == 0) {
                $pagado_mensual[] = "";
                $real_acumulado[] = "";
            } else {
                $pagado_mensual[] = $paid;
                $real_sum += $paid;
                $real_acumulado[] = $real_sum;
            }
        }

        return [
            'pagado_mensual' => $pagado_mensual,
            'esperado_mensual' => $esperado_mensual,
            'real_acumulado' => $real_acumulado,
            'meta_acumulada' => $meta_acumulada,
        ];
    }
}
