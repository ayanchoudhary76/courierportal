<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('invoice_number', 30)->unique();
            $table->date('period_from');
            $table->date('period_to');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('gst_amount', 12, 2);
            $table->decimal('total', 12, 2);
            $table->enum('status', ['draft', 'sent', 'paid'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
