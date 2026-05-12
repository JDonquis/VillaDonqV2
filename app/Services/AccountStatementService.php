<?php

namespace App\Services;

use App\Models\SchoolLapse;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;

class AccountStatementService
{
    private const MONTHS = [
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december',
    ];

    public function getAll($params = [])
    {
        $query = Student::where('status', '!=', 0)
            ->with(['course', 'section', 'representative.user'])
            ->when(isset($params['section_id']), function ($q) use ($params) {
                $q->where('section_id', $params['section_id']);
            })
            ->with([
                'balances' => function ($q) use ($params) {

                    $q->whereHas('schoolLapse', function ($sq) use ($params) {
                        if (isset($params['school_lapse_year'])) {
                            $sq->whereYear('start', $params['school_lapse_year']);
                        }

                        if (isset($params['start_date'])) {
                            $sq->where('start', '>=', $params['start_date']);
                        }

                        if (isset($params['end_date'])) {
                            $sq->where('end', '<=', $params['end_date']);
                        }
                    });

                    $q->with(['balancePayments.payment.accountPayment.method']);
                    $q->oldest();
                },
            ]);

        $students = $query->get();

        $students = $students->map(function ($student) {
            $totalDebt = 0;
            $totalIncome = 0;

            $balances = $student->balances->map(function ($balance) use (&$totalDebt, &$totalIncome) {
                $balanceDebt = 0;

                if ($balance->inscription < 0) {
                    $balanceDebt += abs($balance->inscription);
                }

                foreach (self::MONTHS as $month) {
                    if ($balance->$month < 0) {
                        $balanceDebt += abs($balance->$month);
                    }
                }

                $balanceIncome = $balance->balancePayments->sum('amount');

                $totalDebt += $balanceDebt;
                $totalIncome += $balanceIncome;

                return [
                    'id' => $balance->id,
                    'status' => $balance->status,
                    'school_lapse' => $balance->schoolLapse,
                    'inscription' => $balance->inscription,
                    'inscription_status' => $balance->inscription_status,
                    'months' => collect(self::MONTHS)->mapWithKeys(function ($month) use ($balance) {
                        return [$month => $balance->$month, $month.'_status' => $balance->{$month.'_status'}];
                    }),
                    'total_debt' => $balanceDebt,
                    'total_income' => $balanceIncome,
                    'balance_payments' => $balance->balancePayments->map(fn ($bp) => [
                        'id' => $bp->id,
                        'amount' => $bp->amount,
                        'month' => $bp->month,
                        'is_inscription' => $bp->is_inscription,
                        'payment' => $bp->payment ? [
                            'id' => $bp->payment->id,
                            'date' => $bp->payment->date,
                            'total_in_dolars' => $bp->payment->total_in_dolars,
                            'total_in_bs' => $bp->payment->total_in_bs,
                            'reference' => $bp->payment->reference,
                            'observations' => $bp->payment->observations,
                            'account_payment' => $bp->payment->accountPayment ? [
                                'id' => $bp->payment->accountPayment->id,
                                'person_name' => $bp->payment->accountPayment->person_name,
                                'method' => $bp->payment->accountPayment->method,
                            ] : null,
                        ] : null,
                    ]),
                ];
            })->filter(function ($balance) {
                return $balance['school_lapse'] !== null;
            });

            return [
                'id' => $student->id,
                'name' => $student->name,
                'last_name' => $student->last_name,
                'ci' => $student->ci,
                'phone_number' => $student->phone_number,
                'course' => $student->course,
                'section' => $student->section,
                'representative' => $student->representative,
                'balances' => $balances,
                'total_debt' => $totalDebt,
                'total_income' => $totalIncome,
            ];
        });

        if (isset($params['debt_status'])) {
            if ($params['debt_status'] === 'debt') {
                $students = $students->filter(fn ($s) => $s['total_debt'] > 0);
            } elseif ($params['debt_status'] === 'no_debt') {
                $students = $students->filter(fn ($s) => $s['total_debt'] == 0);
            }
        }

        $sortField = $params['sort_field'] ?? 'name';
        $sortDirection = $params['sort_direction'] ?? 'asc';

        $students = match ($sortField) {
            'debt' => $sortDirection === 'desc'
                ? $students->sortBy('total_debt')
                : $students->sortByDesc('total_debt'),
            'name' => $sortDirection === 'desc'
                ? $students->sortByDesc('name')
                : $students->sortBy('name'),
            'last_name' => $sortDirection === 'desc'
                ? $students->sortByDesc('last_name')
                : $students->sortBy('last_name'),
            'course' => $sortDirection === 'desc'
                ? $students->sortByDesc(fn ($s) => optional($s['course'])->name ?? '')
                : $students->sortBy(fn ($s) => optional($s['course'])->name ?? ''),
            'section' => $sortDirection === 'desc'
                ? $students->sortByDesc(fn ($s) => optional($s['section'])->name ?? '')
                : $students->sortBy(fn ($s) => optional($s['section'])->name ?? ''),
            default => $students,
        };

        $students = $students->values();

        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 25;

        $paginatedItems = $students->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $paginatedItems,
            $students->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'students' => $paginator,
            'total_debt' => $students->sum('total_debt'),
            'total_income' => $students->sum('total_income'),
            'school_lapses' => SchoolLapse::where('status', '!=', 0)->get(),
            'sections' => Section::all(),
        ];
    }
}
