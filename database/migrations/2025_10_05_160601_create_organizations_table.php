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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('org_id');
            $table->string('name');
            $table->string('rep_designation');
            $table->string('addr_line1');
            $table->string('addr_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('district');
            $table->string('pincode');
            $table->string('email')->unique();
            $table->string('mobile');
            $table->string('alt_mobile')->nullable();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
