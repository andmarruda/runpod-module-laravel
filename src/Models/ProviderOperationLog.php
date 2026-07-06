<?php

namespace Andmarruda\RunpodModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $provider_operation_id
 * @property string|null $provider_job_id
 * @property Carbon $timestamp
 * @property string $level
 * @property string $source
 * @property string $message
 * @property array|null $metadata
 */
final class ProviderOperationLog extends Model
{
    use HasFactory;

    protected $table = 'provider_operation_logs';

    protected $fillable = [
        'uuid',
        'provider_operation_id',
        'provider_job_id',
        'timestamp',
        'level',
        'source',
        'message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(ProviderOperation::class, 'provider_operation_id');
    }
}
