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
        Schema::create('program_trainer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('trainer_id');
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('program_id')
                  ->references('program_id')
                  ->on('programs')
                  ->onDelete('cascade');
                  
            $table->foreign('trainer_id')
                  ->references('trainer_id')
                  ->on('trainers')
                  ->onDelete('cascade');

            // Prevent duplicate entries
            $table->unique(['program_id', 'trainer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_trainer');
    }
};
