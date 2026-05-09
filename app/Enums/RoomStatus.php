<?php

namespace App\Enums;

enum RoomStatus: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case REPAIRING = 'repairing';
}
