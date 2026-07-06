<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_operation_logs');
    }
};
