<?php

use App\Models\Assessment;
use App\Models\GradingPeriod;
use App\Models\StudentGrade;
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
        Schema::create('grading_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_year_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->string('status', 20)->default(GradingPeriod::STATUS_ACTIVE)->index();
            $table->timestamps();

            $table->unique(['school_year_id', 'name']);
        });

        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained()->restrictOnDelete();
            $table->foreignId('grading_period_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('assessment_type', 60)->nullable();
            $table->decimal('max_score', 8, 2);
            $table->decimal('weight', 5, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default(Assessment::STATUS_DRAFT)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_grades', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->decimal('score', 8, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default(StudentGrade::STATUS_DRAFT)->index();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('grading_periods');
    }
};
