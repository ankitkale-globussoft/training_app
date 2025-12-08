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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('org_id')->constrained('organizations', 'org_id')->onDelete('cascade');
            $table->foreignId('trainer_id')->constrained('trainers', 'trainer_id')->onDelete('cascade');
            $table->enum('trainer_status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->enum('training_status', ['scheduled', 'completed', 'canceled'])->default('scheduled');
            $table->text('org_review')->nullable();
            $table->integer('org_rating')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
