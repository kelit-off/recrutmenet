<?php

namespace App\Models;

use App\Enums\SlotSide;
use App\Enums\SlotStatus;
use Database\Factories\InterviewSlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $interview_id
 * @property int|null $proposed_by
 * @property SlotSide $proposed_by_side
 * @property Carbon $starts_at
 * @property int $duration_minutes
 * @property SlotStatus $status
 * @property string|null $refusal_reason
 * @property int|null $responded_by
 * @property Carbon|null $responded_at
 * @property-read Interview $interview
 */
class InterviewSlot extends Model
{
    /** @use HasFactory<InterviewSlotFactory> */
    use HasFactory, LogsActivity;

    protected $attributes = ['status' => 'pending'];

    protected $fillable = [
        'interview_id',
        'proposed_by',
        'proposed_by_side',
        'starts_at',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'proposed_by_side' => SlotSide::class,
            'status' => SlotStatus::class,
            'starts_at' => 'datetime',
            'responded_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /** Ex. « 12/11 à 14h » ou « 12/11 à 14h30 ». */
    public function shortLabel(): string
    {
        $time = $this->starts_at->minute === 0 ? $this->starts_at->format('G\h') : $this->starts_at->format('G\hi');

        return $this->starts_at->format('d/m').' à '.$time;
    }

    public function longLabel(): string
    {
        return $this->starts_at->translatedFormat('l d/m/Y à H:i').' ('.$this->duration_minutes.' min)';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['starts_at', 'duration_minutes', 'status', 'refusal_reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('interview_slot');
    }
}
