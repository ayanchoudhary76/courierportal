<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('gstin')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('pincode');
            $table->string('state');
            $table->enum('account_type', ['prepaid', 'credit'])->default('prepaid');
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->foreignId('rate_card_id')->nullable()->constrained('rate_cards')->nullOnDelete();
            $table->foreignId('created_by_admin')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
