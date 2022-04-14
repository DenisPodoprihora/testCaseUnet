<?php

namespace App\Service;

class DateTimeService
{
    /**
     * @return \DateTime
     */
    public static function getCurrentTime(): \DateTime
    {
        return new \DateTime('now', new \DateTimeZone('UTC'));
    }
}