<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_payment_id',
        'date',
        'total_in_dolars',
        'total_in_bs',
        'reference',
        'status',
        'observations',
    ];

    protected $casts = [
        'date' => 'date',
        'total_in_dolars' => 'decimal:2',
        'total_in_bs' => 'decimal:2',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountPayment()
    {
        return $this->belongsTo(AccountPayment::class, 'account_payment_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'payment_students')
            ->withPivot('amount_in_dolars')
            ->withTimestamps();
    }

    public function histories()
    {
        return $this->hasMany(PaymentHistory::class);
    }
}
