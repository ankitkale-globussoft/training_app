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
        Schema::table('training_requirements', function (Blueprint $table) {
            $table->date('schedule_date')->nullable()->after('location');
            $table->string('schedule_time')->nullable()->after('schedule_date');
            $table->integer('number_of_students')->default(1)->after('schedule_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_requirements', function (Blueprint $table) {
            $table->dropColumn(['schedule_date', 'schedule_time', 'number_of_students']);
        });
    }
};
