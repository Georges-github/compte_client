<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validations {

    public static function validerMotDePasse(string $motDePasse, ValidatorInterface $validator): array
    {
        $violations = $validator->validate(
            $motDePasse,
            [
                new Assert\NotBlank(),
                new Assert\NotCompromisedPassword(),
                new Assert\PasswordStrength( ['minScore' => ContraintesDuMotDePasse::SCORE_MIN ] ),
                new Assert\Regex(['pattern' => ContraintesDuMotDePasse::REGEX_COMPLEXE,
                                'message' => ContraintesDuMotDePasse::MESSAGE_COMPLEXE]),
            ]);

        $erreurs = [];
        foreach ($violations as $violation) {
            $erreurs[] = $violation->getMessage();
        }

        return $erreurs;
    }

}
