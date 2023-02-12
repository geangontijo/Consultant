<?php

namespace Models;

use App\Models\ConsultationAppointmentTime;
use Tests\TestCase;

class ConsultationAppointmentTimeTest extends TestCase
{
    public function test_successful_start_on_present()
    {
        $consultationAppointmentTime = new ConsultationAppointmentTime();
        $consultationAppointmentTime->start = new \DateTime();
        $consultationAppointmentTime->end = new \DateTime('+1 hour');

        $this->assertEquals(1, 1);
    }

    public function test_error_start_on_past()
    {
        $this->expectException(\Exception::class);
        $consultationAppointmentTime = new ConsultationAppointmentTime();
        $consultationAppointmentTime->start = new \DateTime('-1 hour');
        $consultationAppointmentTime->end = new \DateTime('+1 hour');
    }
}
