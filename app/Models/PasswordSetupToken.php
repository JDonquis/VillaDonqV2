<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PasswordSetupToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    public function markAsUsed(): void
    {
        $this->used_at = now();
        $this->save();
    }

    public static function generateForUser(User $user, int $expiresInHours = 12): self
    {
        $token = self::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addHours($expiresInHours),
        ]);

        return $token;
    }
}
