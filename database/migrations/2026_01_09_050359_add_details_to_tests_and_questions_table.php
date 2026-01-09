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
        Schema::table('tests', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->integer('total_marks')->default(0);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->integer('marks')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['title', 'total_marks']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('marks');
        });
    }
};
