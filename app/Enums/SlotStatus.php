<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SlotStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Refused = 'refused';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Accepted => 'Accepté',
            self::Refused => 'Refusé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Accepted => 'success',
            self::Refused => 'danger',
        };
    }
}
