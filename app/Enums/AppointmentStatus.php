<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case SCHEDULED = 'scheduled';
    case CANCELED  = 'canceled';
    case COMPLETED = 'completed';
    case NO_SHOW   = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Назначена',
            self::CANCELED  => 'Отменена',
            self::COMPLETED => 'Приём завершён',
            self::NO_SHOW   => 'Пациент не явился',
        };
    }
}
