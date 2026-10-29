<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ApplicationStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case FirstOpinionGiven = 'first_opinion_given';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case InterviewProposed = 'interview_proposed';
    case CounterProposed = 'counter_proposed';
    case InterviewConfirmed = 'interview_confirmed';
    case InterviewDone = 'interview_done';
    case Hired = 'hired';
    case NotRetained = 'not_retained';
    case Withdrawn = 'withdrawn';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::FirstOpinionGiven => 'Premier avis donné',
            self::Accepted => 'Acceptée',
            self::Rejected => 'Refusée',
            self::InterviewProposed => 'Entretien proposé',
            self::CounterProposed => 'Contre-proposition',
            self::InterviewConfirmed => 'Entretien confirmé',
            self::InterviewDone => 'Entretien réalisé',
            self::Hired => 'Recruté',
            self::NotRetained => 'Non retenu',
            self::Withdrawn => 'Retirée',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending, self::Withdrawn => 'gray',
            self::FirstOpinionGiven, self::InterviewDone => 'info',
            self::Accepted, self::InterviewConfirmed => 'primary',
            self::InterviewProposed, self::CounterProposed => 'warning',
            self::Hired => 'success',
            self::Rejected, self::NotRetained => 'danger',
        };
    }

    /**
     * Statuts qui libèrent la contrainte d'unicité : le candidat peut postuler de nouveau.
     *
     * @return list<self>
     */
    public static function inactive(): array
    {
        return [self::Rejected, self::NotRetained, self::Withdrawn];
    }

    /** @return list<self> */
    public static function negotiating(): array
    {
        return [self::InterviewProposed, self::CounterProposed];
    }

    public function canBeWithdrawn(): bool
    {
        return in_array($this, [
            self::Pending,
            self::FirstOpinionGiven,
            self::Accepted,
            self::InterviewProposed,
            self::CounterProposed,
            self::InterviewConfirmed,
        ], true);
    }
}
