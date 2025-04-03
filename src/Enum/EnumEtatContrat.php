<?php

namespace App\Enum;

enum EnumEtatContrat : string
{
    case EN_DISCUSSION = 'En discussion';
    case A_VENIR = 'A venir';
    case EN_COURS = 'En cours';
    case EN_PAUSE = 'En pause';
    case ANNULE = 'Annulé';
    case TERMINE = 'Terminé';
}
