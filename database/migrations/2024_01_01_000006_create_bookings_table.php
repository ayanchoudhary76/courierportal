<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('awb_number', 20)->unique();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('sender_name');
            $table->text('sender_address');
            $table->string('sender_pincode');
            $table->string('sender_phone');
            $table->string('receiver_name');
            $table->text('receiver_address');
            $table->string('receiver_pincode');
            $table->string('receiver_phone');
            $table->enum('service_type', [
                'express_air',
                'priority_surface',
                'economy_surface',
                'international_express',
                'international_economy',
            ]);
            $table->enum('parcel_type', ['document', 'non_document', 'fragile']);
            $table->decimal('weight_actual', 8, 3);
            $table->decimal('weight_volumetric', 8, 3);
            $table->decimal('declared_value', 10, 2)->default(0);
            $table->integer('pieces')->default(1);
            $table->decimal('base_amount', 10, 2);
            $table->json('surcharges');
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_mode', ['online', 'bill_to_account']);
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->enum('booking_status', [
                'booked',
                'pickup_scheduled',
                'picked_up',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'failed',
                'returned',
            ])->default('booked');
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
