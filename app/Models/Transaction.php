<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_code',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'status',
        'total_price',
        'snap_token',
        'expires_at',
        'paid_at',
        'payment_details',
        'address',
        'postal_code',
        'city',
        'phone',
        'payment_method',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isExpired()
    {
        return $this->status === 'expired' || $this->expires_at < now();
    }

    public function getFormattedTransactionCode(): string
    {
        return $this->transaction_code;
    }

    public function isPaymentValid(): bool
    {
        return $this->isPending() && $this->expires_at > now();
    }
}
