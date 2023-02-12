<?php

namespace App\Models;

use App\Enums\ConsultationAnnouncementCategories;
use App\Http\Controllers\ProfessionalController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property string $professional_id
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
        'description'
    ];

    public function appointment_times(): HasMany
    {
        return $this->hasMany(ConsultationAppointmentTime::class);
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

        $this->appointment_times()->createMany(array_map(fn(ConsultationAppointmentTime $object) => $object->toArray(),
            $appointmentObjects));

        $this->getRelationValue('appointment_times');
    }
}
