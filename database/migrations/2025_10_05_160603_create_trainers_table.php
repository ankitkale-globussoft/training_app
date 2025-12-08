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
        Schema::create('trainers', function (Blueprint $table) {
            $table->id('trainer_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('addr_line1');
            $table->string('addr_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('district');
            $table->string('pincode');
            $table->string('resume_link')->nullable();
            $table->string('profile_pic')->nullable();
            $table->text('biodata')->nullable();
            $table->text('achievements')->nullable();
            $table->json('training_programs')->nullable();
            $table->enum('for_org_type', ['school', 'corporate', 'both']);
            $table->string('availability');
            $table->enum('training_mode', ['online', 'offline', 'both']);
            $table->string('signed_form_pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
