<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('user_id');
            $table->string('employee_id');
            $table->date('dob')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('gender')->nullable();
            $table->text('special_days')->nullable();

            // Passport Details
            $table->string('passport_full_name')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_issued_from')->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->text('address')->nullable();

            // Document Paths (Passport)
            $table->string('passport_1st_page')->nullable();
            $table->string('passport_2nd_page')->nullable();
            $table->string('passport_outer_page')->nullable();
            $table->string('passport_id_page')->nullable();

            // Visa Details
            $table->string('visa_number')->nullable();
            $table->date('visa_issued_date')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->string('visa_page')->nullable();

            // Labor Details
            $table->string('labor_number')->nullable();
            $table->date('labor_issued_date')->nullable();
            $table->date('labor_expiry_date')->nullable();
            $table->string('labor_card')->nullable();

            // EID Details
            $table->string('eid_number')->nullable();
            $table->date('eid_issued_date')->nullable();
            $table->date('eid_expiry_date')->nullable();
            $table->string('eid_1st_page')->nullable();
            $table->string('eid_2nd_page')->nullable();

            // Other Details
            $table->string('moh_license_number')->nullable();
            $table->string('dependents')->nullable(); // Yes/No
            $table->string('educational_1st_page')->nullable();
            $table->string('educational_2nd_page')->nullable();
            $table->string('company_mobile_number')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('other_number')->nullable();
            $table->string('home_country_number')->nullable();
            $table->string('company_email');
            $table->string('personal_email');
            $table->string('home_country_id_proof')->nullable();
            $table->integer('total_leaves_allocated')->default(null);
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
