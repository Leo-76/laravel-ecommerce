<?php

namespace MonPackage\Ecommerce\Helpers;

class PrixHelper
{
    /**
     * Formate un montant en centimes vers une chaîne lisible.
     *
     * @param  int|null $centimes   Ex : 1990
     * @return string               Ex : "19,90 €"
     */
    public static function formater(?int $centimes): string
    {
        if ($centimes === null) {
            return '—';
        }

        $montant = $centimes / 100;
        $devise  = config('ecommerce.boutique.devise', 'EUR');
        $locale  = config('ecommerce.boutique.locale_prix', 'fr_FR');
        $symbole = config('ecommerce.boutique.symbole', '€');

        // Utilise intl si disponible pour un rendu parfait
        if (class_exists(\NumberFormatter::class)) {
            try {
                $fmt = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
                return $fmt->formatCurrency($montant, $devise);
            } catch (\Throwable $e) {
                // Fallback ci-dessous
            }
        }

        // Fallback sans extension intl
        return number_format($montant, 2, ',', ' ') . ' ' . $symbole;
    }

    /**
     * Raccourci statique : PrixHelper::of(1990) → "19,90 €"
     */
    public static function of(?int $centimes): string
    {
        return self::formater($centimes);
    }

    /**
     * Convertit un prix décimal (€) en centimes (int).
     * Utile pour transformer les inputs formulaires.
     *
     * @param  float|string|null $euros   Ex : "19.90" ou 19.90
     * @return int                        Ex : 1990
     */
    public static function enCentimes(float|string|null $euros): int
    {
        if ($euros === null || $euros === '') {
            return 0;
        }

        // Accepte virgule ou point décimal
        $euros = str_replace(',', '.', (string) $euros);

        return (int) round((float) $euros * 100);
    }
}
