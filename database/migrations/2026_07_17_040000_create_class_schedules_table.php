<?php

use App\Models\ClassSchedule;
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
        Schema::create('class_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('section_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete();
            $table->string('day_of_week', 20)->index();
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('status', 20)->default(ClassSchedule::STATUS_ACTIVE)->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['school_year_id', 'day_of_week', 'status']);
            $table->index(['teacher_id', 'day_of_week']);
            $table->index(['section_id', 'day_of_week']);
            $table->index(['classroom_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
