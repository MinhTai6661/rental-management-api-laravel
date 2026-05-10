<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetTokens extends Model
{
    protected $table = 'password_reset_tokens';

    protected $fillable = [
        'token',
        'expires_at',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
