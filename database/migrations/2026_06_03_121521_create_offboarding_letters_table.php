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
        Schema::create('offboarding_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offboarding_id');
            $table->foreign('offboarding_id')->references('id')->on('offboardings')->cascadeOnDelete();
            $table->string('letter_type'); // e.g. experience_letter, noc
            $table->string('document_path')->nullable();
            $table->string('status')->default('pending'); // pending, generated, sent
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offboarding_letters');
    }
};
