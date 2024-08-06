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
        Schema::create('student_courses', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id');
            $table->string('student_id_number');
            $table->unsignedBigInteger('course_id');
            $table->tinyInteger('course_semester_taken');
            $table->decimal('grade')->nullable();
            $table->enum('status', ['finished', 'taken', 'enrolled'])->nullable();
            /**
             * finished => course already graded
             * taken => course already taken for current period BUT has not been accepted yet by the advisor
             * enrolled => current period study plan or taken courses has been accepted by the advisor
             */
            $table->timestamps();

            // Define PK
            $table->primary(['student_id', 'course_id']);

            // Define FK
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_student');
    }
};
