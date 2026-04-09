<?php

namespace MonPackage\Ecommerce\Traits;

trait APrix
{
    /**
     * Formate un montant en centimes vers une chaîne lisible.
     * Ex: 1990 → "19,90 €"
     */
    public function formaterPrix(int $centimes): string
    {
        $montant  = $centimes / 100;
        $locale   = config('ecommerce.boutique.locale_prix', 'fr_FR');
        $symbole  = config('ecommerce.boutique.symbole', '€');

        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            return $fmt->formatCurrency($montant, config('ecommerce.boutique.devise', 'EUR'));
        }

        // Fallback sans intl
        return number_format($montant, 2, ',', ' ') . ' ' . $symbole;
    }
}
