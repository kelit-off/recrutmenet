<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\OpinionType;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $job_post_id
 * @property int $user_id
 * @property string $motivation
 * @property string $experience
 * @property string $availability
 * @property ApplicationStatus $status
 * @property string|null $rejection_reason
 * @property int|null $decided_by
 * @property Carbon|null $decided_at
 * @property int|null $validated_by
 * @property Carbon|null $validated_at
 * @property Carbon $created_at
 * @property-read JobPost $jobPost
 * @property-read User $user
 * @property-read Interview|null $interview
 */
class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory, LogsActivity;

    protected $attributes = ['status' => 'pending'];

    /**
     * Le statut et les champs de décision ne sont modifiables que par ApplicationWorkflow (forceFill).
     */
    protected $fillable = ['job_post_id', 'user_id', 'motivation', 'experience', 'availability'];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'decided_at' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function opinions(): HasMany
    {
        return $this->hasMany(Opinion::class);
    }

    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class);
    }

    public function opinionOf(User $user, OpinionType $type): ?Opinion
    {
        return $this->opinions->first(
            fn (Opinion $opinion) => $opinion->user_id === $user->id && $opinion->type === $type,
        );
    }

    public function isActive(): bool
    {
        return ! in_array($this->status, ApplicationStatus::inactive(), true);
    }

    /** Restreint aux candidatures que l'utilisateur du back-office a le droit de voir. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->canSeeAllApplications()) {
            return $query;
        }

        if ($user->can('applications.view_sector')) {
            return $query->whereHas('jobPost', fn (Builder $q) => $q->withTrashed()->whereIn('sector_id', $user->supervisedSectorIds()));
        }

        return $query->whereRaw('1 = 0');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ApplicationStatus::inactive());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'rejection_reason', 'decided_by', 'validated_by'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('application');
    }
}
