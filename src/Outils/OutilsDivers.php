<?php

namespace App\Outils;

class OutilsDivers {

    public static function formaterUneDateEnFr(\DateTimeInterface $date): string
    {
        if (class_exists('\IntlDateFormatter')) {
            $formatter = new \IntlDateFormatter(
                'fr_FR', // langue
                \IntlDateFormatter::LONG, // ex : 12 décembre 1937
                \IntlDateFormatter::NONE, // pas de format d'heure ici
                null, null, 'd MMMM y'     // format personnalisé
            );

            $formattedDate = $formatter->format($date);
            $formattedTime = $date->format('H\hi'); // format 10h50

            return "$formattedDate à $formattedTime";
        }

        // Fallback si intl n'est pas dispo : tableau des mois
        $mois = [
            1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
            5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
            9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
        ];

        $jour = $date->format('d');
        $moisNom = $mois[(int)$date->format('m')];
        $annee = $date->format('Y');
        $heure = $date->format('H\hi');

        return "$jour $moisNom $annee à $heure";
    }

}
