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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('questions_per_exam')->default(10);
            $table->integer('timer_minutes')->nullable();
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('allow_review')->default(true);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
