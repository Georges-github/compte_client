<?php

namespace App\Validation;

class ContraintesDuMotDePasse
{
    public const LONGUEUR_MIN = 8;
    public const LONGUEUR_MAX = 32;

    public const SCORE_MIN = 1;

    public const REGEX_COMPLEXE = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.*\s).{8,32}$/';

    public const MESSAGE_COMPLEXE = 'Le mot de passe doit contenir entre ' . self::LONGUEUR_MIN . ' et ' . self::LONGUEUR_MAX . ' caractères, incluant au moins une majuscule, une minuscule, un chiffre, un caractère spécial et aucun espace.';
}
