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
        Schema::create('content_modules', function (Blueprint $table) {
            $table->id('module_id');

            $table->foreignId('booking_id')
                  ->constrained('bookings', 'booking_id')
                  ->onDelete('cascade');

            $table->string('title');
            $table->integer('order_no')->default(0);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_modules');
    }
};
