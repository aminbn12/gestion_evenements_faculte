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
        // Rooms table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('prof_capacity');
            $table->integer('resident_capacity');
            $table->timestamps();
        });

        // Professors table
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->enum('rank', ['Pr', 'Dr']);
            $table->string('responsible_promo')->nullable();
            $table->timestamps();
        });

        // Residents table
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->integer('level'); // 1, 2, 3, 4
            $table->string('specialty');
            $table->timestamps();
        });

        // Exams table
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->integer('duration'); // minutes
            $table->string('promo'); // 1AP, 2AP, etc.
            $table->string('subject');
            $table->timestamps();
            
            $table->index('date');
            $table->index('promo');
        });

        // Exam assignments table
        Schema::create('exam_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('exam_id');
        });

        // Professor assignments (pivot table)
        Schema::create('professor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_assignment_id')->constrained('exam_assignments')->onDelete('cascade');
            $table->timestamps();
        });

        // Resident assignments (pivot table)
        Schema::create('resident_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_assignment_id')->constrained('exam_assignments')->onDelete('cascade');
            $table->timestamps();
        });

        // Absences table
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('professor_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('resident_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
        Schema::dropIfExists('resident_assignments');
        Schema::dropIfExists('professor_assignments');
        Schema::dropIfExists('exam_assignments');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('residents');
        Schema::dropIfExists('professors');
        Schema::dropIfExists('rooms');
    }
};
