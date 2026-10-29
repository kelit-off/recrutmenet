<?php

namespace App\Models;

use App\Enums\SlotStatus;
use Database\Factories\InterviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $application_id
 * @property string|null $location_or_link
 * @property string|null $report
 * @property Carbon|null $report_finalized_at
 * @property int|null $confirmed_slot_id
 * @property-read Application $application
 * @property-read InterviewSlot|null $confirmedSlot
 */
class Interview extends Model
{
    /** @use HasFactory<InterviewFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = ['application_id', 'location_or_link', 'report'];

    protected function casts(): array
    {
        return [
            'report_finalized_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'interview_participants');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(InterviewSlot::class)->orderBy('created_at')->orderBy('id');
    }

    public function pendingSlot(): HasOne
    {
        return $this->hasOne(InterviewSlot::class)->where('status', SlotStatus::Pending);
    }

    public function confirmedSlot(): BelongsTo
    {
        return $this->belongsTo(InterviewSlot::class, 'confirmed_slot_id');
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->whereKey($user->id)->exists();
    }

    public function isReportFinalized(): bool
    {
        return $this->report_finalized_at !== null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        // Le compte-rendu est sauvegardé automatiquement toutes les 10 s : on ne journalise pas chaque frappe.
        return LogOptions::defaults()
            ->logOnly(['location_or_link', 'report_finalized_at', 'confirmed_slot_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('interview');
    }
}
