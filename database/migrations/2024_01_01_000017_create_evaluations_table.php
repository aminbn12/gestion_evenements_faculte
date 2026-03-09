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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            
            // Evaluation period
            $table->string('period'); // e.g., "2024-Q1", "2024-Annual"
            $table->year('year');
            
            // Criteria scores (1-5 scale)
            $table->integer('teaching_score')->default(0);
            $table->integer('research_score')->default(0);
            $table->integer('service_score')->default(0);
            $table->integer('collaboration_score')->default(0);
            $table->integer('communication_score')->default(0);
            $table->integer('initiative_score')->default(0);
            
            // Overall
            $table->decimal('overall_score', 3, 2)->default(0);
            
            // Comments
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals')->nullable();
            $table->text('evaluator_comments')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'acknowledged'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('acknowledged_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
