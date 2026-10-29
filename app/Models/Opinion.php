<?php

namespace App\Models;

use App\Enums\OpinionType;
use App\Enums\OpinionValue;
use Database\Factories\OpinionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $application_id
 * @property int $user_id
 * @property OpinionType $type
 * @property OpinionValue $value
 * @property string $comment
 * @property-read Application $application
 * @property-read User $user
 */
class Opinion extends Model
{
    /** @use HasFactory<OpinionFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = ['application_id', 'user_id', 'type', 'value', 'comment'];

    protected function casts(): array
    {
        return [
            'type' => OpinionType::class,
            'value' => OpinionValue::class,
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'value', 'comment'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('opinion');
    }
}
