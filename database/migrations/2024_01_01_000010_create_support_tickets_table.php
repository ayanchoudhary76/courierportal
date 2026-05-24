<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('ticket_number', 20)->unique();
            $table->enum('category', [
                'delayed_shipment',
                'damage',
                'wrong_delivery',
                'invoice_issue',
                'rate_query',
                'other',
            ]);
            $table->string('awb_number', 20)->nullable();
            $table->string('subject', 255);
            $table->text('description');
            $table->string('file_path', 500)->nullable();
            $table->enum('status', ['open', 'inprogress', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
