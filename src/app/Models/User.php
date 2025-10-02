<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            // cria carteira inicial se não existir
            if (!$user->wallet) {
                $user->wallet()->create(['balance' => 0]);
            }
        });
    }
}
