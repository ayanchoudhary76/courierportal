<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('international_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_card_id')->constrained('rate_cards')->cascadeOnDelete();
            $table->string('country_group', 100);
            $table->enum('service_type', ['express', 'economy']);
            $table->decimal('weight_from', 8, 3);
            $table->decimal('weight_to', 8, 3);
            $table->decimal('base_rate', 10, 2);
            $table->decimal('fuel_surcharge_pct', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('international_rates');
    }
};
