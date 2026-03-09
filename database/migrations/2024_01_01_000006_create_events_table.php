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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            // Date and time
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_all_day')->default(false);
            
            // Location
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            
            // Event details
            $table->enum('type', ['conference', 'seminar', 'workshop', 'meeting', 'ceremony', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            
            // Capacity
            $table->integer('capacity')->nullable();
            $table->integer('registered_count')->default(0);
            
            // Visibility
            $table->boolean('is_public')->default(false);
            $table->boolean('requires_registration')->default(false);
            
            // Attachments
            $table->string('featured_image')->nullable();
            $table->json('attachments')->nullable();
            
            // Reminder settings
            $table->integer('reminder_days_before')->default(1);
            $table->boolean('auto_reminder_enabled')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
