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
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('project_manager_id')->nullable();
            $table->unsignedBigInteger('team_lead_id')->nullable();
            
            // Assuming they point to the employees table
            $table->foreign('project_manager_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('team_lead_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_manager_id']);
            $table->dropForeign(['team_lead_id']);
            $table->dropColumn(['project_manager_id', 'team_lead_id']);
        });
    }
};
