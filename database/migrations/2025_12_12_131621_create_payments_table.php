<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');

            $table->unsignedBigInteger('requirement_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();

            $table->enum('payer_type', ['organisation', 'admin']);
            $table->enum('payee_type', ['admin', 'trainer']);

            $table->decimal('amount', 10, 2);

            $table->string('transaction_id')->nullable();

            $table->enum('transaction_type', ['training_fee', 'trainer_payout']);
            $table->enum('payment_status', ['pending', 'success', 'failed'])
                ->default('pending');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('requirement_id')
                ->references('requirement_id')
                ->on('training_requirements')
                ->onDelete('cascade');

            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
