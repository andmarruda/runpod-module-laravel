<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_operations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('provider', 50);
            $table->string('service_type', 100);
            $table->string('deployment', 100);
            $table->string('endpoint_id')->nullable();
            $table->string('provider_job_id')->nullable()->index();
            $table->string('idempotency_key')->unique();
            $table->string('status', 50)->index();
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->json('context')->nullable();
            $table->json('metadata')->nullable();
            $table->json('raw_request')->nullable();
            $table->json('raw_response')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'created_at'], 'provider_operations_tenant_status');
            $table->index(['provider', 'endpoint_id', 'created_at'], 'provider_operations_endpoint');
        });
        Schema::create('provider_operation_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('provider_operation_id')->constrained('provider_operations')->cascadeOnDelete();
            $table->string('provider_job_id')->nullable()->index();
            $table->timestamp('timestamp')->nullable();
            $table->string('level', 30)->default('info');
            $table->string('source')->default('provider');
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
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
        Schema::create('provider_endpoint_price_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('provider', 50);
            $table->string('endpoint_id');
            $table->string('deployment')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('price_per_second', 14, 8)->default(0);
            $table->string('gpu_type')->nullable();
            $table->unsignedInteger('gpu_count')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamp('captured_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_endpoint_price_snapshots');
        Schema::dropIfExists('provider_operation_costs');
        Schema::dropIfExists('provider_operation_logs');
        Schema::dropIfExists('provider_operations');
    }
};
