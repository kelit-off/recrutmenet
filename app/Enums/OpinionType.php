<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OpinionType: string implements HasLabel
{
    case First = 'first';
    case Interview = 'interview';
    case Hr = 'hr';

    public function getLabel(): string
    {
        return match ($this) {
            self::First => 'Premier avis',
            self::Interview => "Avis d'entretien",
            self::Hr => 'Avis DRH',
        };
    }
}
