<?php

namespace App\Models;

use Spatie\DataTransferObject\DataTransferObject;

class Verification extends DataTransferObject
{
    public int $code;
    public string $expires_at;

    /**
     * @throws \Exception
     */
    public function hasExpired(): bool
    {
        return new \DateTime($this->expires_at) < new \DateTime();
    }
}
