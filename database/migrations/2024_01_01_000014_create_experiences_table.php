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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Position details
            $table->string('title');
            $table->string('organization');
            $table->string('location')->nullable();
            
            // Duration
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            
            // Description
            $table->text('description')->nullable();
            $table->json('achievements')->nullable(); // Array of achievements
            
            // Type
            $table->enum('type', ['academic', 'professional', 'research', 'administrative'])->default('professional');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
