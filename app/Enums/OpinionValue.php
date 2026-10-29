<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OpinionValue: string implements HasColor, HasLabel
{
    case Positive = 'positive';
    case Reserved = 'reserved';
    case Negative = 'negative';

    public function getLabel(): string
    {
        return match ($this) {
            self::Positive => 'Positif',
            self::Reserved => 'Sous réserve',
            self::Negative => 'Négatif',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Positive => 'success',
            self::Reserved => 'warning',
            self::Negative => 'danger',
        };
    }
}
