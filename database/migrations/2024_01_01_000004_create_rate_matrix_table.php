<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_card_id')->constrained('rate_cards')->cascadeOnDelete();
            $table->enum('service_type', ['express_air', 'priority_surface', 'economy_surface']);
            $table->decimal('weight_from', 8, 3);
            $table->decimal('weight_to', 8, 3);
            $table->string('zone_code', 5);
            $table->decimal('base_rate', 10, 2);
            $table->decimal('fuel_surcharge_pct', 5, 2)->default(0);
            $table->decimal('oda_flat', 8, 2)->default(0);
            $table->decimal('cod_pct', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_matrix');
    }
};
