<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'sender_wallet_id',
        'receiver_wallet_id',
        'type',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'sender_wallet_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'receiver_wallet_id');
    }

    public function senderWallet()
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }

    public function receiverWallet()
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }

    public function senderName()
    {
        return optional($this->senderWallet->user)->name;
    }

    public function receiverName()
    {
        return optional($this->receiverWallet->user)->name;
    }
}
