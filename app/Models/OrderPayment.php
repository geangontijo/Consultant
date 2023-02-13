<?php

namespace App\Models;

use App\Enums\OrderPaymentMethods;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPayment extends Model
{
    protected $casts = [
        'method' => OrderPaymentMethods::class,
        'transaction_id' => 'string',
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'order_id' => 'string',
    ];

    protected $fillable = [
        'order_id',
        'method',
        'transaction_id',
        'amount',
        'metadata'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
