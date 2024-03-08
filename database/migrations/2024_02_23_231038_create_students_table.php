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
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('advisor_id');
            $table->string('student_name');
            $table->tinyInteger('student_semester');
            $table->timestamps();

            // // Define FK
            // $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('cascade');
            // $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            // $table->foreign('advisor_id')->references('advisor_id')->on('advisors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
