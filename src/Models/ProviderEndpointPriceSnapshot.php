<?php

namespace Andmarruda\RunpodModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ProviderEndpointPriceSnapshot extends Model
{
    use HasFactory;

    protected $table = 'provider_endpoint_price_snapshots';

    protected $fillable = ['uuid', 'provider', 'endpoint_id', 'deployment', 'currency', 'price_per_second', 'gpu_type', 'gpu_count', 'metadata', 'captured_at'];

    protected function casts(): array
    {
        return ['gpu_count' => 'integer', 'metadata' => 'array', 'captured_at' => 'datetime'];
    }
}
