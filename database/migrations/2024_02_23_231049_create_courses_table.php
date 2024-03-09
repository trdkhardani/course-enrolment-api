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
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->unsignedBigInteger('department_id');
            $table->string('course_name');
            $table->char('course_code')->unique();
            $table->char('course_class');
            $table->tinyInteger('course_capacity');
            $table->tinyInteger('course_credits');
            $table->boolean('course_is_open')->default(0);
            /**
             * open => the class is opened for the current period
             * closed => the class is closed for the current period
             */
            $table->timestamps();

            // Define FK
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
