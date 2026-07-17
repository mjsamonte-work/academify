<?php

use App\Models\Enrollment;
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
        Schema::create('enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default(Enrollment::STATUS_PENDING)->index();
            $table->date('enrolled_at')->nullable();
            $table->date('withdrawn_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'school_year_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
