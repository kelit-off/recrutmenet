<?php

namespace App\Models;

use App\Enums\JobPostStatus;
use Database\Factories\JobPostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $sector_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string|null $location
 * @property string|null $contract_type
 * @property int $positions_count
 * @property JobPostStatus $status
 * @property Carbon|null $opens_at
 * @property Carbon|null $closes_at
 * @property int|null $created_by
 */
class JobPost extends Model
{
    /** @use HasFactory<JobPostFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $attributes = ['status' => 'draft', 'positions_count' => 1];

    protected $fillable = [
        'sector_id',
        'title',
        'slug',
        'description',
        'location',
        'contract_type',
        'positions_count',
        'status',
        'opens_at',
        'closes_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => JobPostStatus::class,
            'positions_count' => 'integer',
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (JobPost $jobPost): void {
            if (blank($jobPost->slug)) {
                $jobPost->slug = static::uniqueSlug($jobPost->title);
            }
        });
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'offre';
        $slug = $base;
        $i = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /** Offres visibles et ouvertes aux candidatures. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->where('status', JobPostStatus::Open)
            ->where(fn (Builder $q) => $q->whereNull('opens_at')->orWhere('opens_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('closes_at')->orWhere('closes_at', '>=', now()));
    }

    public function isOpenForApplications(): bool
    {
        return $this->status === JobPostStatus::Open
            && ! $this->trashed()
            && $this->positions_count > 0
            && ($this->opens_at === null || $this->opens_at->isPast())
            && ($this->closes_at === null || $this->closes_at->isFuture());
    }

    public function duplicate(): self
    {
        $copy = $this->replicate(['slug']);
        $copy->title = $this->title.' (copie)';
        $copy->status = JobPostStatus::Draft;
        $copy->created_by = auth()->id();
        $copy->save();

        return $copy;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'sector_id', 'location', 'contract_type', 'positions_count', 'status', 'opens_at', 'closes_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('job_post');
    }
}
