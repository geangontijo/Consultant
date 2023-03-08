<?php

namespace App\Models;

use App\Enums\ConsultationAnnouncementCategories;
use App\Http\Controllers\ProfessionalController;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property string $professional_id
 * @property ConsultationAnnouncementCategories $category
 */
class ConsultationAnnouncement extends Model
{
    use HasFactory;

    protected $casts = [
        'professional_id' => 'string',
        'category' => ConsultationAnnouncementCategories::class,
        'description' => 'string'
    ];

    protected $fillable = [
        'category',
        'description',
        'professional_id'
    ];

    protected $appends = [
        'category_name'
    ];

    public function appointmentTimes(): HasMany
    {
        return $this->hasMany(ConsultationAppointmentTime::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'professional_id', 'user_id');
    }

    public function categoryName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category->getName(),
        );
    }

    public function createByArray(array $appointmentList, bool $verifyAlreadyExists = false): void
    {
        if (empty($appointmentList)) {
            return;
        }

        $appointmentObjects = [];
        foreach ($appointmentList as $appointment) {
            $consultationAppointmentTime = new ConsultationAppointmentTime();
            $consultationAppointmentTime->fill($appointment);
            $consultationAppointmentTime->consultationAnnouncement()->associate($this);

            if ($verifyAlreadyExists && DB::table('consultation_appointment_times')
                                          ->where('consultation_announcement_id', $this->id)
                                          ->where('start', $consultationAppointmentTime->start)
                                          ->where('end', $consultationAppointmentTime->end)
                                          ->exists()) {
                // todo: throw exception
            }
            $appointmentObjects[] = $consultationAppointmentTime;
        }

        $this->appointmentTimes()->createMany(array_map(
            fn (ConsultationAppointmentTime $object) => $object->toArray(),
            $appointmentObjects
        ));

        $this->getRelationValue('appointment_times');
    }
}
