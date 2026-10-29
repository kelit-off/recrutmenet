<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** Noms des rôles Spatie, stockés tels quels en base. */
enum Role: string implements HasLabel
{
    case Member = 'Membre';
    case SectorManager = 'Responsable de secteur';
    case Direction = 'Direction';
    case Hr = 'DRH';
    case Admin = 'Admin';

    public function getLabel(): string
    {
        return $this->value;
    }
}
