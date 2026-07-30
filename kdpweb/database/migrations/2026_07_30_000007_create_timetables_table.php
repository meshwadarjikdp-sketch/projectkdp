<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->string('division');
            $table->string('academic_year');
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->string('day_of_week');
            $table->unsignedTinyInteger('slot_number');
            $table->timestamps();

            $table->unique(['department_id', 'semester', 'division', 'academic_year', 'day_of_week', 'slot_number', 'classroom_id'], 'timetable_slot_classroom_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
