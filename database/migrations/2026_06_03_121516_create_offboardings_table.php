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
        Schema::create('offboardings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->enum('status', ['draft', 'pending_visa', 'pending_checklist', 'pending_assets', 'pending_interview', 'pending_settlement', 'pending_letters', 'completed'])->default('draft');
            $table->date('last_working_day')->nullable();
            $table->string('separation_type')->nullable();
            $table->integer('notice_period_days')->nullable();
            $table->date('notice_start_date')->nullable();
            $table->string('visa_sponsorship')->nullable();
            $table->string('nationality')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offboardings');
    }
};
