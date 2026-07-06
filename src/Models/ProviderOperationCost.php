<?php

namespace Andmarruda\RunpodModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property int $provider_operation_id
 * @property string $source
 * @property string $confidence
 * @property string $currency
 * @property string $total_cost
 * @property string|null $compute_cost
 * @property string|null $network_cost
 * @property string|null $storage_cost
 * @property string|null $provider_fee
 * @property float|null $billable_seconds
 * @property float|null $queue_seconds
 * @property float|null $execution_seconds
 * @property string|null $gpu_type
 * @property int $gpu_count
 * @property string|null $endpoint_id
 * @property string|null $endpoint_price_per_second
 * @property array|null $pricing_snapshot
 * @property array|null $raw_usage
 */
final class ProviderOperationCost extends Model
{
    use HasFactory;

    protected $table = 'provider_operation_costs';

    protected $fillable = [
        'uuid',
        'provider_operation_id',
        'source',
        'confidence',
        'currency',
        'total_cost',
        'compute_cost',
        'network_cost',
        'storage_cost',
        'provider_fee',
        'billable_seconds',
        'queue_seconds',
        'execution_seconds',
        'gpu_type',
        'gpu_count',
        'endpoint_id',
        'endpoint_price_per_second',
        'pricing_snapshot',
        'raw_usage',
    ];

    protected function casts(): array
    {
        return [
            'gpu_count' => 'integer',
            'pricing_snapshot' => 'array',
            'raw_usage' => 'array',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(ProviderOperation::class, 'provider_operation_id');
    }
}
