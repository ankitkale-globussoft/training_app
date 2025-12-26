<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainer_bank_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainer_id')->unique(); // One account per trainer for now
            $table->string('account_holder_name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->string('upi_id')->nullable();
            $table->timestamps();

            $table->foreign('trainer_id')->references('trainer_id')->on('trainers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_bank_details');
    }
};
