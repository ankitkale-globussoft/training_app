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
        Schema::create('attempts', function (Blueprint $table) {
            $table->id('attempt_id');
            $table->foreignId('candidate_id')->constrained('candidates', 'candidate_id')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('tests', 'test_id')->onDelete('cascade');
            $table->json('answers');
            $table->integer('score');
            $table->foreignId('certificate_id')->nullable()->constrained('certificates', 'certificate_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
