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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            
            // Alert content
            $table->string('subject');
            $table->text('message');
            
            // Channels
            $table->boolean('send_email')->default(true);
            $table->boolean('send_whatsapp')->default(false);
            
            // Recipients
            $table->enum('recipient_type', ['all', 'organizers', 'presenters', 'participants', 'custom', 'department'])->default('all');
            $table->json('custom_recipients')->nullable(); // Array of user IDs
            
            // Scheduling
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            $table->text('error_message')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
