<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->string('action', 100);
            $table->string('target_table', 100);
            $table->unsignedBigInteger('target_id');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('ip_address', 45);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
