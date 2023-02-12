<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\ConsultationAppointmentTime
 *
 * @property \DateTimeInterface $start
 * @property \DateTimeInterface $end
 * @property float              $price
 * @property ConsultationAppointmentTimeStatus $status
 * @property int $id
 * @property string|null $user_id
 * @property int $consultation_announcement_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ConsultationAnnouncement $consultationAnnouncement
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime whereConsultationAnnouncementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConsultationAppointmentTime whereUserId($value)
 */
	class ConsultationAppointmentTime extends \Eloquent {}
}

