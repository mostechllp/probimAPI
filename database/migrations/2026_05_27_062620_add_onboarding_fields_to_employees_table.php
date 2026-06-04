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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('experience_level')->nullable();
            $table->text('key_skills')->nullable();
            $table->string('highest_education')->nullable();
            $table->string('currency')->nullable();
            $table->string('payment_cycle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'experience_level',
                'key_skills',
                'highest_education',
                'currency',
                'payment_cycle'
            ]);
        });
    }
};
