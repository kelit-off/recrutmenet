<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SlotSide: string implements HasLabel
{
    case Direction = 'direction';
    case Candidate = 'candidate';

    public function getLabel(): string
    {
        return match ($this) {
            self::Direction => 'Direction',
            self::Candidate => 'Candidat',
        };
    }
}
