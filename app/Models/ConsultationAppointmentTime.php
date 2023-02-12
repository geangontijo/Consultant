<?php

namespace App\Models;

use App\Enums\ConsultationAppointmentTimesStatus;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \DateTimeInterface $start
 * @property \DateTimeInterface $end
 * @property float              $price
 * @property ConsultationAppointmentTimesStatus $status
 */
class ConsultationAppointmentTime extends Model
{
    use HasFactory,
        DefaultDateFormatModel;

    protected $fillable = [
        'start',
        'end',
        'price',
        'status'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'price' => 'decimal:2',
        'status' => ConsultationAppointmentTimesStatus::class
    ];

    /**
     * @throws \Exception
     */
    protected function start(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: $this->setStartEndDateFn('start')
        );
    }

    /**
     * @throws \Exception
     */
    protected function end(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: $this->setStartEndDateFn('end')
        );
    }

    public function consultationAnnouncement(): BelongsTo
    {
        return $this->belongsTo(ConsultationAnnouncement::class);
    }

    /**
     * @throws \Exception
     */
    protected function setStartEndDateFn(string $attributeName): callable
    {
        return function ($value) use ($attributeName) {
            $dateTime = Carbon::parse($value)->toDateTime();
            if (($attributeName === 'start' && empty($this->attributes['end'])) ||
                ($attributeName === 'end' && empty($this->attributes['start']))) {

                return $dateTime;
            }

            $end = $attributeName === 'end' ? $dateTime : $this->attributes['end'];
            $start = $attributeName === 'start' ? $dateTime : $this->attributes['start'];

            $currentDate = new DateTime();
            if ($start > $end) {
                throw new Exception('Start date must be before end date');
            } elseif (($diff = $start->getTimestamp() - $currentDate->getTimestamp()) < 0) {
                throw new Exception("Start date must be in the present/future. Difference in seconds {$diff}");
            }

            return $dateTime;
        };
    }

}
