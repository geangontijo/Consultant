<?php

namespace App\Models;

trait DefaultDateFormatModel
{
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

}
