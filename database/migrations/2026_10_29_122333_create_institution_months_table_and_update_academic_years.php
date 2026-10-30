<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Institution Months
        |--------------------------------------------------------------------------
        | প্রতিটি প্রতিষ্ঠানের মাসের নাম আলাদাভাবে সংরক্ষণ করা হবে।
        */
        Schema::create('institution_months', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institution_id')
                ->constrained('madrasas')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('month_number');

            $table->string('name', 100);
            $table->string('name_bn', 100)->nullable();
            $table->string('name_ar', 100)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // একই প্রতিষ্ঠানে একই ক্রমিক মাস দুবার থাকবে না।
            $table->unique(
                ['institution_id', 'month_number'],
                'institution_months_institution_number_unique'
            );

            $table->index(
                ['institution_id', 'is_active'],
                'institution_months_institution_active_index'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Update Academic Years
        |--------------------------------------------------------------------------
        | বিদ্যমান শিক্ষাবর্ষের নাম ও শুরুর মাসের জন্য নতুন কলাম।
        */
        Schema::table('academic_years', function (Blueprint $table) {
            $table->string('name_ar', 100)
                ->nullable()
                ->after('name_bn');

            $table->foreignId('start_month_id')
                ->nullable()
                ->after('end_date')
                ->constrained('institution_months')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropForeign(['start_month_id']);

            $table->dropColumn([
                'start_month_id',
                'name_ar',
            ]);
        });

        Schema::dropIfExists('institution_months');
    }
};