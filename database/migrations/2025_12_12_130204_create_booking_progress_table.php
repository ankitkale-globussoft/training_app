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
        Schema::create('booking_progress', function (Blueprint $table) {
            $table->id('progress_id');

            $table->unsignedBigInteger('booking_id');

            $table->enum('status', [
                'assigned',
                'enroute',
                'arrived',
                'teaching_started',
                'ongoing',
                'completed',
                'test_completed',
                'reviewed'
            ]);

            $table->integer('percentage')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            // Foreign Key
            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_progress');
    }
};
