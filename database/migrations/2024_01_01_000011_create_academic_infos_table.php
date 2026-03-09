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
        Schema::create('academic_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Academic rank
            $table->enum('grade', ['assistant', 'maitre_assistant', 'maitre_conference', 'professeur', 'doctorant', 'vacataire']);
            $table->string('specialty')->nullable();
            $table->string('research_domain')->nullable();
            
            // Employment details
            $table->date('recruitment_date')->nullable();
            $table->enum('contract_type', ['permanent', 'contract', 'vacataire', 'visiting'])->default('permanent');
            $table->string('office_location')->nullable();
            $table->string('office_phone')->nullable();
            
            // Education
            $table->string('highest_degree')->nullable();
            $table->string('degree_institution')->nullable();
            $table->year('degree_year')->nullable();
            
            // Teaching
            $table->integer('teaching_hours_per_week')->default(0);
            $table->json('courses')->nullable(); // Array of course names
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_infos');
    }
};
