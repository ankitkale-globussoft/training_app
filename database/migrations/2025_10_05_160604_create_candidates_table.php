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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id('candidate_id');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('org_id')->constrained('organizations', 'org_id')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('programs', 'program_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
