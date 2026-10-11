<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->string('institution_user_id', 100)->nullable();
            $table->unsignedBigInteger('role_id');
            $table->enum(
                'code_generation_type',
                [
                    'auto',
                    'manual',
                ]
            )->default('auto');
            
            // Basic Info
            $table->string('username', 100);
            $table->string('email', 100)->nullable();
            $table->string('password', 255);
            $table->string('name', 255)->nullable();
            $table->string('name_bn', 255)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('cover_photo', 255)->nullable();
            
            $table->string('phone', 20)->nullable();
            $table->string('phone2', 20)->nullable();
            $table->string('phone_owner', 50)->nullable();
   
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
            $table->enum('religion', ['islam', 'hindu', 'christian', 'buddhist', 'other'])->nullable();
            
            // Identification
            $table->string('nid', 20)->nullable();
            $table->string('birth_certificate', 50)->nullable();
            $table->string('custom_id', 100)->nullable();
            
            // Present Address
            $table->unsignedBigInteger('present_division_id')->nullable();
            $table->unsignedBigInteger('present_district_id')->nullable();
            $table->unsignedBigInteger('present_upazila_id')->nullable();
            $table->string('present_union', 100)->nullable();
            $table->string('present_post_office', 100)->nullable();
            $table->string('present_village_road', 255)->nullable();
            $table->string('present_postal_code', 20)->nullable();
            $table->text('present_address_full')->nullable();
            
            // Permanent Address
            $table->unsignedBigInteger('permanent_division_id')->nullable();
            $table->unsignedBigInteger('permanent_district_id')->nullable();
            $table->unsignedBigInteger('permanent_upazila_id')->nullable();
            $table->string('permanent_union', 100)->nullable();
            $table->string('permanent_post_office', 100)->nullable();
            $table->string('permanent_village_road', 255)->nullable();
            $table->string('permanent_postal_code', 20)->nullable();
            $table->text('permanent_address_full')->nullable();
            
            // Parents Info
            $table->string('father_name', 255)->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->string('guardian_name', 255)->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->string('guardian_relation', 50)->nullable();
            
            // Status & Meta
            $table->tinyInteger('status')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Unique Constraints
            $table->unique(['username']);
            $table->unique(['email']);
            $table->unique(['phone']);
         
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};