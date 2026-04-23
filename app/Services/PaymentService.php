<?php

namespace App\Services;

use App\Models\AccountPayment;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function getAll()
    {
        return Payment::query()->with('students', 'accountPayment', 'user')->paginate(25)->withQueryString();
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

        foreach ($studentsData as $studentData) {
            $student = Student::where('id', $studentData['id'])
                ->where('status', '!=', 0)
                ->firstOrFail();

            $payment->students()->attach($studentData['id'], [
                'amount_in_dolars' => $studentData['amount_in_dolars'],
            ]);
        }

        $payment->load('students', 'accountPayment');

        // TODO: Aquí se debe llamar al servicio de balance para actualizar el estado de cuenta del estudiante
        // Ejemplo: $balanceService->updateStudentBalance($payment);

        // TODO: Histórico de pagos - hay un problema con MySQL/MariaDB guardando JSON
        // $this->createHistory($payment, 'created', null, $payment->toArray());

        return $payment;
    }

    private function createHistory(Payment $payment, string $action, ?array $oldData, ?array $newData): void
    {
        $userId = Auth::id() ?? 1;

        $oldJson = $oldData ? "'" . json_encode($oldData) . "'" : "'{}'";
        $newJson = $newData ? "'" . json_encode($newData) . "'" : "'{}'";

        $sql = "INSERT INTO payment_histories (payment_id, user_id, action, old_data, new_data, created_at) VALUES ({$payment->id}, {$userId}, '{$action}', {$oldJson}, {$newJson}, NOW())";

        DB::statement($sql);
    }
}
