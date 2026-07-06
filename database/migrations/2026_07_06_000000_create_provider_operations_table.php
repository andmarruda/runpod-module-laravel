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
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_operations');
    }
};
