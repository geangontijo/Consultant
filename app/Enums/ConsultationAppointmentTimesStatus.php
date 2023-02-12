<?php

namespace App\Enums;

enum ConsultationAppointmentTimesStatus: string
{
    use EnumCasesAsStringArray;

    case Reserved = 'reserved';
    case Available = 'available';
}
