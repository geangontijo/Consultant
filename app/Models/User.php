<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\OnlyNumbers;
use App\Casts\VerificationJson;
use App\Notifications\ResetPassword;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $email
 * @property string $id
 * @property string $password
 * @property Verification|null $verification
 * @property DateTime $verified_at
 * @property string $phone_number
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'password',
        'email',
        'verification',
        'verified_at',
        'phone_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'phone_number' => OnlyNumbers::class,
    ];

    public function routeNotificationForWhatsApp(): string
    {
        return mb_substr($this->phone_number, 0, 2) . mb_substr($this->phone_number, 3);
    }

    public function routeNotificationForMail(): string
    {
        return $this->email ?: '';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: function ($value) {
                Validator::validate(['password' => $value], ['password' => ['min:8']]);

                return Hash::make($value);
            }
        );
    }

    protected function verification(): Attribute
    {
        return Attribute::make(
            get: fn (string|Verification|array|null $value) => match (true) {
                is_string($value) => new Verification(json_decode($value, true)),
                is_array($value) => new Verification($value),
                default => $value
            },
            set: fn (?Verification $value) => $value
        );
    }
}
