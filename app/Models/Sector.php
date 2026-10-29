<?php

namespace App\Models;

use Database\Factories\SectorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $manager_id
 * @property string|null $discord_webhook_url
 */
class Sector extends Model
{
    /** @use HasFactory<SectorFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'description', 'manager_id', 'discord_webhook_url'];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class);
    }

    public function applications(): HasManyThrough
    {
        return $this->hasManyThrough(Application::class, JobPost::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'manager_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('sector');
    }
}
