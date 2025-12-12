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
        Schema::create('training_requirements', function (Blueprint $table) {
            $table->id('requirement_id');

            // Foreign Keys
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('program_id');

            // Trainer who accepted (optional)
            $table->unsignedBigInteger('accepted_trainer_id')->nullable();

            // Fields
            $table->enum('mode', ['online', 'offline'])->default('online');
            $table->string('location')->nullable();

            $table->dateTime('schedule_start')->nullable();
            $table->dateTime('schedule_end')->nullable();

            // Requirement lifecycle
            $table->enum('status', [
                'open',             // visible to trainers
                'pending_payment',  // trainer accepted, org needs to pay
                'assigned',         // org paid + booking created
                'completed'
            ])->default('open');

            // Payment for requirement
            $table->enum('payment', ['pending', 'completed', 'declined'])
                ->default('pending');

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('org_id')->references('org_id')->on('organisations')->onDelete('cascade');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            $table->foreign('accepted_trainer_id')->references('trainer_id')->on('trainers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_requirements');
    }
};
