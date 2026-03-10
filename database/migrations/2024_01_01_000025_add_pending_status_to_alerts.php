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
        // Drop and recreate status enum to include 'pending'
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
        });
    }
};
