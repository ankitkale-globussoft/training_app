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
        Schema::table('team_members', function (Blueprint $table) {
            // First, drop the old columns
            $table->dropColumn(['block1', 'block2', 'block3']);

            // Then, add the new columns
            $table->string('block1_value')->nullable();
            $table->text('block1_desc')->nullable();

            $table->string('block2_value')->nullable();
            $table->text('block2_desc')->nullable();

            $table->string('block3_value')->nullable();
            $table->text('block3_desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'block1_value', 'block1_desc',
                'block2_value', 'block2_desc',
                'block3_value', 'block3_desc'
            ]);

            // Recreate old columns
            $table->text('block1')->nullable();
            $table->text('block2')->nullable();
            $table->text('block3')->nullable();
        });
    }
};
