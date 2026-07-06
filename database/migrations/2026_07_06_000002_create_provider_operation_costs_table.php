<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_operation_costs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('provider_operation_id')->constrained('provider_operations')->cascadeOnDelete();
            $table->string('source', 50);
            $table->string('confidence', 50);
            $table->string('currency', 3)->default('USD');
            $table->decimal('total_cost', 14, 6)->default(0);
            $table->decimal('compute_cost', 14, 6)->nullable();
            $table->decimal('network_cost', 14, 6)->nullable();
            $table->decimal('storage_cost', 14, 6)->nullable();
            $table->decimal('provider_fee', 14, 6)->nullable();
            $table->decimal('billable_seconds', 12, 3)->nullable();
            $table->decimal('queue_seconds', 12, 3)->nullable();
            $table->decimal('execution_seconds', 12, 3)->nullable();
            $table->string('gpu_type')->nullable();
            $table->unsignedInteger('gpu_count')->default(1);
            $table->string('endpoint_id')->nullable();
            $table->decimal('endpoint_price_per_second', 14, 8)->nullable();
            $table->json('pricing_snapshot')->nullable();
            $table->json('raw_usage')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_operation_costs');
    }
};
