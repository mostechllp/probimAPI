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
        Schema::create('offboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offboarding_id');
            $table->foreign('offboarding_id')->references('id')->on('offboardings')->cascadeOnDelete();
            $table->string('category'); // e.g., visa_cancellation, hr_admin, pro_government, finance_it
            $table->string('task_name');
            $table->enum('status', ['pending', 'completed', 'not_applicable'])->default('pending');
            $table->string('responsible_role')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offboarding_checklists');
    }
};
