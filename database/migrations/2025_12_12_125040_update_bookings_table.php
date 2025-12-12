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
        Schema::table('bookings', function (Blueprint $table) {

            // 1. Add new foreign key
            $table->foreignId('requirement_id')
                ->after('booking_id')
                ->constrained('training_requirements', 'requirement_id')
                ->onDelete('cascade');

            // 2. Replace old statuses with new booking_status
            $table->enum('booking_status', [
                'assigned',
                'in_progress',
                'completed',
                'canceled'
            ])->default('assigned')->after('trainer_id');

            // 3. Add amount column
            $table->decimal('amount', 10, 2)->nullable()->after('booking_status');

            // 4. Drop old status columns
            $table->dropColumn(['trainer_status', 'training_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            // rollback steps

            $table->dropForeign(['requirement_id']);
            $table->dropColumn('requirement_id');

            $table->dropColumn('booking_status');
            $table->dropColumn('amount');

            $table->enum('trainer_status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->enum('training_status', ['scheduled', 'completed', 'canceled'])->default('scheduled');
        });
    }
};
