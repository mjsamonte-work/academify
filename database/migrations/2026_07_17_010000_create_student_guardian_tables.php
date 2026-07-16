<?php

use App\Models\Student;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('preferred_name')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('grade_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default(Student::STATUS_ACTIVE)->index();
            $table->timestamps();
        });

        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('status')->default(Student::STATUS_ACTIVE)->index();
            $table->timestamps();
        });

        Schema::create('guardian_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->string('relationship');
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_pick_up')->default(false);
            $table->boolean('receives_notifications')->default(true);
            $table->timestamps();
            $table->unique(['student_id', 'guardian_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_student');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('students');
    }
};
