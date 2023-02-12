<?php

namespace App\Models;

use App\Casts\OnlyNumbers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $email
 * @property string $crm
 * @property string $phone_number
 * @property string  $photo_url
 */
class Professional extends Model
{
    use HasFactory;

    protected $casts = [
        'crm' => OnlyNumbers::class,
        'email' => 'string',
        'phone_number' => OnlyNumbers::class,
    ];

    protected $fillable = [
        'crm',
        'email',
        'phone_number',
        'photo_url',
    ];

    protected $hidden = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

}
