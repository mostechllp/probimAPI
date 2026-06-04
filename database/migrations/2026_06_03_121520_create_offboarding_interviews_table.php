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
        Schema::create('offboarding_interviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offboarding_id');
            $table->foreign('offboarding_id')->references('id')->on('offboardings')->cascadeOnDelete();
            $table->string('interviewer')->nullable();
            $table->date('interview_date')->nullable();
            $table->string('interview_mode')->nullable();
            $table->string('overall_satisfaction')->nullable();
            $table->string('primary_reason')->nullable();
            $table->string('work_life_rating')->nullable();
            $table->string('manager_relationship_rating')->nullable();
            $table->text('enjoyed_most')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->boolean('would_recommend')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offboarding_interviews');
    }
};
