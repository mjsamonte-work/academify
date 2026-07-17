<?php

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
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
        Schema::create('attendance_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained()->restrictOnDelete();
            $table->date('attendance_date');
            $table->string('status', 20)->default(AttendanceSession::STATUS_DRAFT)->index();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['class_schedule_id', 'attendance_date']);
        });

        Schema::create('attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default(AttendanceRecord::STATUS_PRESENT)->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['attendance_session_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
    }
};
