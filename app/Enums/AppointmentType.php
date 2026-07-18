<?php

namespace App\Enums;

enum AppointmentType: string
{
    case Erstberatung = 'erstberatung';
    case Folgetermin = 'folgetermin';
    case Hausbesuch = 'hausbesuch';
    case VideoCheckin = 'video_checkin';

    public function label(): string
    {
        return match ($this) {
            self::Erstberatung => 'Erstberatung',
            self::Folgetermin => 'Folgetermin',
            self::Hausbesuch => 'Hausbesuch',
            self::VideoCheckin => 'Video-Check-in',
        };
    }
}
