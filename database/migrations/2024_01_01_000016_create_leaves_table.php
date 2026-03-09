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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Leave details
            $table->enum('type', ['annual', 'sick', 'maternity', 'paternity', 'study', 'unpaid', 'other'])->default('annual');
            $table->text('reason')->nullable();
            
            // Duration
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');
            $table->boolean('is_half_day')->default(false);
            
            // Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->dateTime('approved_at')->nullable();
            
            // Attachments
            $table->json('attachments')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
