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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); 
            $table->string('name');
            $table->text('about');
            $table->string('image')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('experience');
            $table->boolean('is_executive')->default(false);
            $table->text('block1')->nullable();
            $table->text('block2')->nullable();
            $table->text('block3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
