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
        Schema::create('employee_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->unsignedBigInteger('offboarding_id')->nullable();
            $table->foreign('offboarding_id')->references('id')->on('offboardings')->nullOnDelete();
            $table->string('asset_name');
            $table->string('asset_code')->nullable();
            $table->date('issued_on')->nullable();
            $table->string('status')->default('Issued'); // Issued, Returned, Pending Return
            $table->string('condition')->nullable(); // Good, Damaged
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_assets');
    }
};
