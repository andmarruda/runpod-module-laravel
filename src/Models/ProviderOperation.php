<?php

namespace Andmarruda\RunpodModule\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $team_id
 * @property int|null $user_id
 * @property string $provider
 * @property string $service_type
 * @property string $deployment
 * @property string|null $endpoint_id
 * @property string|null $provider_job_id
 * @property string $idempotency_key
 * @property string $status
 * @property array|null $input
 * @property array|null $output
 * @property array|null $context
 * @property array|null $metadata
 * @property array|null $raw_request
 * @property array|null $raw_response
 * @property string|null $error_code
 * @property string|null $error_message
 * @property Carbon|null $dispatched_at
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $failed_at
 * @property Carbon|null $cancelled_at
 * @property-read Collection<int, ProviderOperationLog> $logs
 * @property-read Collection<int, ProviderOperationCost> $costs
 */
final class ProviderOperation extends Model
{
    use HasFactory;

    protected $table = 'provider_operations';

    protected $fillable = [
        'uuid',
        'team_id',
        'user_id',
        'provider',
        'service_type',
        'deployment',
        'endpoint_id',
        'provider_job_id',
        'idempotency_key',
        'status',
        'input',
        'output',
        'context',
        'metadata',
        'raw_request',
        'raw_response',
        'error_code',
        'error_message',
        'dispatched_at',
        'started_at',
        'completed_at',
        'failed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
            'context' => 'array',
            'metadata' => 'array',
            'raw_request' => 'array',
            'raw_response' => 'array',
            'dispatched_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProviderOperationLog::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(ProviderOperationCost::class);
    }
}
