<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }
};
