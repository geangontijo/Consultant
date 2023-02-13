<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $product_id
 * @property int $user_id
 */
class CartItem extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ConsultationAppointmentTime::class, 'product_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
