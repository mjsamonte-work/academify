<?php

use App\Models\SchoolYear;
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
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_active')->default(false)->index();
            $table->string('status')->default(SchoolYear::STATUS_ACTIVE)->index();
            $table->timestamps();
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default(SchoolYear::STATUS_ACTIVE)->index();
            $table->timestamps();
            $table->unique(['school_year_id', 'name']);
        });

        Schema::create('grade_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default(SchoolYear::STATUS_ACTIVE)->index();
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_level_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->string('status')->default(SchoolYear::STATUS_ACTIVE)->index();
            $table->timestamps();
            $table->unique(['grade_level_id', 'name']);
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default(SchoolYear::STATUS_ACTIVE)->index();
            $table->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default(SchoolYear::STATUS_ACTIVE)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('grade_levels');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('school_years');
    }
};
