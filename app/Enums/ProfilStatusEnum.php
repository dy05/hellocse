<?php

namespace App\Enums;

enum ProfilStatusEnum: string
{
    case INACTIF = 'inactif';
    case WAITING = 'en attente';
    case ACTIF = 'actif';
}
