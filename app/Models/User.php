<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $discord_id
 * @property string|null $discord_username
 * @property string|null $avatar
 * @property int|null $sector_id
 */
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'discord_id',
        'discord_username',
        'avatar',
        'sector_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /** Secteurs dont l'utilisateur est le responsable. */
    public function managedSectors(): HasMany
    {
        return $this->hasMany(Sector::class, 'manager_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function interviews(): BelongsToMany
    {
        return $this->belongsToMany(Interview::class, 'interview_participants');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->where('name', '!=', Role::Member->value)->exists();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar;
    }

    public function isAdminFromEnv(): bool
    {
        return $this->discord_id !== null
            && in_array($this->discord_id, config('services.discord.admin_ids', []), true);
    }

    /**
     * Identifiants des secteurs que l'utilisateur supervise (secteur rattaché + secteurs dont il est responsable).
     *
     * @return list<int>
     */
    public function supervisedSectorIds(): array
    {
        return collect([$this->sector_id])
            ->merge($this->managedSectors()->pluck('id'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function canSeeAllApplications(): bool
    {
        return $this->can(Permission::ApplicationsViewAll->value);
    }

    /** Utilisateurs pouvant participer à un entretien. */
    public function scopeInterviewers(Builder $query): Builder
    {
        return $query->permission(Permission::InterviewsManage->value);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'sector_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user');
    }
}
