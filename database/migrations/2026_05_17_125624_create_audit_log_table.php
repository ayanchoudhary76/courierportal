<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 60);           // create, update, delete, assign_rate_card, etc.
            $table->string('target_table', 60);
            $table->unsignedBigInteger('target_id');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // No updated_at — audit logs are append-only
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
