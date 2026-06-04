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
        Schema::create('offboarding_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offboarding_id');
            $table->foreign('offboarding_id')->references('id')->on('offboardings')->cascadeOnDelete();
            $table->decimal('total_payable', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('net_payable', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, processed, paid
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offboarding_settlements');
    }
};
