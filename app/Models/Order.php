<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * @property float $items_amount
 * @property string $id
 */
class Order extends Model
{
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'items_amount' => 'decimal:2',
        'status' => OrderStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }
}
