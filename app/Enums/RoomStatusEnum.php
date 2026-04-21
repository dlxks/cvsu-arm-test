<?php

namespace App\Enums;

enum RoomStatusEnum: string
{
    case USEABLE = 'useable';
    case NOT_USEABLE = 'not_useable';
    case UNDER_CONSTRUCTION = 'under_construction';
    case UNDER_RENOVATION = 'under_renovation';
}
