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
        Schema::create('training_contents', function (Blueprint $table) {
            $table->id('content_id');

            $table->foreignId('booking_id')
                ->constrained('bookings', 'booking_id')
                ->onDelete('cascade');

            $table->foreignId('trainer_id')
                ->constrained('trainers', 'trainer_id')
                ->onDelete('cascade');

            $table->foreignId('module_id')
                ->nullable()
                ->constrained('content_modules', 'module_id')
                ->onDelete('set null');

            $table->enum('mode', ['online', 'offline']);

            $table->enum('content_type', [
                'video',
                'text',
                'pdf',
                'link',
                'meeting'
            ]);

            $table->string('title');
            $table->text('description')->nullable();

            $table->longText('text_content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();

            $table->boolean('is_visible_to_org')->default(true);
            $table->boolean('is_visible_to_candidates')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_contents');
    }
};
